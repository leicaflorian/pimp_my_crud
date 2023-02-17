<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Category;

class PostController extends Controller {
  /**
   * Display a listing of the resource.
   *
   * @return View
   */
  public function index(): View {
    $posts = Post::all();
    
    return view('posts.index', compact("posts"));
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return View
   */
  public function create(): View {
    $categories = Category::all();

    return view('posts.create', ['categories' => $categories]);
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  Request  $request
   *
   * @return RedirectResponse
   */
  public function store(Request $request): RedirectResponse {
    $data = $request->all();
    $post = Post::create($data);
    
    return redirect()->route('posts.show', $post->id);
  }
  
  /**
   * Display the specified resource.
   *
   * @param Post $post
   *
   * @return View
   */
  public function show(Post $post): View {
    return view('posts.show', compact("post"));
  }
  
  /**
   * Show the form for editing the specified resource.
   *
   * @param  Post  $post
   *
   * @return View
   */
  public function edit(Post $post): View {
    $categories = Category::all();

    return view('posts.edit', [
      "post" => $post,
      'categories' => $categories
    ]);
  }
  
  /**
   * Update the specified resource in storage.
   *
   * @param  Request $request
   * @param  Post $post
   *
   * @return RedirectResponse
   */
  public function update(Request $request, Post $post): RedirectResponse {
    $data = $request->all();
    
    $post->update($data);
    
    return redirect()->route('posts.show', $post->id);
  }
  
  /**
   * Remove the specified resource from storage.
   *
   * @param  Post  $post
   *
   * @return RedirectResponse
   */
  public function destroy(Post $post): RedirectResponse {
    $post->destroy();
    
    return redirect()->route('posts.index');
  }
}
