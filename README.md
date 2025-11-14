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

### posts 테이블
- id (Primary Key)
- title (제목)
- content (내용)
- author (작성자)
- created_at (작성일)
- updated_at (수정일)