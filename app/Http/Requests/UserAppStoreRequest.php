<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAppStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'botType' => 'required',
            'botName' => 'required',
            'mediaName' => 'sometimes',
            'pageID' => 'sometimes',
            'userID' => 'sometimes',
            'botNickname' => 'sometimes',
            'channelLink' => 'sometimes',
            'botUsername' => 'sometimes',
            'botAccessToken' => 'sometimes',
            'botLink' => 'sometimes',
        ];
    }
}
