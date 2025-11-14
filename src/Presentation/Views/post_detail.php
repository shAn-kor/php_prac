<?php ob_start(); ?>
<div style="margin-bottom: 20px;">
    <a href="/" class="btn">목록</a>
    <?php if (isset($_SESSION['user_id']) && $post && $post['user_id'] == $_SESSION['user_id']): ?>
        <a href="?action=edit&id=<?= $post['id'] ?>" class="btn">수정</a>
        <form method="POST" action="?action=delete&id=<?= $post['id'] ?>" style="display: inline;">
            <button type="submit" class="btn btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
        </form>
    <?php endif; ?>
</div>

<?php if ($post): ?>
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <p><strong>작성자:</strong> <?= htmlspecialchars($post['author']) ?></p>
    <p><strong>작성일:</strong> <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></p>
    <hr>
    <div style="white-space: pre-wrap;"><?= htmlspecialchars($post['content']) ?></div>
<?php else: ?>
    <p>게시글을 찾을 수 없습니다.</p>
<?php endif; ?>
<?php $content = ob_get_clean(); include 'layout.php'; ?>