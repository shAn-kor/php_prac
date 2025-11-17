<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="login-form">
            <div class="text-center mb-4">
                <i class="bi-person-circle fs-1 text-primary mb-3"></i>
                <h2 class="fw-bold text-primary">로그인</h2>
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
                           placeholder="아이디를 입력하세요" 
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
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi-box-arrow-in-right"></i> 로그인
                    </button>
                    <a href="?action=register" class="btn btn-outline-success btn-lg">
                        <i class="bi-person-plus"></i> 회원가입
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>