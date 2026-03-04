<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    /**
     * Display comments for a post.
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
        // TODO: Implement list comments for a post
    }

    /**
     * Store a newly created comment.
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
    public function store(string $postId)
    {
        // TODO: Implement create comment
    }

    /**
     * Remove the specified comment.
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
        // TODO: Implement delete comment
    }
}
