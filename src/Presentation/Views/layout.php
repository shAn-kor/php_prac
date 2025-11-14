<!DOCTYPE html>
<html>
<head>
    <title>게시판</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .btn { padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; display: inline-block; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 200px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .user-info { font-size: 14px; }
        .error { color: red; margin: 10px 0; }
        .login-form { max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $content; ?>
    </div>
</body>
</html>