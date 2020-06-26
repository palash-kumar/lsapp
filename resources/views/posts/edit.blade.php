@extends('layouts.app')

@section('content')
    <h1>Create Post</h1>

    {!! Form::open(['action'=>['PostsController@update', $post->id], 'method'=>'POST']) !!}
        <div class="fom-group">
            {{Form::label('title', 'Title')}}
            {{Form::text('title', $post->title, ['class'=>'form-control', 'placeholder'=>'Title'])}}
        </div>
        <div class="fom-group">
            {{Form::label('body', 'Body')}}
            {{Form::textarea('body', $post->body, ['id'=>'article-ckeditor','class'=>'form-control', 'placeholder'=>'Body text'])}}
        </div>
        <div class="fom-group">
            {{Form::file('cover_image')}}
        </div>
        {{Form::hidden('_method','PUT')}}
        {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
    {!! Form::close() !!}
    
@endsection