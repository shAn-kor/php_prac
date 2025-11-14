<?php ob_start(); ?>
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="header">
        <h1>게시판</h1>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['username']) ?>님 환영합니다! 
            <a href="?action=logout" class="btn" style="background: #6c757d;">로그아웃</a>
        </div>
    </div>
    
    <div style="margin-bottom: 20px;">
        <a href="?action=create" class="btn">글쓰기</a>
    </div>
<?php else: ?>
    <div class="header">
        <h1>게시판</h1>
        <div>
            <a href="?action=login" class="btn">로그인</a>
            <a href="?action=register" class="btn btn-success">회원가입</a>
        </div>
    </div>
<?php endif; ?>

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
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= $post['id'] ?></td>
            <td><a href="?action=show&id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
            <td><?= htmlspecialchars($post['author']) ?></td>
            <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include 'layout.php'; ?>