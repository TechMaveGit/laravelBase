<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenericResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Encrypt the 'id' attribute
        $response = ['id' => encryptUrlId($this->id)];

        // Automatically add all other model attributes
        foreach ($this->resource->toArray() as $attribute => $value) {
            if ($attribute !== 'id') {
                $response[$attribute] = $value;
            }
        }

        return $response;
    }
}
