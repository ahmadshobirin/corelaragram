<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'name'   => ucfirst($this->name),
            'email'  => $this->id,
            'joined' => $this->created_at->diffForHumans(),
            'token'  => $this->mergeWhen($this->token != null, $this->token)
        ];
    }
}
