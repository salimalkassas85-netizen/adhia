<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_phone' => ['required', 'string', 'max:20'],
            'donor_area_id' => ['nullable', 'exists:areas,id'],
            'target_area_id' => ['nullable', 'required_if:donation_scope,selected_area', 'exists:areas,id'],
            'donation_scope' => ['required', 'in:own_area,selected_area,most_needed'],
            'donation_type' => ['required', 'in:meat_kg,money,sacrifice_share,full_sacrifice'],
            'amount' => ['nullable', 'required_if:donation_type,money', 'numeric', 'min:1'],
            'meat_kg' => ['nullable', 'required_if:donation_type,meat_kg', 'numeric', 'min:1'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_accuracy' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
