<?php ob_start(); ?>
<div class="form-container">
    <h2><?= $post ? '글 수정' : '글쓰기' ?></h2>
    
    <form method="POST" action="?action=<?= $post ? 'update&id=' . $post['id'] : 'store' ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \Infrastructure\Security\SecurityHelper::generateCSRFToken() ?>">
    <div class="form-group">
        <label>제목</label>
        <input type="text" name="title" value="<?= $post ? htmlspecialchars($post['title']) : '' ?>" required>
    </div>
    
    <div class="form-group">
        <label>내용</label>
        <textarea name="content" required><?= $post ? htmlspecialchars($post['content']) : '' ?></textarea>
    </div>
    
    <?php if (!$post): ?>
    <div class="form-group">
        <label>첨부파일</label>
        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
        <small style="color: #666; font-size: 12px;">최대 10MB, 이미지/문서 파일만 업로드 가능</small>
    </div>
    <?php endif; ?>
    
        <button type="submit" class="btn"><?= $post ? '수정' : '저장' ?></button>
        <a href="<?= $post ? '?action=show&id=' . $post['id'] : '/' ?>" class="btn" style="background: #6c757d;">취소</a>
    </form>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>