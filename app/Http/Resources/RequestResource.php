<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'request_type' => [
                'id' => $this->requestType->id ?? null,
                'name' => $this->requestType->name ?? null,
            ],
            'status' => [
                'id' => $this->status->id ?? null,
                'name' => $this->status->name ?? null,
                'color' => $this->status->color ?? null,
            ],
            'mokalaf' => [
                'id' => $this->mokalaf->id ?? null,
                'name' => $this->mokalaf->name ?? null,
                'identity_number' => $this->mokalaf->identity_number ?? null,
            ],
            'deadline' => $this->deadline ? $this->deadline->format('Y-m-d') : null,
            'is_urgent' => $this->is_urgent,
            'fee_amount' => $this->fee_amount !== null ? (string) $this->fee_amount : null,
            'paid_amount' => (string) ($this->paid_amount ?? '0'),
            'remaining_amount' => (string) ($this->remaining_amount ?? '0'),
            'creator' => [
                'id' => $this->creator->id ?? null,
                'name' => $this->creator->name ?? null,
            ],
            'address' => $this->address,
            'mobile' => $this->mobile,
            'attachments_count' => $this->attachments->count(),
            'responses_count' => $this->responses->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
