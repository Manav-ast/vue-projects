<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{

    public function authorize()
    {
        // Allow all users to make this request
        return true;
    }
    // public function authorize()
    // {

    //     return $this->user() !== null;
    // }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }
}
