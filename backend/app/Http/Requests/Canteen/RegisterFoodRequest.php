<?php

namespace App\Http\Requests\Canteen;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RegisterFoodRequest extends BaseRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
            'image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg', // Allowed MIME types
                'max:5120', // Max File Size in KB
                Rule::dimensions()->maxWidth(3000)->maxHeight(3000), // Optional: Max dimensions
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image_url.image' => 'The file must be an image.',
            'image_url.max' => 'The image may not be greater than 5MB.',
            'image_url.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image_url.dimensions' => 'The image dimensions are too large (max 3000x3000 pixels).',
        ];
    }
}
