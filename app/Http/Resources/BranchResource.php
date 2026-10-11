<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'section_id' => $this->section_id,
            'manager_id' => $this->manager_id,
            'description' => $this->description,
            'is_active' => $this->is_active,

            // Relationships
            'section' => new SectionResource($this->whenLoaded('section')),
            'director' => new BranchDirectorResource($this->whenLoaded('director')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}