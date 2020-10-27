<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->method() == "PUT") {
            $event = \App\Event::find($this->route('id'));
            return $event && $this->user()->can("update-event", $event);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "title" =>  ["required", "string", "max:100"],
            "description" => ["required", "string"],
            "started" => ["required", "string"],
            "localization" => ["required", "string"]
        ];
    }
}
