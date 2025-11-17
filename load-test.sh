#!/bin/bash

echo "=== 부하 테스트 실행 ==="

# Apache Bench 설치 확인
if ! command -v ab &> /dev/null; then
    echo "Apache Bench가 설치되지 않았습니다."
    echo "macOS: brew install httpd"
    echo "Ubuntu: sudo apt-get install apache2-utils"
    exit 1
fi

BASE_URL="http://localhost:8080"

echo "1. 게시글 목록 조회 테스트 (50 동시 사용자, 1000 요청)"
ab -n 1000 -c 50 $BASE_URL/

echo ""
echo "2. 게시글 상세 조회 테스트 (50 동시 사용자, 500 요청)"
ab -n 500 -c 50 $BASE_URL/?action=show&id=1

echo ""
echo "3. 로그인 페이지 접근 테스트 (25 동시 사용자, 500 요청)"
ab -n 500 -c 25 $BASE_URL/?action=login

echo ""
echo "4. 이중화 테스트 시작..."
echo "클러스터 환경 시작 중..."
docker-compose -f docker-compose.cluster.yml up -d

sleep 10

echo "클러스터 환경에서 부하 테스트..."
ab -n 500 -c 25 $BASE_URL/

echo ""
echo "5. 장애 시뮬레이션..."
echo "PHP 인스턴스 1개 중단..."
docker stop board_php1

echo "장애 상황에서 부하 테스트..."
ab -n 200 -c 10 $BASE_URL/

echo "인스턴스 복구..."
docker start board_php1

echo ""
echo "=== 부하 테스트 완료 ==="