<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    private const PAGE_SIZE = 5;

    public function __construct()
    {
        $this->middleware('auth')->except('index','show');
        $this->middleware('auth.optional')->only('index','show');
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Post $post)
    {
        return CommentResource::collection($post->comments()->ordered()->paginate(self::PAGE_SIZE))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Post $post
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Post $post): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'text' => 'required|max:2000']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);

        $validated = $validator->validated();
        $comment = new Comment();
        $comment->text = $validated['text'];
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $post->id;
        $comment->save();

        return response()->json(new CommentResource($comment), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     */
    public function show(Request $request, Post $post, Comment $comment): JsonResponse
    {
        return response()->json(new CommentResource($comment));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Post $post, Comment $comment): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'text' => 'required|max:2000']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);

        $validated = $validator->validated();
        if (isset($validated['text'])) $comment->text = $validated['text'];
        $comment->save();

        return response()->json(new CommentResource($comment));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Request $request, Post $post, Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['message' => 'Comment removed successfully']);
    }
}
