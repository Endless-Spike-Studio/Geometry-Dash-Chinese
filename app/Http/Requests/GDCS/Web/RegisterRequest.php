<?php

namespace App\Http\Requests\GDCS\Web;

use App\Http\Requests\Request;
use App\Models\GDCS\Account;
use Illuminate\Validation\Rule;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique(Account::class)
            ],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique(Account::class)
            ],
            'password' => [
                'required',
                'string',
                'confirmed'
            ]
        ];
    }
}
