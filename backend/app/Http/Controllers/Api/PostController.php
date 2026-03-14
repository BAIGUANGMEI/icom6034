<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePostRequest;
use App\Http\Requests\Api\UpdatePostRequest;
use App\Http\Requests\Api\UploadPostImageRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

/**
 * API controller for posts: CRUD, search, and image upload.
 * Content supports rich text (HTML); images are stored locally via uploadImage().
 */
class PostController extends Controller
{
    /**
     * List all posts with pagination. Eager-loads user and tags to avoid N+1.
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
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * Create a new post (auth required). Syncs tags by name (firstOrCreate).
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
    public function store(StorePostRequest $request)
    {
        // Create post for authenticated user; content may be HTML from rich text editor
        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->validated('content'),
        ]);

        // Resolve tag names to IDs (create tag if not exists), then sync
        $tagIds = collect($request->tags)->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => strtolower(trim($tagName))])->id;
        });
        $post->tags()->sync($tagIds);

        $post->load(['user', 'tags']);

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Return a single post by ID with user, tags, and comment count.
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
        $post = Post::with(['user', 'tags'])
            ->withCount('comments')
            ->findOrFail($id);

        return new PostResource($post);
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
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::findOrFail($id);

        // Only the post author can update
        if ($post->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        if ($request->filled('title')) {
            $post->title = $request->title;
        }
        if ($request->filled('content')) {
            $post->content = $request->validated('content');
        }
        $post->save();

        // Optionally update tags (same firstOrCreate + sync as store)
        if ($request->has('tags')) {
            $tagIds = collect($request->tags)->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => strtolower(trim($tagName))])->id;
            });
            $post->tags()->sync($tagIds);
        }

        $post->load(['user', 'tags']);

        return new PostResource($post);
    }

    /**
     * Delete a post. Only the author is allowed (403 otherwise).
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
    public function destroy(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
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
    public function search(Request $request)
    {
        $query = Post::with(['user', 'tags'])->withCount('comments');

        // Optional: filter by keyword in title or content
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        // Optional: filter by tag name
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', 'like', $request->tag);
            });
        }

        $posts = $query->latest()->paginate($request->get('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * List posts belonging to the authenticated user (auth required).
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
    public function myPosts(Request $request)
    {
        $posts = $request->user()
            ->posts()
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }

    /**
     * List posts by a given user ID (public). Used e.g. for profile page.
     */
    #[OA\Get(
        path: '/api/users/{id}/posts',
        summary: 'Get all posts by a specific user',
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of user posts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Post')
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function userPosts(int $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $posts = Post::where('user_id', $user->id)
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }

    /**
     * Upload one image for use in post body (rich text). Stored under storage/app/public/post-images.
     * Returns public URL for the client to embed in content. Auth required.
     */
    #[OA\Post(
        path: '/api/posts/images',
        summary: 'Upload image for post (stored locally)',
        tags: ['Posts'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['image'],
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file (jpeg, png, gif, webp, max 5MB)'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Image uploaded',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'url', type: 'string', example: 'http://localhost:8000/storage/post-images/xxx.jpg'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function uploadImage(UploadPostImageRequest $request)
    {
        // Store under public disk so it is served at /storage/post-images/...
        $path = $request->file('image')->store('post-images', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json(['url' => $url], 201);
    }
}
