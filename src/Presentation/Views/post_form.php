<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="form-container">
            <div class="text-center mb-4">
                <h2 class="display-6 fw-bold text-primary">
                    <i class="bi-pencil-square"></i> 
                    <?= $post ? '글 수정' : '새 글 작성' ?>
                </h2>
                <hr class="divider" />
            </div>
            
            <form method="POST" action="?action=<?= $post ? 'update&id=' . $post['id'] : 'store' ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= \Infrastructure\Security\SecurityHelper::generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="bi-card-heading"></i> 제목
                    </label>
                    <input type="text" name="title" class="form-control form-control-lg" 
                           value="<?= $post ? htmlspecialchars($post['title']) : '' ?>" 
                           placeholder="제목을 입력하세요..." 
                           maxlength="200" 
                           oninput="this.value = this.value.replace(/[';\"\\`]/g, '')" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="bi-card-text"></i> 내용
                    </label>
                    <textarea name="content" class="form-control" rows="12" 
                              placeholder="내용을 입력하세요..." 
                              maxlength="5000" 
                              oninput="this.value = this.value.replace(/[';\"\\`]/g, '')" required><?= $post ? htmlspecialchars($post['content']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="bi-paperclip"></i> 첨부파일
                    </label>
                    <?php if ($post): ?>
                        <?php 
                        $attachments = [];
                        if (isset($_GET['id'])) {
                            $attachmentService = new \Domain\Service\AttachmentService(new \Infrastructure\Repository\AttachmentRepository());
                            $attachmentEntities = $attachmentService->getAttachmentsByPostId((int)$_GET['id']);
                            foreach ($attachmentEntities as $attachment) {
                                $attachments[] = [
                                    'id' => $attachment->getId(),
                                    'original_name' => $attachment->getOriginalName()
                                ];
                            }
                        }
                        ?>
                        <?php if (!empty($attachments)): ?>
                            <div class="mb-2">
                                <small class="text-muted">기존 첨부파일:</small>
                                <?php foreach ($attachments as $attachment): ?>
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-1">
                                        <span><?= htmlspecialchars($attachment['original_name']) ?></span>
                                        <label class="form-check-label">
                                            <input type="checkbox" name="delete_attachments[]" value="<?= $attachment['id'] ?>" class="form-check-input me-1">
                                            삭제
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <input type="file" name="attachments[]" class="form-control" multiple 
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                    <div class="form-text">
                        <i class="bi-info-circle"></i> 최대 10MB, 이미지/문서 파일만 업로드 가능
                        <?php if ($post): ?>
                            <br><small>새 파일을 선택하면 기존 파일에 추가됩니다.</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-md-2">
                        <i class="bi-check-circle"></i> <?= $post ? '수정 완료' : '게시하기' ?>
                    </button>
                    <a href="<?= $post ? '?action=show&id=' . $post['id'] : '/' ?>" class="btn btn-secondary btn-lg">
                        <i class="bi-x-circle"></i> 취소
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>