<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Kozina 게시판</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    <link href="/css/styles.css" rel="stylesheet" />
    <style>
        .post-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .post-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #f4623a;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
        }
        .post-title:hover {
            color: #e55a36;
        }
        .post-content {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .post-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #868e96;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .comment-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #f4623a;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .login-form {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 50px auto;
        }
        .btn {
            background: #f4623a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn:hover {
            background: #e55a36;
            transform: translateY(-2px);
            color: white;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        input, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #f4623a;
        }
        textarea {
            height: 200px;
            resize: vertical;
        }
        .error {
            color: #dc3545;
            margin: 15px 0;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
        }
        .main-content {
            min-height: calc(100vh - 200px);
        }
        .divider {
            height: 0.25rem;
            max-width: 3.25rem;
            border: none;
            background-color: #f4623a;
            opacity: 1;
            margin: 1.5rem auto;
        }
        .divider-light {
            background-color: #fff;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
        }
        .navbar-nav .nav-link:hover {
            color: #f4623a !important;
        }
        .btn-xl {
            padding: 1.25rem 2.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            border-radius: 10rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(244, 98, 58, 0.25);
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #dee2e6;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body id="page-top">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="/">Laravel 게시판</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><span class="nav-link"><?= htmlspecialchars($_SESSION['username']) ?>님</span></li>
                        <li class="nav-item"><a class="nav-link" href="?action=create">글쓰기</a></li>
                        <li class="nav-item"><a class="nav-link" href="?action=logout">로그아웃</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="?action=login">로그인</a></li>
                        <li class="nav-item"><a class="nav-link" href="?action=register">회원가입</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div style="padding-top: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); background-attachment: fixed; min-height: 100vh;">
        <div class="container px-4 px-lg-5">
            <?php echo $content; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-5">
        <div class="container px-4 px-lg-5">
            <div class="small text-center text-muted">Copyright &copy; 2024 - Laravel 게시판</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/scripts.js"></script>
</body>
</html>