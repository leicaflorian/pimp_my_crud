@extends('layouts.app')

@section('title', "Post #$post->id")

@section('content')
  <div class="bg-light border-bottom mb-4">
    <div class="container">
      <div class="nav justify-content-center">
        <a class="nav-link" href="{{ route('posts.edit', $post->id) }}">Edit</a>
        <a class="nav-link" href="{{ route('posts.destroy', $post->id) }}">Delete</a>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <a href="{{ route('posts.index') }}" class="btn btn-link btn-sm me-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="1em" height="1em">
            <path fill="currentColor"
                  d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
          </svg>
        </a>
        Post #{{ $post->id }}
      </div>

      <div class="card-body">

        <div><strong>Id:</strong> {{ $post->id }}</div>

          <div><strong>Title:</strong> {{ $post->title }}</div>

          <div><strong>Content:</strong> {{ $post->content }}</div>

          <div><strong>Cover img:</strong> {{ $post->cover_img }}</div>

          <div><strong>Public:</strong> {{ $post->public }}</div>

          <div><strong>Status:</strong> {{ $post->status }}</div>

          <div><strong>Created at:</strong> {{ $post->created_at }}</div>

          <div><strong>Updated at:</strong> {{ $post->updated_at }}</div>

          <div><strong>User id:</strong> {{ $post->user_id }}</div>

          <div><strong>Category id:</strong> {{ $post->category_id }}</div>

      </div>
    </div>
  </div>

@endsection
