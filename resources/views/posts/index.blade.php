@extends('layouts.app')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('posts.create') }}" class="btn">글쓰기</a>
</div>

<table>
    <thead>
        <tr>
            <th>번호</th>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->id }}</td>
            <td><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></td>
            <td>{{ $post->author }}</td>
            <td>{{ $post->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $posts->links() }}
@endsection