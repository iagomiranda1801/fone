<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'total',
        'lucro',
        'cancelada',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'lucro' => 'decimal:2',
        'cancelada' => 'boolean',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function scopeAtiva(Builder $query): Builder
    {
        return $query->where('cancelada', false);
    }
}
