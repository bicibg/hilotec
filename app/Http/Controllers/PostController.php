<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest()->get();

        return view('pages.posts.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('pages.posts.show', compact('post'));
    }
}
