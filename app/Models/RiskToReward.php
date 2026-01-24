<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RiskToReward extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->slug) {
                $model->slug = Str::slug($model->ratio.'-'.now()->timestamp);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }
}
