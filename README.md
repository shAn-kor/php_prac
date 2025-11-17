# Laravel 게시판

PHP Laravel 기반의 기본 웹 게시판입니다.

## 설치 및 실행

### Docker 사용 (권장)

1. **MySQL 컨테이너 실행**
```bash
docker-compose up -d
```

2. **의존성 설치**
```bash
composer install
```

3. **서버 실행**
```bash
php artisan serve
```

### 수동 설치

1. **의존성 설치**
```bash
composer install
```

2. **환경 설정**
```bash
cp .env.example .env
php artisan key:generate
```

3. **데이터베이스 설정**
- MySQL에서 `board` 데이터베이스 생성
- `.env` 파일에서 데이터베이스 정보 수정

4. **마이그레이션 실행**
```bash
php artisan migrate
```

5. **서버 실행**
```bash
php artisan serve
```

## 기능

- 게시글 목록 조회
- 게시글 작성
- 게시글 상세 보기
- 게시글 수정
- 게시글 삭제
- 페이지네이션

## 데이터베이스 구조

### users 테이블
- id (Primary Key)
- username (사용자명)
- password (비밀번호)
- created_at (생성일)

### posts 테이블
- id (Primary Key)
- title (제목)
- content (내용)
- author (작성자)
- user_id (작성자 ID)
- created_at (작성일)
- updated_at (수정일)

### comments 테이블
- id (Primary Key)
- post_id (게시글 ID)
- content (내용)
- author (작성자)
- user_id (작성자 ID)
- created_at (작성일)
- updated_at (수정일)

### attachments 테이블
- id (Primary Key)
- post_id (게시글 ID)
- original_name (원본 파일명)
- stored_name (저장된 파일명)
- file_path (파일 경로)
- file_size (파일 크기)
- mime_type (MIME 타입)
- created_at (생성일)

## 테스트

### 유닛 테스트 실행
```bash
./run-tests.sh
```

### 부하 테스트 실행
```bash
./load-test.sh
```

### 이중화 테스트
```bash
docker-compose -f docker-compose.cluster.yml up -d
```

## 보안 기능

- **CSRF 방어**: 모든 폼에 토큰 적용
- **XSS 방어**: 입출력 데이터 필터링
- **파일 업로드 보안**: MIME 타입, 확장자, 크기 제한
- **SQL Injection 방어**: PDO Prepared Statement 사용
- **세션 보안**: Redis 기반 세션 클러스터링