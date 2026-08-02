<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'document_type' => $this->document_type,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'decided_at' => $this->decided_at,
            'note' => $this->note,
            'applicant' => $this->whenLoaded('applicant'),
            'decided_by' => $this->whenLoaded('decidedBy'),
            'histories' => $this->whenLoaded('histories'),
            'files' => $this->whenLoaded('files'),
        ];
    }
}
