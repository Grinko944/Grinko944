<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "photo" => "required|mimes:jpg,png,jpeg|max:10000",
            "name" => "required|string",
            "name_RU" => "required|string",
            "name_BG" => "required|string",
            "name_DE" => "required|string",
            "description" => "required|string",
            "description_RU" => "required|string",
            "description_BG" => "required|string",
            "description_DE" => "required|string"
        ];

        switch ($this->getMethod()) {
            case "POST":
                return $rules;
            case "PUT":
            case "PATCH":
                return [
                    "photo" => "mimes:jpg,png,jpeg|max:10000",
                    "name" => "string",
                    "name_RU" => "string",
                    "name_BG" => "string",
                    "name_DE" => "string",
                    "description" => "string",
                    "description_RU" => "string",
                    "description_BG" => "string",
                    "description_DE" => "string",
                    "price" => "decimal"
                ];
            default:
                return [];
        }
    }

    public function messages()
    {
        return [
            "data.required" => "A date is required",
            "date.date_format" => "A date must be in format: Y-m-d",
            "date.unique" => "This date is already taken",
            "date.after_or_equal" => "A date must be after or equal today",
            "date.exists" => "This date doesn\"t exists",
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all();
        switch ($this->getMethod()) {
            case "DELETE":
                $data["date"] = $this->route("day");
        }
        return $data;
    }

}
