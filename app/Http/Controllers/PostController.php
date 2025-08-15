<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // All queries now automatically go to the tenant database
        $posts = Post::all(); // fetch all posts for this tenant
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }
}
