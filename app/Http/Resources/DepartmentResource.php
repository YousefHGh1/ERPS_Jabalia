<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'manager_id' => $this->manager_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'color' => $this->color,

            // Relationships
            'parent' => new self($this->whenLoaded('parent')),
            'children' => self::collection($this->whenLoaded('children')),
            'director' => new DepartmentDirectorResource($this->whenLoaded('director')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'sections' => SectionResource::collection($this->whenLoaded('sections')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}