<?php

namespace App\Http\Requests;

use \App\Http\Requests\CommonRequest;

class SocialRequest extends CommonRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'string'
        ];
    }
}
