<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response()->json(Post::with('categories')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|unique:posts',
            'content' => 'required|string',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        $post->categories()->attach($request->category_ids);

        return response()->json($post->load('categories'), 201);
    }

    public function show(Post $post)
    {
        return response()->json($post->load('categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'sometimes|required|string|unique:posts,title,' . $post->id,
            'content' => 'sometimes|required|string',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $post->update($request->only(['title', 'content']));

        if ($request->has('category_ids')) {
            $post->categories()->sync($request->category_ids);
        }

        return response()->json($post->load('categories'));
    }

    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
