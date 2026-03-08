<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Position;
use App\Models\RiskToReward;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
    }

    /**
     * Extract trading data from a chart image.
     * Returns array with: entry, sl, tp (integers), position_id, market_id, risk_to_reward_id
     */
    public function extractPricesFromChart(string $filePath): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('GeminiService: GEMINI_API_KEY not set.');
            return null;
        }

        // Accept either absolute path or public-disk-relative path
        $fullPath = file_exists($filePath) ? $filePath : Storage::disk('public')->path($filePath);
        if (!file_exists($fullPath)) {
            Log::warning('GeminiService: File not found', ['path' => $fullPath]);
            return null;
        }

        $imageData = base64_encode(file_get_contents($fullPath));
        $mimeType = mime_content_type($fullPath) ?: 'image/png';

        $availableMarkets = Market::pluck('name')->implode(', ');

        $prompt = <<<PROMPT
This is a trading chart image from TradingView. The user has drawn a RED annotation on the chart.

**YOUR #1 TASK — READ THE RED PRICE TAG:**
- Find the RED price tag label on the chart. It will be accompanied by a red text label saying "HARGA ENTRY" (or similar Indonesian text meaning "Entry Price").
- The red price tag shows the exact entry price. Read it precisely.
- The price format on this chart uses INDONESIAN number formatting:
  - The DOT (.) is a THOUSANDS separator (like a comma in English)
  - The COMMA (,) is the DECIMAL separator (like a dot in English)
  - Example: "5.122,154" means 5122.154 (five thousand one hundred twenty-two point one five four)
  - Example: "2.608,010" means 2608.010
- Convert the price to standard decimal format (e.g., 5122.154, 2608.01)

**Extract:**
1. **entry**: The price shown in the RED price tag near the "HARGA ENTRY" label. Return as standard decimal number (e.g. 5122.154, NOT "5.122,154" string format)
2. **symbol**: The trading symbol in the top-left chart header (e.g. "XAUUSD", "GBPUSD", "EURUSD"). Match to one of: {$availableMarkets}
3. **position**: "BUY" if this is a bullish/long trade setup (price going up), "SELL" if bearish/short (price going down). Determine from context: zone labels, arrow direction, or candle position relative to entry.
4. **reason**: Tulis alasan trading dalam Bahasa Indonesia, format poin-poin singkat (bullet points dengan "-"). Minimum 100 kata, maksimum 200 kata. Analisis: zona entry yang ditandai, arah harga, level support/resistance, struktur pasar. Jangan gunakan kata "Gemini" atau "AI".

Return ONLY a valid JSON object:
{"entry": 5122.154, "symbol": "XAU/USD", "position": "BUY", "reason": "- Harga berada di zona demand...\n- ..."}

Rules:
- entry must be a number (float), NOT a string with dots/commas
- For XAU/USD: entry price is always > 1000
- sl and tp will be calculated automatically — do NOT include them
- If you cannot find the red price tag, use null for entry
PROMPT;

        try {
            $response = Http::timeout(30)->post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData,
                                ],
                            ],
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0,
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('GeminiService: API error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $content = $response->json('candidates.0.content.parts.0.text');
            if (!$content) {
                return null;
            }

            // Strip markdown code fences if present
            $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
            $content = preg_replace('/\s*```$/', '', $content);

            $data = json_decode(trim($content), true);
            if (!is_array($data)) {
                return null;
            }

            // Log raw Gemini response for debugging
            Log::info('GeminiService: raw response', ['entry' => $data['entry'] ?? null, 'symbol' => $data['symbol'] ?? null, 'position' => $data['position'] ?? null]);

            // Parse entry — handle Indonesian format "5.122,154" → 5122.154
            $rawEntry = $data['entry'] ?? null;
            if (is_string($rawEntry)) {
                // Indonesian format: dots=thousands, comma=decimal → "5.122,154" → "5122.154"
                $rawEntry = str_replace('.', '', $rawEntry); // remove thousand separators
                $rawEntry = str_replace(',', '.', $rawEntry); // convert decimal comma to dot
            }
            $entry = $rawEntry !== null ? (float) $rawEntry : null;

            // Sanity check: entry price must be realistic (> 100 for any forex/gold/crypto)
            if ($entry !== null && $entry < 100) {
                Log::warning('GeminiService: entry price too low, likely misread', ['entry' => $entry]);
                return null;
            }

            // Auto-detect position direction (from Gemini, no sl/tp from chart needed)
            $positionName = $data['position'] ?? null;
            $isBuy = strtoupper($positionName ?? 'BUY') === 'BUY';

            // === FIXED RISK MANAGEMENT: Risk=$10, RR=1:3, Lot=0.01 ===
            // Formula: Risk = SL_distance × Lot × Multiplier
            // $10 = SL_distance × 0.01 × 100  →  SL_distance = $10
            // TP_distance = SL_distance × 3 = $30
            if ($entry !== null) {
                $slDistance = 10.0;  // $10 risk
                $tpDistance = 30.0;  // $30 reward (1:3 RR)

                $sl = $isBuy ? $entry - $slDistance : $entry + $slDistance;
                $tp = $isBuy ? $entry + $tpDistance : $entry - $tpDistance;
            }

            $result = [
                'entry' => $entry !== null ? $this->toFormInteger($entry) : null,
                'sl'    => $sl    !== null ? $this->toFormInteger($sl)    : null,
                'tp'    => $tp    !== null ? $this->toFormInteger($tp)    : null,
            ];

            // Auto-detect market_id
            if (!empty($data['symbol'])) {
                $market = Market::whereRaw('LOWER(name) = ?', [strtolower($data['symbol'])])->first();
                $result['market_id'] = $market?->id;
            }

            // Position
            if ($positionName) {
                $position = Position::whereRaw('UPPER(name) = ?', [strtoupper($positionName)])->first();
                $result['position_id'] = $position?->id;
            }

            // Fixed RR = 1:3
            $rrModel = RiskToReward::where('ratio', '1:3')->first();
            $result['risk_to_reward_id'] = $rrModel?->id;

            // Fixed lot size = 0.01 (id: 1)
            $result['lot_size_id'] = \App\Models\LotSize::where('size', '0.01')->value('id');

            // Reason from Gemini analysis
            if (!empty($data['reason'])) {
                $result['reason'] = $data['reason'];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('GeminiService: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Convert decimal price (e.g. 2608.01) to form integer format (e.g. 2608010)
     * This matches the formatPrice() logic used in TransactionResource.
     */
    protected function toFormInteger(float $value): int
    {
        return (int) round($value * 1000);
    }
}
