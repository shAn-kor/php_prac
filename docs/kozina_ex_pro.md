# Kozina Laravel Project

검토자 및 작성자 : 최현호  
개발자 : 안성훈
--- 

### 환경  
Laravel (php)  
Nginx  
Mysql  

---

### 기능 요구사항
1. 로그인  
2. 게시판
3. 게시판 댓글
4. 게시글 첨부 파일 등록
5. 모던 블로그 형식의 디자인(https://startbootstrap.com/theme/creative)

---

### 비기능적 요구사항
1. 세션 유지 기능(이중화를 생각해서 Session Clustering) --> 테스트만 진행
2. 기본적인 보안(injection, XSS, CSRF)
3. 첨부파일 filtering
4. 이중화 테스트
5. 일일 500 유저 부하 테스트

---
- 화면 디자인 중요  
- 일부 기능 및 비기능 요구사항에 대해서는 php 테스트 코드를 통해 커버리지 70%로 작성 완료할 것 --> 유닛 테스트만 작성  
- 테스트 코드 작성 예시
```
php artisan make:test UserLoginTest
php artisan make:test UserServiceTest --unit
```
- PHPUnit(테스트 툴) 사용할 것

- 비기능적 요구사항 4, 5번에 대해서는 보고서 작성
- 전체 통합 테스트를 FE에서 직접 조작하면서 영상 녹화 후, Youtube에 업로드 할 것  

--- 
#### 참고

> 1- 라라벨이 기본으로 해주는 보안들  
라라벨 코어 기준:  
✅ 비밀번호 해싱: bcrypt/argon2 등으로 자동 해싱  
✅ CSRF 방어: VerifyCsrfToken 미들웨어 기본 활성화  
✅ 세션 하이재킹 완화: 로그인 시 세션 재생성  
✅ “remember me” 토큰: 암호화 + DB 저장  
✅ Rate Limiting: ThrottleRequests 미들웨어로 로그인 시도 제한 가능  
라라벨 방식(auth scaffolding)으로 만들면 보안 기본은 꽤 잘 깔려 있음  
> 2- “로그인/회원가입/비번 초기화” 자동으로 해주는 패키지  
라라벨에서 로그인 관련 UI/로직을 “자동으로 틀짜기” 해주는 패키지가 있음  
>
> 2-1. Laravel Breeze (가장 깔끔한 기본 템플릿)  
라라벨 공식 미니 스캐폴딩  
이메일/비밀번호 기반 로그인, 회원가입, 비밀번호 재설정, 이메일 인증, 로그아웃까지 세트로 제공  
Tailwind + Blade/Inertia/React/Vue 등 선택 가능  
심플해서 커스터마이징하기 좋음  

```
composer require laravel/breeze --dev
php artisan breeze:install
php artisan migrate
npm install && npm run dev
```

> 바로 로그인/회원가입 화면 + 기본 보안 로직 다 깔림.  
>
> 2-2. Laravel Jetstream (좀 더 풀 패키지)  
회원가입, 로그인, 이메일 인증, 비밀번호 재설정  
✅ 2FA(2단계 인증, OTP) 지원  
✅ 세션 관리(다른 브라우저에서 로그인한 세션 로그아웃 등)  
✅ 팀 기능(팀 기반 권한 관리)  
UI는 Livewire/ Inertia.js 중 선택  
규모 좀 있는 서비스면 Jetstream이 스프링 시큐리티 + 일부 OAuth 확장 느낌이랑 비슷함.  
>
>2-3. Laravel Fortify (백엔드 전용 보안 엔진)  
UI 안 붙어 있는 “로그인/회원가입/2FA 백엔드 엔진” 같은 역할  
Jetstream/Breeze에서 내부적으로 쓰기도 함  
본인만의 SPA/프론트(React/Vue/Next.js) 쓰면서 API 방식 로그인 만들고 싶을 때 유용  
>
> 2-4. Laravel Socialite (소셜 로그인)  
구글/네이버/카카오/깃헙 같은 소셜 로그인은 이걸로 처리  
OAuth 리다이렉트/콜백/토큰 등 처리 대부분 해줌  