@extends('layouts.app')

@section('content')
    <a href="/posts" class="btn btn-secondary">Go back</a>
    <h1>{{$post->title}}</h1>
    <img class="w-100" src="/storage/cover_images/{{$post->cover_image}}">
    <div class="well">
        {!!$post->body!!}
    </div>
    <hr>
    <small>Written on {{$post->created_at}} created by {{$post->user->name}}</small>
    <hr>
    @if (!Auth::guest())
        @if (Auth::user()->id == $post->user_id)
            <a href="/posts/{{$post->id}}/edit" class="btn btn-info">Edit</a>

            {!! Form::open(['action'=>['PostsController@destroy', $post->id], 'method'=>'POST', 'class'=>'pull-right']) !!}
                {{Form::hidden('_method','DELETE')}}
                {{Form::submit('Delete',['class'=>'btn btn-danger'])}}
            {!! Form::close() !!}
        @endif
    @endif
    
@endsection