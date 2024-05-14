<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes(): array
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
        ];

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $value) {
            if ($this->has($key)) {
                $attributesToUpdate[$value] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'data.attributes.status' => 'Status must be one of: A, C, H, X',
            'data.relationships.author.data.id.required' => 'Author ID is required',
            'data.relationships.author.data.id.integer' => 'Author ID must be an integer',
        ];
    }
}
