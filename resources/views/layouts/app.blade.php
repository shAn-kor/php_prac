<!DOCTYPE html>
<html>
<head>
    <title>게시판</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .btn { padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .btn-danger { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>게시판</h1>
        @yield('content')
    </div>
</body>
</html>