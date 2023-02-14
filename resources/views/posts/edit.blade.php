@extends('layouts.app')

@section('title', "Post #$post->id | Edit")

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <a href="{{ route('posts.index') }}" class="btn btn-link btn-sm me-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="1em" height="1em">
            <path fill="currentColor"
                  d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
          </svg>
        </a>
        Post #{{ $post->id }} | Edit
      </div>

      <div class="card-body">

        <form action="{{ route('posts.update', $post->id) }}" method="POST"
              enctype="multipart/form-data">
          @csrf()
          @method('PUT')

          <div class="mb-3">
            <label class="form-label" for="input_title">Title</label>
            <input type="text"
                   class="form-control @error('title') is-invalid @enderror"
                   name="title"
                   id="input_title"
                   value="{{ old('title', $post->title) }}">
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="input_content">Content</label>
            <input type="text"
                   class="form-control @error('content') is-invalid @enderror"
                   name="content"
                   id="input_content"
                   value="{{ old('content', $post->content) }}">
            @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="input_cover_img">Cover img</label>
            <input type="text"
                   class="form-control @error('cover_img') is-invalid @enderror"
                   name="cover_img"
                   id="input_cover_img"
                   value="{{ old('cover_img', $post->cover_img) }}">
            @error('cover_img')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="input_public">Public</label>
            <input type="text"
                   class="form-control @error('public') is-invalid @enderror"
                   name="public"
                   id="input_public"
                   value="{{ old('public', $post->public) }}">
            @error('public')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="input_category_id">Category id</label>
            <input type="text"
                   class="form-control @error('category_id') is-invalid @enderror"
                   name="category_id"
                   id="input_category_id"
                   value="{{ old('category_id', $post->category_id) }}">
            @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
          <button class="btn btn-success">Save</button>
        </form>

      </div>
    </div>
  </div>

@endsection
