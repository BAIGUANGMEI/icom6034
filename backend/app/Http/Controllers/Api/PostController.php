<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    #[OA\Get(
        path: '/api/posts',
        summary: 'List all posts',
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Page number'),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Items per page'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of posts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Post')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        // TODO: Implement list all posts
    }

    /**
     * Store a newly created post.
     */
    #[OA\Post(
        path: '/api/posts',
        summary: 'Create a new post',
        tags: ['Posts'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content', 'tags'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'My Interview at Google'),
                    new OA\Property(property: 'content', type: 'string', example: 'Here is my experience...'),
                    new OA\Property(property: 'tags', type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['interview', 'google']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Post created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Post')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store()
    {
        // TODO: Implement create post
    }

    /**
     * Display the specified post.
     */
    #[OA\Get(
        path: '/api/posts/{id}',
        summary: 'Get a single post by ID',
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Post detail',
                content: new OA\JsonContent(ref: '#/components/schemas/Post')
            ),
            new OA\Response(response: 404, description: 'Post not found'),
        ]
    )]
    public function show(string $id)
    {
        // TODO: Implement show single post
    }

    /**
     * Update the specified post.
     */
    #[OA\Put(
        path: '/api/posts/{id}',
        summary: 'Update an existing post',
        tags: ['Posts'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'content', type: 'string'),
                    new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Post updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Post')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Post not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(string $id)
    {
        // TODO: Implement update post
    }

    /**
     * Remove the specified post.
     */
    #[OA\Delete(
        path: '/api/posts/{id}',
        summary: 'Delete a post',
        tags: ['Posts'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Post ID'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Post deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Post deleted successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Post not found'),
        ]
    )]
    public function destroy(string $id)
    {
        // TODO: Implement delete post
    }

    /**
     * Search posts by keyword or tag.
     */
    #[OA\Get(
        path: '/api/search/posts',
        summary: 'Search posts by keyword or tag',
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'keyword', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Search keyword'),
            new OA\Parameter(name: 'tag', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Filter by tag name'),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), description: 'Page number'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Search results',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Post')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function search()
    {
        // TODO: Implement post search (keyword/tag)
    }

    /**
     * Get posts for the authenticated user.
     */
    #[OA\Get(
        path: '/api/my-posts',
        summary: 'Get current user\'s posts',
        tags: ['Posts'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of user\'s own posts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Post')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function myPosts()
    {
        // TODO: Implement get user's own posts
    }
}
