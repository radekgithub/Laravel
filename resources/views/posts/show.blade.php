@extends('layouts.app')

@section('content')
    <a href="/posts" class="btn btn-default">Go back</a>
    <h1>{{ $post->title }}</h1>
    <img src="/storage/cover_images/{{ $post->cover_image }}">
    <br /><br />

    <!-- to display formatted text from ckeditor need to use { !!  !! } syntax instead -->
    <div>{!! $post->body !!}</div>
    <hr>
    <small>Written on {{ $post->created_at }} by {{ $post->user->name }}</small>
    <hr>
    @if(!Auth::guest())
        @if(Auth::user()->id == $post->user_id)
            <a href="/posts/{{ $post->id }}/edit" class="btn btn-default">Edit</a>

            {!! Form::open(['action' => ['PostsController@destroy', $post->id], 'method' => 'POST', 'class' => 'pull-right']) !!}
                {{ Form::hidden('_method', 'DELETE') }}
                {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
            {!! Form::close() !!}
        @endif
    @endif
    <h3>Comments</h3>
    <p><a href="/comments/create/{{ $post->id }}" class="btn btn-success">Add Comment</a></p>
    @if( count($post->comment) > 0)
        @foreach($post->comment as $comment)
            <div class="well">
            <p><small>Written on {{ $comment->created_at }} by {{ $comment->user->name }}</small></p>
            <p>{!! $comment->body !!}</p>
            </div>
        @endforeach
    @else
        <p>There are no comments for this post yet.</p>
    @endif
@endsection