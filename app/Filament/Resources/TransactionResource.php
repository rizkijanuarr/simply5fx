<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TransactionResource extends Resource
{
    use \App\Traits\HasNavigationBadge;

    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Manajemen Transaksi';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    /**
     * Format harga dari 2879601 menjadi 2879.601
     */
    protected static function formatPrice($value): float
    {
        if (empty($value)) {
            return 0.0;
        }

        $str = (string) $value;
        if (strlen($str) >= 4) {
            $beforeDecimal = substr($str, 0, -3);
            $afterDecimal = substr($str, -3);
            return floatval("{$beforeDecimal}.{$afterDecimal}");
        }
        return floatval($value);
    }

    /**
     * Calculate Profit & Loss untuk CREATE mode
     * Dynamic calculation berdasarkan market.multiplier dari database
     */
    protected static function calculateProfitLossForCreate(callable $set, callable $get): void
    {
        $entry = $get('harga_entry');
        $tp = $get('harga_tp');
        $sl = $get('harga_sl');
        $lotSizeId = $get('lot_size_id');
        $marketId = $get('market_id');

        if (!$entry || !$tp || !$sl || !$lotSizeId || !$marketId) {
            return;
        }

        // Format harga (2863673 → 2863.673)
        $entryPrice = static::formatPrice($entry);
        $tpPrice = static::formatPrice($tp);
        $slPrice = static::formatPrice($sl);

        // Get lot size
        $lotSize = \App\Models\LotSize::find($lotSizeId);
        if (!$lotSize) {
            return;
        }

        // Get market untuk ambil multiplier dari database
        $market = \App\Models\Market::find($marketId);
        if (!$market) {
            return;
        }

        $lot = floatval($lotSize->size);
        $multiplier = floatval($market->multiplier ?? 100); // Default 100 jika null

        // Calculate profit & loss
        // Profit = |TP - Entry| × Lot × Multiplier (dari database)
        // Loss = |Entry - SL| × Lot × Multiplier (dari database)
        $profitValue = abs($tpPrice - $entryPrice) * $lot * $multiplier;
        $lossValue = abs($entryPrice - $slPrice) * $lot * $multiplier;

        // Set profit dan rugi dengan format +$X.XX dan -$X.XX
        $set('profit', '+$' . number_format($profitValue, 2, '.', ''));
        $set('rugi', '-$' . number_format($lossValue, 2, '.', ''));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(function ($record) {
                $isEditing = $record !== null;

                return [
                    // Fields for CREATE mode (disabled in EDIT mode)
                    Forms\Components\Select::make('account_trade_id')
                        ->relationship('accountTrade', 'name')
                        ->required()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if ($state) {
                                // Di CREATE mode, tampilkan current balance di field Balance Change
                                if (!$isEditing) {
                                    $account = \App\Models\AccountTrade::find($state);
                                    if ($account) {
                                        $set('account_balance_change', $account->balance);
                                    }
                                }
                                
                                // Trigger calculation di CREATE mode
                                if (!$isEditing) {
                                    static::calculateProfitLossForCreate($set, $get);
                                }
                            }
                        }),

                    // Transaction Date - Always visible
                    Forms\Components\DateTimePicker::make('transaction_date')
                        ->label('Transaction Date & Time')
                        ->seconds(false)
                        ->default(now())
                        ->disabled($isEditing)
                        ->required(),

                    Forms\Components\Select::make('position_id')
                        ->relationship('position', 'name')
                        ->required()
                        ->disabled($isEditing)
                        ->dehydrated(),

                    Forms\Components\Select::make('market_id')
                        ->relationship('market', 'name')
                        ->required()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if (!$isEditing) {
                                static::calculateProfitLossForCreate($set, $get);
                            }
                        }),

                    Forms\Components\Select::make('risk_to_reward_id')
                        ->relationship('riskToReward', 'ratio')
                        ->required()
                        ->disabled($isEditing)
                        ->dehydrated(),

                    Forms\Components\Select::make('lot_size_id')
                        ->relationship('lotSize', 'size')
                        ->required()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if (!$isEditing) {
                                static::calculateProfitLossForCreate($set, $get);
                            }
                        }),

                    Forms\Components\TextInput::make('harga_entry')
                        ->required()
                        ->numeric()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if (!$isEditing) {
                                static::calculateProfitLossForCreate($set, $get);
                            }
                        }),

                    Forms\Components\TextInput::make('harga_sl')
                        ->required()
                        ->numeric()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if (!$isEditing) {
                                static::calculateProfitLossForCreate($set, $get);
                            }
                        }),

                    Forms\Components\TextInput::make('harga_tp')
                        ->required()
                        ->numeric()
                        ->disabled($isEditing)
                        ->dehydrated()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) use ($isEditing) {
                            if (!$isEditing) {
                                static::calculateProfitLossForCreate($set, $get);
                            }
                        }),

                    Forms\Components\FileUpload::make('screenshot_before')
                        ->image()
                        ->disk('public')
                        ->directory('images/screenshot_before')
                        ->disabled($isEditing)
                        ->dehydrated(),

                    // Fields for EDIT mode only
                    Forms\Components\FileUpload::make('screenshot_after')
                        ->image()
                        ->disk('public')
                        ->directory('images/screenshot_after')
                        ->visible($isEditing),

                    // Profit - Auto Calculated di CREATE, Disabled di EDIT
                    Forms\Components\TextInput::make('profit')
                        ->label('Profit')
                        ->disabled()
                        ->dehydrated()
                        ->helperText($isEditing ? 'Calculated from CREATE' : 'Auto-calculated from TP - Entry × Lot × Multiplier'),

                    // Rugi - Auto Calculated di CREATE, Disabled di EDIT
                    Forms\Components\TextInput::make('rugi')
                        ->label('Rugi')
                        ->disabled()
                        ->dehydrated()
                        ->helperText($isEditing ? 'Calculated from CREATE' : 'Auto-calculated from Entry - SL × Lot × Multiplier'),

                    // Hit - Only visible in EDIT mode
                    Forms\Components\Select::make('hit_id')
                        ->label('Hit (TP/SL)')
                        ->relationship('hit', 'name')
                        ->visible($isEditing)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (!$state) {
                                // Jika HIT di-clear, kembalikan ke balance asli
                                $accountTradeId = $get('account_trade_id');
                                if ($accountTradeId) {
                                    $account = \App\Models\AccountTrade::find($accountTradeId);
                                    $set('account_balance_change', $account?->balance ?? '-');
                                }
                                return;
                            }

                            // Get current account balance
                            $accountTradeId = $get('account_trade_id');
                            if (!$accountTradeId) {
                                return;
                            }

                            $account = \App\Models\AccountTrade::find($accountTradeId);
                            if (!$account) {
                                return;
                            }

                            // Parse current balance
                            $currentBalance = floatval(str_replace(['$', ' ', ','], '', $account->balance ?? '0'));

                            // Get hit name
                            $hit = \App\Models\Hit::find($state);
                            if (!$hit) {
                                return;
                            }

                            $hitName = strtoupper(trim($hit->name));
                            $profit = $get('profit');
                            $rugi = $get('rugi');

                            // Parse profit dan rugi
                            $profitValue = floatval(str_replace(['+', '$', ' '], '', $profit ?? '0'));
                            $rugiValue = floatval(str_replace(['-', '$', ' '], '', $rugi ?? '0'));

                            // Calculate new balance based on HIT
                            $newBalance = $currentBalance;
                            if (in_array($hitName, ['WIN', 'TP'])) {
                                $newBalance = $currentBalance + $profitValue;
                            } elseif (in_array($hitName, ['LOSE', 'SL'])) {
                                $newBalance = $currentBalance - $rugiValue;
                            }

                            // Set new balance
                            $set('account_balance_change', '$' . number_format($newBalance, 2, '.', ''));
                        }),

                    // Account Balance Change - Always visible, disabled
                    Forms\Components\TextInput::make('account_balance_change')
                        ->label('Balance Change')
                        ->disabled()
                        ->dehydrated()
                        ->default(function ($record) use ($isEditing) {
                            // Di EDIT mode, jika sudah ada value, tampilkan
                            if ($isEditing && $record && $record->account_balance_change) {
                                return $record->account_balance_change;
                            }
                            // Di EDIT mode tapi belum ada HIT, tampilkan current balance
                            if ($isEditing && $record && $record->accountTrade) {
                                return $record->accountTrade->balance;
                            }
                            return null;
                        })
                        ->helperText($isEditing ? 'New balance after WIN/LOSE' : 'Will be calculated when HIT is set'),

                    // Reason - Always editable
                    Forms\Components\Textarea::make('reason')
                        ->columnSpanFull(),
                ];
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('accountTrade.name')
                    ->label('Informasi Transaksi')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $badges = array_filter([
                            $record->position?->name ? ['label' => $record->position->name, 'color' => 'blue'] : null,
                            $record->market?->name ? ['label' => $record->market->name, 'color' => 'green'] : null,
                            $record->lotSize?->size ? ['label' => 'Lot: ' . $record->lotSize->size, 'color' => 'gray'] : null,
                        ]);

                        $colors = [
                            'blue' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 ring-blue-500/20',
                            'green' => 'bg-green-500/10 text-green-600 dark:text-green-400 ring-green-500/20',
                            'gray' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400 ring-gray-500/20',
                        ];

                        $badgeHtml = '';
                        foreach ($badges as $badge) {
                            $colorClass = $colors[$badge['color']] ?? $colors['gray'];
                            $badgeHtml .= "<span class='inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {$colorClass}'>{$badge['label']}</span> ";
                        }

                        $images = '';
                        if ($record->screenshot_before || $record->screenshot_after) {
                            $beforeImg = $record->screenshot_before
                                ? "<img src='".asset('storage/'.$record->screenshot_before)."' class='w-10 h-10 rounded object-cover ring-1 ring-gray-300 dark:ring-gray-600' alt='Before' />"
                                : '';
                            $afterImg = $record->screenshot_after
                                ? "<img src='".asset('storage/'.$record->screenshot_after)."' class='w-10 h-10 rounded object-cover ring-1 ring-gray-300 dark:ring-gray-600' alt='After' />"
                                : '';
                            $images = "<div class='flex gap-2'>{$beforeImg}{$afterImg}</div>";
                        }

                        $reason = $record->reason
                            ? "<div class='pl-2 border-l-2 border-amber-400'><p class='text-[10px] text-gray-600 dark:text-gray-400 italic'>" . Str::limit($record->reason, 100) . "</p></div>"
                            : '';

                        return "
                            <div class='space-y-1.5 py-1'>
                                <div class='text-xs font-medium text-gray-600 dark:text-gray-400'>
                                    {$record->sku} • {$record->accountTrade?->name} • {$record->transaction_date}
                                </div>
                                <div class='flex flex-wrap gap-1'>{$badgeHtml}</div>
                                <div class='flex gap-3 text-[11px]'>
                                    <span><span class='text-gray-500 dark:text-gray-400'>E:</span> <span class='font-semibold text-gray-900 dark:text-gray-100'>{$record->harga_entry}</span></span>
                                    <span><span class='text-gray-500 dark:text-gray-400'>SL:</span> <span class='font-semibold text-red-600 dark:text-red-400'>{$record->harga_sl}</span></span>
                                    <span><span class='text-gray-500 dark:text-gray-400'>TP:</span> <span class='font-semibold text-green-600 dark:text-green-400'>{$record->harga_tp}</span></span>
                                </div>
                                {$images}
                                {$reason}
                            </div>
                        ";
                    })
                    ->searchable(['sku']) // Tetap bisa search by SKU
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Current Balance')
                            ->using(function ($query) {
                                try {
                                    // Ambil IDs dari query yang sudah difilter
                                    $ids = $query->pluck('id');
                                    
                                    if ($ids->isEmpty()) {
                                        return '-';
                                    }
                                    
                                    // Query ulang menggunakan Eloquent untuk dapat relasi
                                    $transactions = Transaction::whereIn('id', $ids)
                                        ->with('accountTrade')
                                        ->get();

                                    // Ambil unique account_trade_id
                                    $accountIds = $transactions->pluck('account_trade_id')->unique()->filter();
                                    
                                    if ($accountIds->isEmpty()) {
                                        return '-';
                                    }
                                    
                                    // Jika hanya 1 akun, tampilkan balance-nya
                                    if ($accountIds->count() === 1) {
                                        $account = \App\Models\AccountTrade::find($accountIds->first());
                                        return $account ? $account->balance : '-';
                                    }
                                    
                                    // Jika lebih dari 1 akun, tampilkan total
                                    $totalBalance = 0;
                                    foreach ($accountIds as $accountId) {
                                        $account = \App\Models\AccountTrade::find($accountId);
                                        if ($account) {
                                            $balance = floatval(str_replace(['$', ' ', ','], '', $account->balance ?? '0'));
                                            $totalBalance += $balance;
                                        }
                                    }
                                    
                                    return '$' . number_format($totalBalance, 2, '.', '');
                                } catch (\Exception $e) {
                                    \Log::error('Summary Account Balance Error: ' . $e->getMessage());
                                    return 'Error';
                                }
                            }),
                    ]),

                Tables\Columns\TextColumn::make('riskToReward.ratio')
                    ->label('R/R')
                    ->badge()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('hit.name')
                    ->label('HIT')
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->hit) {
                            return 'gray';
                        }
                        $hitName = strtoupper(trim($record->hit->name));
                        if (in_array($hitName, ['WIN', 'TP'])) {
                            return 'success';
                        }
                        if (in_array($hitName, ['LOSE', 'SL'])) {
                            return 'danger';
                        }
                        return 'gray';
                    })
                    ->sortable()
                    ->searchable()
                    ->default('-')
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Win/Lose')
                            ->using(function () {
                                // Query langsung semua transactions dengan HIT
                                $transactions = Transaction::whereNotNull('hit_id')
                                    ->with('hit')
                                    ->get();

                                if ($transactions->isEmpty()) {
                                    return '0W / 0L';
                                }

                                $totalWin = 0;
                                $totalLose = 0;

                                foreach ($transactions as $transaction) {
                                    if (!$transaction->hit) {
                                        continue;
                                    }
                                    
                                    $hitName = strtoupper(trim($transaction->hit->name));
                                    
                                    if (in_array($hitName, ['WIN', 'TP'])) {
                                        $totalWin++;
                                    } elseif (in_array($hitName, ['LOSE', 'SL'])) {
                                        $totalLose++;
                                    }
                                }

                                return $totalWin . 'W / ' . $totalLose . 'L';
                            }),
                    ]),

                Tables\Columns\TextColumn::make('profit')
                    ->label('Profit')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->default('-')
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total Profit')
                            ->using(function ($query) {
                                $ids = $query->pluck('id');
                                $transactions = Transaction::whereIn('id', $ids)->with('hit')->get();

                                $totalProfit = $transactions
                                    ->filter(function ($transaction) {
                                        if (!$transaction->hit_id || !$transaction->hit) {
                                            return false;
                                        }
                                        $hitName = strtoupper(trim($transaction->hit->name ?? ''));
                                        return in_array($hitName, ['WIN', 'TP']);
                                    })
                                    ->sum(function ($transaction) {
                                        return floatval(str_replace(['+', '$', ' '], '', $transaction->profit ?? '0'));
                                    });

                                return '$' . number_format($totalProfit, 2, '.', '');
                            }),
                    ]),

                Tables\Columns\TextColumn::make('rugi')
                    ->label('Rugi')
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->default('-')
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total Loss')
                            ->using(function ($query) {
                                $ids = $query->pluck('id');
                                $transactions = Transaction::whereIn('id', $ids)->with('hit')->get();

                                $totalLoss = $transactions
                                    ->filter(function ($transaction) {
                                        if (!$transaction->hit_id || !$transaction->hit) {
                                            return false;
                                        }
                                        $hitName = strtoupper(trim($transaction->hit->name ?? ''));
                                        return in_array($hitName, ['LOSE', 'SL']);
                                    })
                                    ->sum(function ($transaction) {
                                        return floatval(str_replace(['-', '$', ' '], '', $transaction->rugi ?? '0'));
                                    });

                                return '-$' . number_format($totalLoss, 2, '.', '');
                            }),
                    ]),

                Tables\Columns\TextColumn::make('account_balance_change')
                    ->label('Balance Change')
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->account_balance_change) {
                            return 'gray';
                        }
                        return 'info';
                    })
                    ->sortable()
                    ->default('-')
                    ->formatStateUsing(function ($state, $record) {
                        // Jika belum ada HIT, jangan tampilkan
                        if (!$record->hit_id) {
                            return '-';
                        }
                        return $state ?? '-';
                    })
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Final Balance')
                            ->using(function ($query) {
                                try {
                                    $ids = $query->pluck('id');
                                    
                                    if ($ids->isEmpty()) {
                                        return '-';
                                    }
                                    
                                    $transactions = Transaction::whereIn('id', $ids)
                                        ->with('accountTrade')
                                        ->whereNotNull('hit_id')
                                        ->get();

                                    // Jika tidak ada transaksi dengan HIT, return -
                                    if ($transactions->isEmpty()) {
                                        return '-';
                                    }

                                    $accountIds = $transactions->pluck('account_trade_id')->unique()->filter();
                                    
                                    if ($accountIds->isEmpty()) {
                                        return '-';
                                    }
                                    
                                    if ($accountIds->count() === 1) {
                                        $account = \App\Models\AccountTrade::find($accountIds->first());
                                        return $account ? $account->balance : '-';
                                    }
                                    
                                    $totalBalance = 0;
                                    foreach ($accountIds as $accountId) {
                                        $account = \App\Models\AccountTrade::find($accountId);
                                        if ($account) {
                                            $balance = floatval(str_replace(['$', ' ', ','], '', $account->balance ?? '0'));
                                            $totalBalance += $balance;
                                        }
                                    }
                                    
                                    return '$' . number_format($totalBalance, 2, '.', '');
                                } catch (\Exception $e) {
                                    \Log::error('Summary Final Balance Error: ' . $e->getMessage());
                                    return 'Error';
                                }
                            }),
                    ]),

                // Kolom dummy untuk NET Profit/Loss (hanya untuk summary)
                Tables\Columns\TextColumn::make('net_profit_loss')
                    ->label('NET Profit')
                    ->badge()
                    ->color('warning')
                    ->default('-')
                    ->formatStateUsing(function () {
                        return '-';
                    })
                    ->summarize([
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Net Profit/Loss')
                            ->using(function () {
                                // Query semua transactions dengan HIT
                                $transactions = Transaction::whereNotNull('hit_id')
                                    ->with('hit')
                                    ->get();

                                if ($transactions->isEmpty()) {
                                    return '$0.00';
                                }

                                $totalProfit = 0;
                                $totalLoss = 0;

                                foreach ($transactions as $transaction) {
                                    if (!$transaction->hit) {
                                        continue;
                                    }
                                    
                                    $hitName = strtoupper(trim($transaction->hit->name));
                                    
                                    if (in_array($hitName, ['WIN', 'TP'])) {
                                        $profitValue = floatval(str_replace(['+', '$', ' '], '', $transaction->profit ?? '0'));
                                        $totalProfit += $profitValue;
                                    } elseif (in_array($hitName, ['LOSE', 'SL'])) {
                                        $lossValue = floatval(str_replace(['-', '$', ' '], '', $transaction->rugi ?? '0'));
                                        $totalLoss += $lossValue;
                                    }
                                }

                                $netProfit = $totalProfit - $totalLoss;
                                $prefix = $netProfit >= 0 ? '+' : '';
                                
                                return $prefix . '$' . number_format($netProfit, 2, '.', '');
                            }),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_trade_id')
                    ->label('Account')
                    ->relationship('accountTrade', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('market_id')
                    ->label('Market')
                    ->relationship('market', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('risk_to_reward_id')
                    ->label('Risk/Reward')
                    ->relationship('riskToReward', 'ratio')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('hit_id')
                    ->label('HIT (Win/Lose)')
                    ->relationship('hit', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('transaction_from')
                            ->label('Transaction From')
                            ->maxDate(fn (Forms\Get $get) => $get('transaction_until') ?: now())
                            ->native(false),
                        Forms\Components\DatePicker::make('transaction_until')
                            ->label('Transaction Until')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['transaction_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['transaction_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\TransactionResource\Widgets\TransactionStats::class,
        ];
    }
}
