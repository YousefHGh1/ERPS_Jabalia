<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'code' => $this->code,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'is_active' => $this->is_active,

            // Relationships
            'category' => new JobCategoryResource($this->whenLoaded('category')),
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}