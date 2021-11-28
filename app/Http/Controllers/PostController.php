<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    const PAGE_SIZE = 5;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return PostResource::collection(Post::with(['user', 'comments' => function (HasMany $builder) {
            $builder->ordered();
        }])->ordered()->paginate(self::PAGE_SIZE))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     * @throws ValidationException
     */
    public function store(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|max:100',
            'text' => 'required|max:2000']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);
        $validated = $validator->validated();
        $post = new Post();
        $post->title = $validated['title'];
        $post->text = $validated['text'];
        $post->user_id = User::inRandomOrder()->first()->id;
        $post->save();

        return response()->json(new PostResource($post), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post, Comment $comment): JsonResponse
    {
        return response()->json(new PostResource($post));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Post $post, Comment $comment): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'sometimes|required|max:100',
            'text' => 'sometimes|required|max:2000']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);
        $validated = $validator->validated();
        if (isset($validated['title'])) $post->title = $validated['title'];
        if (isset($validated['text'])) $post->text = $validated['text'];
        $post->save();

        return response()->json(new PostResource($post));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post, Comment $comment): JsonResponse
    {
        $post->delete();
        return response()->json(['message' => 'Post removed successfully']);
    }
}
