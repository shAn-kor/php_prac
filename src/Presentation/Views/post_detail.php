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
    
    <!-- 댓글 섹션 -->
    <div style="margin-top: 40px;">
        <h3>댓글 (<?= count($comments) ?>)</h3>
        
        <!-- 댓글 작성 폼 -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="?action=comment_store&id=<?= $post['id'] ?>" style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <div class="form-group">
                    <textarea name="content" placeholder="댓글을 입력하세요..." required style="height: 80px;"></textarea>
                </div>
                <button type="submit" class="btn">댓글 작성</button>
            </form>
        <?php else: ?>
            <p style="padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                <a href="?action=login">로그인</a>하시면 댓글을 작성할 수 있습니다.
            </p>
        <?php endif; ?>
        
        <!-- 댓글 목록 -->
        <div style="margin-top: 20px;">
            <?php foreach ($comments as $comment): ?>
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div>
                            <strong><?= htmlspecialchars($comment['author']) ?></strong>
                            <span style="color: #666; font-size: 12px; margin-left: 10px;">
                                <?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?>
                            </span>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $comment['user_id'] == $_SESSION['user_id']): ?>
                            <div>
                                <button onclick="editComment(<?= $comment['id'] ?>)" class="btn" style="background: #28a745; font-size: 12px; padding: 4px 8px;">수정</button>
                                <form method="POST" action="?action=comment_delete&id=<?= $comment['id'] ?>" style="display: inline;">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 4px 8px;" onclick="return confirm('댓글을 삭제하시겠습니까?')">삭제</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="comment-content-<?= $comment['id'] ?>" style="white-space: pre-wrap;"><?= htmlspecialchars($comment['content']) ?></div>
                    <div id="comment-edit-<?= $comment['id'] ?>" style="display: none;">
                        <form method="POST" action="?action=comment_update&id=<?= $comment['id'] ?>">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <textarea name="content" style="height: 80px;"><?= htmlspecialchars($comment['content']) ?></textarea>
                            <div style="margin-top: 10px;">
                                <button type="submit" class="btn">수정 완료</button>
                                <button type="button" onclick="cancelEdit(<?= $comment['id'] ?>)" class="btn" style="background: #6c757d;">취소</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: #666; padding: 20px;">아직 댓글이 없습니다.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function editComment(id) {
            document.getElementById('comment-content-' + id).style.display = 'none';
            document.getElementById('comment-edit-' + id).style.display = 'block';
        }
        
        function cancelEdit(id) {
            document.getElementById('comment-content-' + id).style.display = 'block';
            document.getElementById('comment-edit-' + id).style.display = 'none';
        }
    </script>
    
<?php else: ?>
    <p>게시글을 찾을 수 없습니다.</p>
<?php endif; ?>
<?php $content = ob_get_clean(); include 'layout.php'; ?>