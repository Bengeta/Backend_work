<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class NewsApiController extends Controller
{
    private const PAGE_SIZE = 5;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index','show');
        $this->middleware('auth.optional')->only('index','show');
        $this->authorizeResource(News::class, 'news');
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return NewsResource::collection(News::query()
            ->whereDate('published_at', '<=', date('Y-m-d H:i:s'))
            ->where('is_published', true)
            ->ordered()
            ->paginate(self::PAGE_SIZE));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'text' => 'required|max:2000',
            'is_published' => 'required|boolean']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);

        $validated = $validator->validated();
        $news = new News();
        $news->title = $validated['title'];
        $news->text = $validated['text'];
        if ($request->get('description') != null) $news->description = $request->get('description');
        $news->is_published = $validated['is_published'];
        $news->published_at = now();
        $news->save();
        return response()->json(new NewsResource($news), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param News $news
     * @return JsonResponse
     */
    public function show(News $news): JsonResponse
    {
        return response()->json(new NewsResource($news));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param News $news
     * @return JsonResponse
     */
    public function update(Request $request, News $news): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'sometimes|required|max:100',
            'text' => 'sometimes|required|max:2000',
            'is_published' => 'sometimes|boolean']);
        if ($validator->fails()) return response()->json($validator->errors()->all(), 422);
        $validated = $validator->validated();
        if (isset($validated['title'])) $news->title = $validated['title'];
        if (isset($validated['text'])) $news->text = $validated['text'];
        if (isset($validated['is_published'])) {
            if ($validated['is_published'] === true && $news->is_published === false)
                $news->published_at = now();
            $news->is_published = $validated['is_published'];
        }
        if ($request->get('description') != null) $news->description = $request->get('description');
        $news->save();
        return response()->json(new NewsResource($news));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param News $news
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(News $news): JsonResponse
    {
        $news->delete();
        return response()->json(['message' => 'Article removed successfully']);
    }
}
