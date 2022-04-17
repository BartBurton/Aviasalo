<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'id' => $this->id,
            'name'=> $this->user->name,
            
            'surname' => $this->surname,
            'dob' => $this->dob,
            'doc' => $this->doc,
            'email' => $this->email,

            'avatar_path' => $this->avatar_path,

            'password'=> $this->user->password,
            'remember_token' => $this->user->remember_token,
        ];
    }
}
