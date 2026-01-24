<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->sku) {
                $model->sku = self::generateSequentialNumber();
            }
        });

        // Update AccountTrade balance saat hit_id di-save (mode EDIT)
        static::updating(function (self $model): void {
            // Cek apakah hit_id berubah dari null ke value (pertama kali diisi)
            if (!$model->isDirty('hit_id')) {
                return; // hit_id tidak berubah, skip
            }

            // Hanya update jika hit_id baru diisi (dari null ke value)
            $originalHitId = $model->getOriginal('hit_id');
            if ($originalHitId !== null) {
                return; // hit_id sudah pernah diisi sebelumnya, skip
            }

            if (!$model->hit_id || !$model->account_trade_id) {
                return;
            }

            $hit = Hit::find($model->hit_id);
            
            // PENTING: Fresh query untuk mendapatkan balance terbaru dari database
            $accountTrade = AccountTrade::find($model->account_trade_id);
            
            if (!$hit || !$accountTrade) {
                return;
            }

            // Refresh untuk memastikan data terbaru
            $accountTrade->refresh();

            $hitName = strtoupper(trim($hit->name));

            // Parse profit dan rugi (hapus +$ dan -$)
            $profitValue = floatval(str_replace(['+', '$', ' '], '', $model->profit ?? '0'));
            $rugiValue = floatval(str_replace(['-', '$', ' '], '', $model->rugi ?? '0'));

            // Parse current balance (hapus $ jika ada)
            $currentBalanceString = $accountTrade->balance ?? '$0.00';
            $currentBalance = floatval(str_replace(['$', ' ', ','], '', $currentBalanceString));
            
            // Debug log (optional - bisa dihapus nanti)
            \Log::info('Transaction Update Balance', [
                'transaction_id' => $model->id,
                'account_trade_id' => $model->account_trade_id,
                'hit' => $hitName,
                'current_balance_raw' => $currentBalanceString,
                'current_balance_parsed' => $currentBalance,
                'profit_value' => $profitValue,
                'rugi_value' => $rugiValue,
            ]);

            $newBalance = $currentBalance;
            $balanceChange = 0;

            // WIN (TP) = tambah profit
            if ($hitName === 'TP' || $hitName === 'WIN') {
                $newBalance = $currentBalance + $profitValue;
                $balanceChange = $profitValue;
                $model->account_balance_change = '+$' . number_format($balanceChange, 2, '.', '');
            }
            // LOSE (SL) = kurangi rugi (rugiValue sudah positif dari parsing)
            elseif ($hitName === 'SL' || $hitName === 'LOSE') {
                $newBalance = $currentBalance - $rugiValue;
                $balanceChange = -$rugiValue;
                $model->account_balance_change = '-$' . number_format(abs($balanceChange), 2, '.', '');
            }

            // Update balance dengan format $X.XX (normalisasi dengan 2 decimal)
            $formattedBalance = '$' . number_format($newBalance, 2, '.', '');
            
            // Debug log (optional - bisa dihapus nanti)
            \Log::info('Transaction Update Balance Result', [
                'new_balance' => $newBalance,
                'formatted_balance' => $formattedBalance,
                'balance_change' => $model->account_balance_change,
            ]);
            
            $accountTrade->balance = $formattedBalance;
            $accountTrade->saveQuietly(); // Use saveQuietly to avoid infinite loop
        });
    }

    /**
     * Generate sequential SKU number
     * Format: TRX-0001, TRX-0002, etc.
     */
    protected static function generateSequentialNumber(): string
    {
        // Get the last transaction SKU
        $lastTransaction = self::withTrashed()
            ->whereNotNull('sku')
            ->where('sku', 'like', 'TRX-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTransaction) {
            // First transaction
            return 'TRX-0001';
        }

        // Extract number from last SKU (TRX-0001 -> 0001)
        $lastNumber = (int) str_replace('TRX-', '', $lastTransaction->sku);
        
        // Increment and format with leading zeros (4 digits)
        $newNumber = $lastNumber + 1;
        
        return 'TRX-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }



    protected function casts(): array
    {
        return [
            'sku' => 'string',
            'id' => 'integer',
            'account_trade_id' => 'integer',
            'position_id' => 'integer',
            'market_id' => 'integer',
            'risk_to_reward_id' => 'integer',
            'lot_size_id' => 'integer',
            'hit_id' => 'integer',
            'harga_entry' => 'string',
            'harga_sl' => 'string',
            'harga_tp' => 'string',
            'profit' => 'string',
            'rugi' => 'string',
            'account_balance_change' => 'string',
        ];
    }

    public function accountTrade(): BelongsTo
    {
        return $this->belongsTo(AccountTrade::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function riskToReward(): BelongsTo
    {
        return $this->belongsTo(RiskToReward::class);
    }

    public function lotSize(): BelongsTo
    {
        return $this->belongsTo(LotSize::class);
    }

    public function hit(): BelongsTo
    {
        return $this->belongsTo(Hit::class);
    }
}
