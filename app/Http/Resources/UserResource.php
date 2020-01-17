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
            'email'  => $this->email,
            'joined' => $this->created_at->diffForHumans(),
            'photo'  => ($this->token != null )
                        ? '-'
                        : route('image.displayImage', explode('/', $this->photo)),
            'follow'    => $this->follow(),
            'followers' => $this->followers(),
            'token'     => $this->token,
        ];
    }
}
