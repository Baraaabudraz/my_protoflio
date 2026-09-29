<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * The contact form is public.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'service' => ['nullable', 'string', 'max:150'],
            'budget' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: hidden from people, filled in by bots (handled quietly in the controller)
            'website' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Messages in the visitor's language (the app has no lang/ar/validation.php).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Please enter your name.'),
            'name.min' => __('Please enter your name.'),
            'email.required' => __('Please enter your email so I can reply.'),
            'email.email' => __('Please enter a valid email address.'),
            'message.required' => __('Please tell me a little about your project.'),
            'message.min' => __('Please add a few more details (at least 10 characters).'),
            'message.max' => __('Your message is too long (maximum 5000 characters).'),
        ];
    }
}
