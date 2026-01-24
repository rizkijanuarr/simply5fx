<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LotSize extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->slug) {
                $model->slug = Str::slug($model->size.'-'.now()->timestamp);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'size' => 'decimal:2',
        ];
    }
}
