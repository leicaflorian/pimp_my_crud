@extends('layouts.app')

@section('title', '{{ pageTitle }}')

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <a href="{{ route('{{ resource }}.index') }}" class="btn btn-link btn-sm me-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="1em" height="1em">
            <path fill="currentColor"
                  d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
          </svg>
        </a>
        {{ pageTitle }}
      </div>

      <div class="card-body">

        <form action="{{ route('{{ resource }}.store') }}" method="POST"
              enctype="multipart/form-data">
          @csrf()

          {{ formInputs }}

          <a href="{{ route('{{ resource }}.index') }}" class="btn btn-secondary">Cancel</a>
          <button class="btn btn-success">Save</button>
        </form>

      </div>
    </div>
  </div>

@endsection
