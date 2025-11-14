@extends('layouts.app')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('posts.index') }}" class="btn">목록</a>
    <a href="{{ route('posts.edit', $post) }}" class="btn">수정</a>
    <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
    </form>
</div>

<h2>{{ $post->title }}</h2>
<p><strong>작성자:</strong> {{ $post->author }}</p>
<p><strong>작성일:</strong> {{ $post->created_at->format('Y-m-d H:i') }}</p>
<hr>
<div style="white-space: pre-wrap;">{{ $post->content }}</div>
@endsection