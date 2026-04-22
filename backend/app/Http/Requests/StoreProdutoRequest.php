<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'        => ['required', 'string', 'min:3', 'max:255'],
            'preco_venda' => ['required', 'numeric', 'gt:0', 'max:9999999.99'],
            'estoque'     => ['sometimes', 'integer', 'min:0'],
            'custo_medio' => ['sometimes', 'numeric', 'min:0'],
            'ativo'       => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'    => 'O nome do produto é obrigatório.',
            'nome.min'         => 'O nome deve ter no mínimo 3 caracteres.',
            'nome.max'         => 'O nome deve ter no máximo 255 caracteres.',
            'preco_venda.required' => 'O preço de venda é obrigatório.',
            'preco_venda.gt'   => 'O preço de venda deve ser maior que zero.',
            'preco_venda.max'  => 'O preço de venda é muito alto.',
            'estoque.integer'  => 'O estoque deve ser um número inteiro.',
            'estoque.min'      => 'O estoque não pode ser negativo.',
            'custo_medio.min'  => 'O custo médio não pode ser negativo.',
        ];
    }
}
