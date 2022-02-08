<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PassangerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'passanger' => [
                'id' => $this->id,
                'name' => $this->name,
                'surname' => $this->surname,
                'dob' => $this->dob,
                'doc' => $this->doc,
                'email' => $this->email,
                'password' => $this->password,
                'remember_token' => $this->remember_token,
                'avatar_path' => $this->avatar_path
            ]
        ];
    }
}
