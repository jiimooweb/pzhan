<?php

namespace App\Http\Requests;

use App\Http\Requests\CommonRequest;

class ReportRequest extends CommonRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bereported_id' => 'required|integer',
            'comment' => 'required|integer',
            'type' => 'required',
            'cause' => 'required|integer',
        ];
    }
}
