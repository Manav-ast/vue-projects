<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        // Allow all users to make this request
        return true;
    }

    public function rules()
    {
        return [
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date'
        ];
    }
}
