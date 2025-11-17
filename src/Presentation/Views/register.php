<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="login-form">
            <div class="text-center mb-4">
                <i class="bi-person-plus-fill fs-1 text-success mb-3"></i>
                <h2 class="fw-bold text-success">회원가입</h2>
                <hr class="divider" />
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= \Infrastructure\Security\SecurityHelper::generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="bi-person"></i> 아이디
                    </label>
                    <input type="text" name="username" class="form-control form-control-lg" 
                           placeholder="사용할 아이디를 입력하세요" 
                           pattern="[a-zA-Z0-9_]+" title="영문, 숫자, 언더스코어만 사용 가능" 
                           maxlength="20" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="bi-lock"></i> 비밀번호
                    </label>
                    <input type="password" name="password" class="form-control form-control-lg" 
                           placeholder="비밀번호를 입력하세요" 
                           maxlength="50" required>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi-person-check"></i> 회원가입
                    </button>
                    <a href="?action=login" class="btn btn-outline-secondary btn-lg">
                        <i class="bi-arrow-left"></i> 로그인으로 돌아가기
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>