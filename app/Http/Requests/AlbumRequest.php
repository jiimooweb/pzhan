<?php

namespace App\Http\Requests;

use \App\Http\Requests\CommonRequest;

class AlbumRequest extends CommonRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'fan_id' => 'required'
        ];
    }
}
