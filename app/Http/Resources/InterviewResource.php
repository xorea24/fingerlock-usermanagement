<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
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
            'interview_date' => $this->interview_date,
            'status' => $this->statusToString(),
            'position' => new PositionResource($this->whenLoaded('position')),
            'created_at' => $this->created_at?->toISOString()
        ];
    }
}