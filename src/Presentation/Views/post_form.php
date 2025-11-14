<?php ob_start(); ?>
<h2><?= $post ? '글 수정' : '글쓰기' ?></h2>

<form method="POST" action="?action=<?= $post ? 'update&id=' . $post['id'] : 'store' ?>">
    <div class="form-group">
        <label>제목</label>
        <input type="text" name="title" value="<?= $post ? htmlspecialchars($post['title']) : '' ?>" required>
    </div>
    
    <div class="form-group">
        <label>내용</label>
        <textarea name="content" required><?= $post ? htmlspecialchars($post['content']) : '' ?></textarea>
    </div>
    
    <button type="submit" class="btn"><?= $post ? '수정' : '저장' ?></button>
    <a href="<?= $post ? '?action=show&id=' . $post['id'] : '/' ?>" class="btn" style="background: #6c757d;">취소</a>
</form>
<?php $content = ob_get_clean(); include 'layout.php'; ?>