<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRecordFieldRequest extends FormRequest
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
            'id' => ['nullable', 'exists:users,id'],
            'user_id' => ['required', 'exists:users,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'school_class_id' => ['required', 'exists:school_classes,id', 'not_in:0'],
            'school_subject_id' => ['required', 'exists:school_subjects,id', 'not_in:0'],
            'date' => ['required', 'date'],
            'grade' => ['required', 'integer', 'min:1', 'max:12'],
            'status' => ['required', 'string', 'in:on-time,late,absent'],
            'minutes_late' => ['required_if:status,late', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
