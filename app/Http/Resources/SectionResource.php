<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'department_id' => $this->department_id,
            'manager_id' => $this->manager_id,
            'description' => $this->description,
            'is_active' => $this->is_active,

            // Relationships
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'director' => new SectionDirectorResource($this->whenLoaded('director')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'branches' => BranchResource::collection($this->whenLoaded('branches')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}