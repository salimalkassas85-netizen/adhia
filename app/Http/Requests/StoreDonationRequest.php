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


    protected function prepareForValidation(): void
    {
        $donorAreaId = auth()->user()?->area_id ?: $this->input('donor_area_id');

        $this->merge([
            'donor_area_id' => $donorAreaId,
            'target_area_id' => $donorAreaId,
            'donation_scope' => 'own_area',
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userAreaId = auth()->user()?->area_id;

            if ($userAreaId && (int) $this->input('donor_area_id') !== (int) $userAreaId) {
                $validator->errors()->add('donor_area_id', 'لا يمكن تسجيل مساهمة خارج منطقتك.');
            }

            if ($this->filled('selected_case_id')) {
                $caseExists = \App\Models\BeneficiaryRequest::whereKey($this->integer('selected_case_id'))
                    ->where('area_id', $this->integer('donor_area_id'))
                    ->exists();

                if (! $caseExists) {
                    $validator->errors()->add('selected_case_id', 'الحالة المختارة يجب أن تكون من نفس منطقتك.');
                }
            }
        });
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
            'donor_area_id' => ['required', 'exists:areas,id'],
            'target_area_id' => ['nullable', 'exists:areas,id'],
            'donation_scope' => ['nullable', 'in:own_area'],
            'donation_type' => ['required', 'in:meat_kg,money,sacrifice_share,full_sacrifice'],
            'amount' => ['nullable', 'required_if:donation_type,money', 'numeric', 'min:1'],
            'meat_kg' => ['nullable', 'required_if:donation_type,meat_kg', 'numeric', 'min:1'],
            'pickup_address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_accuracy' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'selected_case_id' => ['nullable', 'integer', 'exists:beneficiary_requests,id'],
            'selected_cases' => ['prohibited'],
            'selected_cases.*' => ['prohibited'],
        ];
    }
}
