<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'czn_id' => $this->czn_id,
            'emp_number' => $this->emp_number,
            'name' => $this->name,
            'email' => $this->email,
            'gender' => $this->gender,
            'status' => $this->status,
            'address' => $this->address,
            'mobile' => $this->mobile,
            'birthdate' => $this->birthdate,
            'birthplace' => $this->birthplace,
            'salary' => $this->salary,
            'department_id' => $this->department_id,
            'section_id' => $this->section_id,
            'branch_id' => $this->branch_id,
            'job_id' => $this->job_id,

            // Relationships
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'job' => new JobResource($this->whenLoaded('job')),
            'user' => new UserResource($this->whenLoaded('user')),
            'dependents' => EmployeeDependentResource::collection($this->whenLoaded('employeeDependents')),
            'bonuses' => EmployeeBonusResource::collection($this->whenLoaded('employeeBonus')),
            'holidays' => EmployeeHolidayResource::collection($this->whenLoaded('employeeHoliday')),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}