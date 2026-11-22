<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'mechanic_id' => $this->mechanic_id,
            'manager_id' => $this->manager_id,
            'vehicle_model' => $this->vehicle_model,
            'license_plate' => $this->license_plate,
            'year' => $this->year,
            'description' => $this->description,
            'status' => $this->status,
            'estimated_cost' => $this->estimated_cost,
            'actual_cost' => $this->actual_cost,
            'start_date' => $this->start_date?->toISOString(),
            'completion_date' => $this->completion_date?->toISOString(),
            'notes' => $this->notes,
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
            ]),
            'mechanic' => $this->whenLoaded('mechanic', fn () => [
                'id' => $this->mechanic?->id,
                'name' => $this->mechanic?->name,
                'email' => $this->mechanic?->email,
            ]),
            'manager' => $this->whenLoaded('manager', fn () => [
                'id' => $this->manager?->id,
                'name' => $this->manager?->name,
                'email' => $this->manager?->email,
            ]),
            'services' => $this->whenLoaded('services', fn () => $this->services->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'quantity' => $service->pivot?->quantity ?? 1,
                'unit_price' => $service->pivot?->unit_price,
                'subtotal' => $service->pivot?->subtotal,
            ])->all()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
