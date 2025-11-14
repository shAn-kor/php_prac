<?php ob_start(); ?>
<div class="login-form">
    <h2>회원가입</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>아이디</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>비밀번호</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">회원가입</button>
        <a href="?action=login" class="btn" style="background: #6c757d;">로그인</a>
    </form>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>