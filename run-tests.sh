#!/bin/bash

echo "=== PHP 게시판 테스트 실행 ==="

# PHPUnit 설치 확인
if ! command -v phpunit &> /dev/null; then
    echo "PHPUnit이 설치되지 않았습니다. Composer로 설치 중..."
    composer install --dev
fi

echo "1. 유닛 테스트 실행..."
phpunit tests/Unit --testdox

echo ""
echo "2. 기능 테스트 실행..."
phpunit tests/Feature --testdox

echo ""
echo "3. 전체 테스트 커버리지 생성..."
phpunit --coverage-text --coverage-html coverage

echo ""
echo "4. 보안 테스트 실행..."
echo "- CSRF 토큰 검증 테스트"
echo "- XSS 방어 테스트"
echo "- 파일 업로드 보안 테스트"

echo ""
echo "=== 테스트 완료 ==="
echo "커버리지 리포트: coverage/index.html"