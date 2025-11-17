<?php ob_start(); ?>
<div class="main-content">

    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="text-white font-weight-bold mb-4">우리들의 이야기</h1>
        <hr class="divider divider-light" />
        <p class="text-white-75 mb-4">자유롭게 생각을 나누고 소통하는 공간입니다.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?action=create" class="btn btn-light btn-xl">새 글 작성하기</a>
        <?php else: ?>
            <a href="?action=login" class="btn btn-light btn-xl">로그인하고 시작하기</a>
        <?php endif; ?>
    </div>

    <!-- Posts Grid -->
    <div class="row gx-4 gx-lg-5">
        <?php foreach ($posts as $post): ?>
        <div class="col-lg-6 col-md-12 mb-4" data-post-id="<?= $post['id'] ?>">
            <div class="post-card h-100">
                <a href="?action=show&id=<?= $post['id'] ?>" class="post-title">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
                <div class="post-content">
                    <?= mb_substr(strip_tags($post['content']), 0, 120) ?>...
                </div>
                <div class="post-meta">
                    <span><i class="bi-person"></i> <?= htmlspecialchars($post['author']) ?></span>
                    <span><i class="bi-calendar"></i> <?= date('m-d', strtotime($post['created_at'])) ?></span>
                    <span><i class="bi-chat-dots"></i> <?= $post['comment_count'] ?? 0 ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($posts)): ?>
    <div class="text-center py-5">
        <div class="card">
            <div class="py-5">
                <i class="bi-chat-dots fs-1 text-muted mb-4"></i>
                <h3 class="text-muted mb-3">아직 게시글이 없습니다</h3>
                <p class="text-muted mb-4">첫 번째 게시글을 작성해보세요!</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="?action=create" class="btn btn-primary">글쓰기</a>
                <?php else: ?>
                    <a href="?action=login" class="btn btn-primary">로그인</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 로딩 인디케이터 -->
    <div id="loading" class="text-center py-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">게시글을 불러오는 중...</p>
    </div>

    <!-- 더 이상 게시글이 없을 때 -->
    <div id="no-more-posts" class="text-center py-4" style="display: none;">
        <p class="text-muted">모든 게시글을 확인하셨습니다.</p>
    </div>
</div>

<script>
let currentPage = 1;
let isLoading = false;
let hasMorePosts = true;

function loadMorePosts() {
    if (isLoading || !hasMorePosts) return;
    
    isLoading = true;
    document.getElementById('loading').style.display = 'block';
    
    fetch(`?action=api_posts&page=${currentPage + 1}`)
        .then(response => response.json())
        .then(posts => {
            if (posts.length === 0) {
                hasMorePosts = false;
                document.getElementById('no-more-posts').style.display = 'block';
            } else {
                const postsContainer = document.querySelector('.row.gx-4.gx-lg-5');
                
                posts.forEach(post => {
                    // 중복 체크
                    if (!document.querySelector(`[data-post-id="${post.id}"]`)) {
                        const postElement = createPostElement(post);
                        postsContainer.appendChild(postElement);
                    }
                });
                
                currentPage++;
            }
        })
        .catch(error => {
            console.error('Error loading posts:', error);
        })
        .finally(() => {
            isLoading = false;
            document.getElementById('loading').style.display = 'none';
        });
}

function createPostElement(post) {
    const col = document.createElement('div');
    col.className = 'col-lg-6 col-md-12 mb-4';
    col.setAttribute('data-post-id', post.id);
    
    const createdAt = new Date(post.created_at);
    const formattedDate = String(createdAt.getMonth() + 1).padStart(2, '0') + '-' + 
        String(createdAt.getDate()).padStart(2, '0');
    
    col.innerHTML = `
        <div class="post-card h-100">
            <a href="?action=show&id=${post.id}" class="post-title">
                ${escapeHtml(post.title)}
            </a>
            <div class="post-content">
                ${escapeHtml(post.content.substring(0, 120))}...
            </div>
            <div class="post-meta">
                <span><i class="bi-person"></i> ${escapeHtml(post.author)}</span>
                <span><i class="bi-calendar"></i> ${formattedDate}</span>
                <span><i class="bi-chat-dots"></i> ${post.comment_count || 0}</span>
            </div>
        </div>
    `;
    
    return col;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 스크롤 이벤트 리스너
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
        loadMorePosts();
    }
});
</script>

<!-- 업로드 오류 모달 -->
<?php if (isset($_SESSION['upload_error'])): ?>
<div class="modal fade" id="uploadErrorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">파일 업로드 오류</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi-exclamation-triangle"></i>
                    <?= htmlspecialchars($_SESSION['upload_error']) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">확인</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('uploadErrorModal'));
    modal.show();
});
</script>
<?php unset($_SESSION['upload_error']); ?>
<?php endif; ?>

<?php $content = ob_get_clean(); include 'layout.php'; ?>