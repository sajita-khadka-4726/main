<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Project
 */
final class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'organization_id' => $this->organization_id,
            'description' => $this->description,
            'color' => $this->color,
            'status' => $this->status,
            'deadline' => $this->deadline,
            'created_by' => $this->created_by,
        ];
    }
}
