<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $pharmacyId = $this->user()->pharmacy_id;

        return [
            // Scoped so a bill can never be posted against another pharmacy's distributor.
            'distributor_id' => [
                'required',
                Rule::exists('distributors', 'id')->where(fn ($query) => $query->where('pharmacy_id', $pharmacyId)),
            ],
            'bill_no' => [
                'required', 'string', 'max:100',
                Rule::unique('purchases', 'bill_no')->where(fn ($query) => $query->where('pharmacy_id', $pharmacyId)),
            ],
            'bill_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'],
            'items.*.batch_no' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
