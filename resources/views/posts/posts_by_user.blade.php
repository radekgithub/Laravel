@extends('layouts.app')

@section('content')
    @if(count($posts) > 0)
        <h1>Posts by {{ $posts[0]->user->name }}</h1>
        @foreach($posts as $post)
            <div class="well">
                <h1><a href="/posts/{{ $post->id }}">{{ $post->title }}</a></h1>
                <p><small>Written on {{ $post->created_at }}</small></p>
                <p>{{ $post->body }}</p>
            </div>
        @endforeach
    @else
        <p>This user has no posts yet.</p>
    @endif
@endsection