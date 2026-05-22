<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('courses', 'slug')->ignore($courseId),
            ],
            'summary' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:20000'],
            'completion_criteria' => ['nullable', 'string', 'max:20000'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
        ];
    }

    public function preparedSlug(): string
    {
        $slug = $this->input('slug');

        if (is_string($slug) && $slug !== '') {
            return Str::slug($slug);
        }

        return Str::slug((string) $this->input('title'));
    }
}
