<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'extension_name' => $this->extension_name,
            'full_name' => $this->getFullName(),
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'civil_status' => $this->civil_status,
            'address' => $this->address,
            'created_at' => $this->created_at?->toISOString(),
            'position' => new PositionResource($this->whenLoaded('position')),
        ];
    }
}