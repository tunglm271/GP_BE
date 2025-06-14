<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalFacilityRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facility_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'working_time' => 'nullable|string|max:255',
            'thumbnail' => 'nullable',
            'logo' => 'nullable',
            'website' => 'nullable|url|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'legal_representative_name' => 'nullable|string|max:255',
            'legal_representative_id' => 'nullable|string|max:50',
            'tax_code' => 'nullable|string|max:50',
            'medical_practice_license' => 'nullable|string|max:255',
            'issuance_date' => 'nullable|date',
            'issuance_place' => 'nullable|string|max:255',
            'business_registration_certificate' => 'nullable|string|max:255',
        ];
    }
}
