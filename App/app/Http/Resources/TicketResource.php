<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public $preserveKeys = false;


    public function toArray($request)
    {
        return [
            'ticket' => [
                'id' => $this->id,
                'price' => $this->price,
                'count' => $this->count,
                'preview_img' => $this->preview_img,
            ],
            'aircompany' => $this->aircompany,
            'segments' => $this->segments->sortBy('order')->values()
        ];
    }
}
