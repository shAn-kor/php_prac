<?php ob_start(); ?>
<div class="login-form">
    <h2>로그인</h2>
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
        <button type="submit" class="btn">로그인</button>
        <a href="?action=register" class="btn btn-success">회원가입</a>
    </form>
    <p style="margin-top: 20px; font-size: 12px; color: #666;">
        테스트 계정: admin / password 또는 user1 / password
    </p>
</div>
<?php $content = ob_get_clean(); include 'layout.php'; ?>