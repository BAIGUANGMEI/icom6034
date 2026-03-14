<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use OpenApi\Attributes as OA;

/**
 * API controller for post comments: list, create, delete.
 * All write operations require authentication; delete is restricted to comment author.
 */
class CommentController extends Controller
{
    /**
     * List all comments for a post, newest first, with commenter (user) loaded.
     */
    #[OA\Get(
        path: '/api/posts/{postId}/comments',
        summary: 'List comments for a post',
        tags: ['Comments'],
        parameters: [
            new OA\Parameter(name: 'postId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of comments',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Comment')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Post not found'),
        ]
    )]
    public function index(string $postId)
    {
        $post = Post::findOrFail($postId);
        // Load all comments (roots and replies) with user and parent comment's user for "Reply to X"
        $comments = $post->comments()
            ->with(['user', 'parent.user'])
            ->orderByRaw('parent_id IS NULL DESC')
            ->latest()
            ->get();

        return CommentResource::collection($comments);
    }

    /**
     * Create a comment on a post (auth required). Author is current user.
     */
    #[OA\Post(
        path: '/api/posts/{postId}/comments',
        summary: 'Create a comment on a post',
        tags: ['Comments'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'postId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', maxLength: 255, example: 'Great post, thanks for sharing!'),
                    new OA\Property(property: 'parent_id', type: 'integer', nullable: true, description: 'ID of comment to reply to (optional)'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Comment')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Post not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreCommentRequest $request, string $postId)
    {
        $post = Post::findOrFail($postId);

        $parentId = $request->validated('parent_id');
        if ($parentId) {
            $parent = Comment::where('id', $parentId)->where('post_id', $postId)->firstOrFail();
        }

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'content' => $request->validated('content'),
        ]);
        $comment->load(['user', 'parent.user']);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Delete a comment. Only the comment author is allowed (403 otherwise).
     */
    #[OA\Delete(
        path: '/api/posts/{postId}/comments/{id}',
        summary: 'Delete a comment',
        tags: ['Comments'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'postId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Comment ID'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comment deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Comment deleted successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ]
    )]
    public function destroy(string $postId, string $id)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($id);

        if ($comment->user_id !== request()->user()->id) {
            abort(403, 'Forbidden');
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
