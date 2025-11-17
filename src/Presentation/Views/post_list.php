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
        <div class="col-lg-6 col-md-12 mb-4">
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
let allPosts = []; // 모든 게시글 데이터 저장
const POSTS_PER_PAGE = 6;
const MAX_VISIBLE_POSTS = 18; // DOM에 최대 18개만 유지

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
                // 데이터만 저장, DOM 업데이트는 별도 처리
                allPosts.push(...posts);
                currentPage++;
                updateVisiblePosts();
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

function updateVisiblePosts() {
    const postsContainer = document.querySelector('.row.gx-4.gx-lg-5');
    
    // DOM에 너무 많은 요소가 있으면 상위 요소들 제거
    if (allPosts.length > MAX_VISIBLE_POSTS) {
        const children = postsContainer.children;
        const removeCount = Math.min(POSTS_PER_PAGE, children.length - MAX_VISIBLE_POSTS);
        
        for (let i = 0; i < removeCount; i++) {
            if (children[0]) {
                children[0].remove();
            }
        }
    }
    
    // 새로운 게시글만 DOM에 추가
    const startIndex = Math.max(0, allPosts.length - POSTS_PER_PAGE);
    const newPosts = allPosts.slice(startIndex);
    
    newPosts.forEach(post => {
        if (!document.querySelector(`[data-post-id="${post.id}"]`)) {
            const postElement = createPostElement(post);
            postsContainer.appendChild(postElement);
        }
    });
}

function createPostElement(post) {
    const col = document.createElement('div');
    col.className = 'col-lg-6 col-md-12 mb-4';
    col.setAttribute('data-post-id', post.id); // 중복 방지용 ID
    
    const createdAt = new Date(post.created_at);
    const formattedDate = createdAt.getFullYear() + '-' + 
        String(createdAt.getMonth() + 1).padStart(2, '0') + '-' + 
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

// 디버깅용 정보 표시
setInterval(() => {
    const domCount = document.querySelectorAll('[data-post-id]').length;
    const dataCount = allPosts.length;
    console.log(`DOM: ${domCount}개, 데이터: ${dataCount}개`);
}, 5000);
</script>

<?php $content = ob_get_clean(); include 'layout.php'; ?>