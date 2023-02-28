<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|numeric|min:1|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1|max:1000', // Assumption: Maximum order for per product is set to 1000
        ];
    }
}
