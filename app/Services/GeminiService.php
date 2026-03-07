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
This is a trading chart image (e.g. TradingView). Analyze it carefully.

Extract the following information:

1. **entry**: The entry price label (middle price between TP and SL zones)
2. **sl**: The Stop Loss price (in the red/pink zone)
3. **tp**: The Take Profit price (in the green zone)
4. **symbol**: The trading symbol shown in the chart header (e.g. "XAUUSD", "GBPUSD", "EURUSD", "BTCUSD"). Match to one of: {$availableMarkets}
5. **position**: "BUY" if TP is above entry (long trade), "SELL" if TP is below entry (short trade)
6. **reason**: Tulis alasan trading dalam Bahasa Indonesia, format poin-poin singkat (bullet points dengan "-"). Minimum 100 kata, maksimum 200 kata. Analisis berdasarkan gambar: zona entry, arah harga, level support/resistance yang terlihat, pola candlestick, dan risk/reward. Jangan gunakan kata "Gemini" atau "AI".

Return ONLY a valid JSON object, no explanation:
{"entry": 2608.01, "sl": 2602.35, "tp": 2631.42, "symbol": "XAU/USD", "position": "BUY", "reason": "- Harga berada di zona support...\n- ..."}

Rules:
- For symbol: normalize format to include "/" (e.g. XAUUSD → XAU/USD, GBPUSD → GBP/USD)
- If you cannot determine a value, use null for that key
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

            $entry = isset($data['entry']) ? (float) $data['entry'] : null;
            $sl    = isset($data['sl'])    ? (float) $data['sl']    : null;
            $tp    = isset($data['tp'])    ? (float) $data['tp']    : null;

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

            // Auto-detect position_id from data or prices
            $positionName = $data['position'] ?? null;
            if (!$positionName && $entry !== null && $tp !== null) {
                $positionName = $tp > $entry ? 'BUY' : 'SELL';
            }
            if ($positionName) {
                $position = Position::whereRaw('UPPER(name) = ?', [strtoupper($positionName)])->first();
                $result['position_id'] = $position?->id;
            }

            // Auto-calculate risk_to_reward_id
            if ($entry !== null && $sl !== null && $tp !== null && abs($entry - $sl) > 0) {
                $rr = round(abs($tp - $entry) / abs($entry - $sl));
                $rrRatio = "1:{$rr}";
                $rrModel = RiskToReward::where('ratio', $rrRatio)->first();
                $result['risk_to_reward_id'] = $rrModel?->id;
            }

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
