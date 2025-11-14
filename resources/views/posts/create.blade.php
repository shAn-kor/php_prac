@extends('layouts.app')

@section('content')
<h2>글쓰기</h2>

<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    <div class="form-group">
        <label>제목</label>
        <input type="text" name="title" value="{{ old('title') }}" required>
    </div>
    
    <div class="form-group">
        <label>작성자</label>
        <input type="text" name="author" value="{{ old('author') }}" required>
    </div>
    
    <div class="form-group">
        <label>내용</label>
        <textarea name="content" required>{{ old('content') }}</textarea>
    </div>
    
    <button type="submit" class="btn">저장</button>
    <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">취소</a>
</form>
@endsection