<?php

namespace App\Http\Requests\Canteen;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateFoodRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert the string 'true' or 'false' from multipart/form-data
        // into a real boolean type before validation runs.
        if ($this->has('is_available')) {
            $this->merge([
                'is_available' => filter_var($this->is_available, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'stock' => ['nullable', 'integer'],
            'is_available' => ['nullable', 'boolean'],
            'image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg', // Allowed MIME types
                'max:2048', // Max File Size in KB
                Rule::dimensions()->maxWidth(1000)->maxHeight(1000), // Optional: Max dimensions
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image_url.image' => 'The file must be an image.',
            'image_url.max' => 'The image may not be greater than 2MB.',
            'image_url.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image_url.dimensions' => 'The image dimensions are too large (max 1000x1000 pixels).',
        ];
    }
}
