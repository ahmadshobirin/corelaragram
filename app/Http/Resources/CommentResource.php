<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'id'         => $this->id,
            'post_id'    => $this->post_id,
            'user'       => User::where('id',$this->user_id)->first()->name,
            'comment'    => $this->comment,
            'created_at' => $this->created_at->format('d-m-Y H:i'),
        ];
    }
}
