<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** API resource for Comment: id, content, user (commenter), parent_id, parent (when reply), created_at. */
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => new UserResource($this->whenLoaded('user')),
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'user' => $this->parent->user ? ['id' => $this->parent->user->id, 'name' => $this->parent->user->name] : null,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
