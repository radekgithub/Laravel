@extends('layouts.app')

@section('content')
    <h1>Add Comment</h1>
    {!! Form::open(['action' => 'CommentsController@store', 'method' => 'POST']) !!}
    <div class="form-group">
        {{ Form::textarea('body', '', ['id' => 'article-ckeditor', 'class' => 'form-control', 'placeholder' => 'Body Text']) }}
    </div>
    {{ Form::hidden('post_id', $post->id) }}
    {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
    {!! Form::close() !!}
@endsection
