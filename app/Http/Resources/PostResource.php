<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'id'           => $this->id,
            'image'        => route('image.displayImage',explode('/',$this->image)),
            'caption'      => $this->caption,
            'created_at'   => $this->created_at->format('d-m-Y H:i'),
            'countlikes'   => count($this->likes),
            'countcomment' => count($this->comments),
            'likes'        => LikeResource::collection($this->whenLoaded('likes')),
            'comments'     => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
