<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'user_id' => 'nullable|numeric|min:1|exists:users,id',
            'client_id' => 'required|numeric|min:1|exists:clients,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'total_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string|max:300',
        ];
    }
}
