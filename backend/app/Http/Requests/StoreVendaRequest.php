<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente' => ['required', 'string'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade' => ['required', 'integer', 'gt:0'],
            'produtos.*.preco_unitario' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente.required' => 'O nome do cliente é obrigatório.',
            'produtos.required' => 'Pelo menos um produto é obrigatório.',
            'produtos.min' => 'Pelo menos um produto é obrigatório.',
            'produtos.*.id.exists' => 'O produto selecionado não existe.',
            'produtos.*.quantidade.gt' => 'A quantidade deve ser maior que zero.',
            'produtos.*.preco_unitario.gt' => 'O preço unitário deve ser maior que zero.',
        ];
    }
}
