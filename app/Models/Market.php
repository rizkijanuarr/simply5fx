<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Market extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->slug) {
                $model->slug = Str::slug($model->name.'-'.now()->timestamp);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'multiplier' => 'decimal:2',
        ];
    }
}
