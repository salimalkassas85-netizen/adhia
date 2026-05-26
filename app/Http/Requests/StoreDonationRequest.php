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

            $hasCasesInArea = \App\Models\BeneficiaryRequest::where('area_id', $this->integer('donor_area_id'))->exists();

            if (! $hasCasesInArea) {
                $validator->errors()->add('selected_case_id', 'لا يمكن تسجيل مساهمة الآن لأنه لا توجد حالات محتاجة في منطقتك.');
                return;
            }

            if ($this->filled('selected_case_id')) {
                $caseExists = \App\Models\BeneficiaryRequest::whereKey($this->integer('selected_case_id'))
                    ->where('area_id', $this->integer('donor_area_id'))
                    ->exists();

                if (! $caseExists) {
                    $validator->errors()->add('selected_case_id', 'يجب اختيار حالة محتاجة من نفس منطقتك قبل تسجيل المساهمة.');
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
            'selected_case_id' => ['required', 'integer', 'exists:beneficiary_requests,id'],
            'selected_cases' => ['prohibited'],
            'selected_cases.*' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'donor_name.string' => 'اسم المساهم يجب أن يكون نصًا صحيحًا.',
            'donor_name.max' => 'اسم المساهم لا يجب أن يزيد عن 255 حرفًا.',

            'donor_phone.required' => 'رقم الهاتف مطلوب حتى يستطيع أدمن المنطقة التواصل معك.',
            'donor_phone.string' => 'رقم الهاتف يجب أن يكون نصًا صحيحًا.',
            'donor_phone.max' => 'رقم الهاتف لا يجب أن يزيد عن 20 رقمًا.',

            'donor_area_id.required' => 'من فضلك اختر منطقتك.',
            'donor_area_id.exists' => 'المنطقة المختارة غير صحيحة.',
            'target_area_id.exists' => 'منطقة التوجيه غير صحيحة.',
            'donation_scope.in' => 'نطاق المساهمة غير صحيح.',

            'donation_type.required' => 'من فضلك اختر نوع المساهمة.',
            'donation_type.in' => 'نوع المساهمة المختار غير صحيح.',

            'amount.required_if' => 'المبلغ مطلوب عند اختيار مساهمة مالية.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقمًا صحيحًا.',
            'amount.min' => 'المبلغ يجب ألا يقل عن 1 جنيه.',

            'meat_kg.required_if' => 'كمية اللحم بالكيلو مطلوبة عند اختيار لحم بالكيلو.',
            'meat_kg.numeric' => 'كمية اللحم يجب أن تكون رقمًا صحيحًا.',
            'meat_kg.min' => 'كمية اللحم يجب ألا تقل عن 1 كيلو.',

            'pickup_address.string' => 'عنوان الاستلام يجب أن يكون نصًا صحيحًا.',
            'pickup_address.max' => 'عنوان الاستلام لا يجب أن يزيد عن 2000 حرف.',

            'latitude.numeric' => 'إحداثيات موقع الاستلام غير صحيحة.',
            'latitude.between' => 'إحداثيات موقع الاستلام غير صحيحة.',
            'longitude.numeric' => 'إحداثيات موقع الاستلام غير صحيحة.',
            'longitude.between' => 'إحداثيات موقع الاستلام غير صحيحة.',
            'location_accuracy.integer' => 'دقة الموقع غير صحيحة.',
            'location_accuracy.min' => 'دقة الموقع غير صحيحة.',
            'location_accuracy.max' => 'دقة الموقع غير صحيحة.',

            'notes.string' => 'الملاحظات يجب أن تكون نصًا صحيحًا.',
            'notes.max' => 'الملاحظات لا يجب أن تزيد عن 2000 حرف.',

            'selected_case_id.required' => 'اختيار المحتاج إجباري قبل تسجيل المساهمة.',
            'selected_case_id.integer' => 'الحالة المختارة غير صحيحة.',
            'selected_case_id.exists' => 'الحالة المختارة غير موجودة.',

            'selected_cases.prohibited' => 'لا يمكن اختيار أكثر من محتاج في نفس المساهمة.',
            'selected_cases.*.prohibited' => 'لا يمكن اختيار أكثر من محتاج في نفس المساهمة.',
        ];
    }

    public function attributes(): array
    {
        return [
            'donor_name' => 'اسم المساهم',
            'donor_phone' => 'رقم الهاتف',
            'donor_area_id' => 'منطقتك',
            'target_area_id' => 'منطقة التوجيه',
            'donation_scope' => 'نطاق المساهمة',
            'donation_type' => 'نوع المساهمة',
            'amount' => 'المبلغ',
            'meat_kg' => 'كمية اللحم',
            'pickup_address' => 'عنوان الاستلام',
            'latitude' => 'خط العرض',
            'longitude' => 'خط الطول',
            'location_accuracy' => 'دقة الموقع',
            'notes' => 'الملاحظات',
            'selected_case_id' => 'المحتاج',
        ];
    }

}
