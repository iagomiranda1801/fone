<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'custo_medio',
        'preco_venda',
        'estoque',
    ];

    protected $casts = [
        'custo_medio' => 'decimal:2',
        'preco_venda' => 'decimal:2',
        'estoque' => 'integer',
    ];

    public function compraItens(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function vendaItens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }
}
