@extends('layouts.app')

@section('content')
<h2>글 수정</h2>

<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label>제목</label>
        <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
    </div>
    
    <div class="form-group">
        <label>작성자</label>
        <input type="text" name="author" value="{{ old('author', $post->author) }}" required>
    </div>
    
    <div class="form-group">
        <label>내용</label>
        <textarea name="content" required>{{ old('content', $post->content) }}</textarea>
    </div>
    
    <button type="submit" class="btn">수정</button>
    <a href="{{ route('posts.show', $post) }}" class="btn" style="background: #6c757d;">취소</a>
</form>
@endsection