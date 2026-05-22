<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AchievementUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        $achievementId = $this->route('achievement')?->id;

        return [
            'key' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(\\.[a-z0-9]+)*$/i', 'unique:achievements,key,'.$achievementId],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:40'],
            'tier' => ['required', 'string', 'in:bronze,silver,gold,platinum'],
            'points' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],

            // Rule configuration (admin-controlled)
            'rule_type' => ['nullable', 'string', 'in:course_count,specific_courses'],
            'rule_is_active' => ['nullable', 'boolean'],
            'min_course_completions' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = $this->input('rule_type');
            if (! $type) {
                return;
            }

            if ($type === 'course_count' && ! $this->filled('min_course_completions')) {
                $validator->errors()->add('min_course_completions', 'Min completions is required for course count rules.');
            }

            if ($type === 'specific_courses') {
                $courseIds = $this->input('course_ids', []);
                if (! is_array($courseIds) || count($courseIds) < 1) {
                    $validator->errors()->add('course_ids', 'Select at least one required course for specific courses rules.');
                }
            }
        });
    }
}
