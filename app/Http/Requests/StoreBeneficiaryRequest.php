<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
            'area_id' => ['required', 'exists:areas,id'],
            'family_members_count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'has_children' => ['nullable', 'boolean'],
            'has_elderly' => ['nullable', 'boolean'],
            'social_status' => ['nullable', 'string', 'in:married,widowed,divorced,single,other'],
            'full_address' => ['required', 'string', 'max:2000'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_accuracy' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'يرجى تحديد موقع التوصيل من زر تحديد موقعي الحالي.',
            'longitude.required' => 'يرجى تحديد موقع التوصيل من زر تحديد موقعي الحالي.',
        ];
    }
}
