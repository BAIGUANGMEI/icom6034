<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class TagController extends Controller
{
    /**
     * Display a listing of all tags.
     */
    #[OA\Get(
        path: '/api/tags',
        summary: 'List all tags',
        tags: ['Tags'],
        responses: [
            new OA\Response(response: 200, description: 'List of tags',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Tag')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        // TODO: Implement list all tags
    }

    /**
     * Display the specified tag with its posts.
     */
    #[OA\Get(
        path: '/api/tags/{id}',
        summary: 'Get a tag with its related posts',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Tag ID'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tag detail with posts',
                content: new OA\JsonContent(ref: '#/components/schemas/Tag')
            ),
            new OA\Response(response: 404, description: 'Tag not found'),
        ]
    )]
    public function show(string $id)
    {
        // TODO: Implement show tag with related posts
    }
}
