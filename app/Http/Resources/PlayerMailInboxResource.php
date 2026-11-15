<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerMailInboxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->mail->id,
            'category' => $this->mail->category->value,
            'sender' => $this->mail->sender_name,
            'subject' => $this->mail->subject,
            'read_at' => $this->read_at,
            'received_at' => $this->received_at,
        ];
    }
}
