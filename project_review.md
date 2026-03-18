# 프로젝트 리뷰 및 보완 필요 사항 (Project Review & Improvements)

현재 프로젝트의 구조와 작성된 코드(마이그레이션, 라우트, 뷰 등)를 분석한 결과, 기본 뼈대(Laravel Breeze + Vue/Inertia)와 데이터베이스 설계는 진행되었으나, 실제 백엔드 로직과 프론트엔드의 데이터 연동이 누락된 초기 개발 단계로 보입니다. 

다음은 프로젝트를 완성하기 위해 보완하고 추가해야 할 핵심 영역들입니다.

---

## 1. 데이터베이스 모델 및 관계 설정 (Eloquent Models)
현재 커스텀 테이블(`posts`, `tiers`, `point_histories` 등)에 대한 마이그레이션 파일은 존재하지만, 이를 ORM으로 다룰 **Model 클래스가 없습니다.**

* **필요 작업:**
  * `Post`, `Tier`, `PointHistory` 모델 생성 (`php artisan make:model`)
  * `User` 모델 포함 각 모델 간의 관계(Relationships) 정의
    * `User` ↔ `Post` (1:N)
    * `User` ↔ `Tier` (N:1)
    * `User` ↔ `PointHistory` (1:N)
  * `$fillable` (대량 할당 허용) 및 `$casts` (예: `posts.summary` json 캐스팅 등) 속성 설정

## 2. 컨트롤러(Controllers) 및 라우팅 파라미터 연동
현재 `routes/web.php`를 보면 `Home`, `Popular`, `Bookmarks` 등의 페이지가 컨트롤러 없이 단순히 `Inertia::render()` 만을 호출하고 있어 **실제 DB 데이터가 프론트엔드로 전달되지 않습니다.**

* **필요 작업:**
  * `PostController`, `HomeController` 등 목적에 맞는 컨트롤러 생성
  * 데이터베이스에서 게시글 등의 데이터를 조회하여 Inertia 뷰로 전달 (`Inertia::render('Home', ['posts' => $posts])`)
  * API / Form Submit 처리를 위한 `store`, `update`, `destroy` 라우트 및 로직 구현

## 3. 유효성 검사 및 폼 리퀘스트 (Form Requests)
사용자로부터 데이터를 입력받는 부분(게시글 작성, 정보 수정 등)에 대한 **서버 사이드 유효성 검증(Validation)**이 필요합니다.

* **필요 작업:**
  * `StorePostRequest`, `UpdatePostRequest` 등의 Form Request 클래스 생성
  * 데이터 타입, 길이 제한, 필수 여부 등을 백엔드에서 강력하게 필터링

## 4. 인가 및 권한 관리 (Authorization & Security)
게시글 수정/삭제 시 **본인 여부를 확인**하거나, 특정 작업을 **관리자만 수행**할 수 있도록 하는 권한 제어가 필요합니다.

* **필요 작업:**
  * `PostPolicy` 등 생성하여, 글 작성자만 수정 및 삭제 가능하도록 `Gate`나 `Policy` 적용
  * 사용자의 밴(ban) 상태(`is_banned`)를 체크하는 미들웨어(Middleware) 추가 및 로그인 차단 로직 구현

## 5. 비즈니스 로직 - 포인트 및 티어 시스템 구축
마이그레이션을 보면 사용자에게 포인트와 티어를 부여하는 시스템이 기획되어 있습니다. 그러나 이를 처리할 비즈니스 로직(Service 계층)이 없습니다.

* **필요 작업:**
  * 액션(게시글 작성, 좋아요 등) 발생 시 포인트를 적립/차감하는 서비스 클래스 구현
  * `point_histories` 테이블에 내역 기록 연동
  * 포인트 도달 시 자동으로 티어를 승급/강등시키는 로직 구현 (Event / Listener 또는 Observer 활용)

## 6. 프론트엔드 (Vue/Inertia) 연동 및 UI 보완
프론트엔드 페이지 파일은 존재하지만, 실제 백엔드에서 넘겨주는 **동적 데이터를 받아 화면에 그리는 처리(Props 연동)**가 누락되어 있을 가능성이 큽니다.

* **필요 작업:**
  * Vue 컴포넌트 내에서 `defineProps()`를 통해 백엔드에서 전달된 데이터 수신
  * 동적 데이터 렌더링 (리스트 맵핑, 페이지네이션 구현)
  * 게시글 작성/수정 시 폼 전송에 `@inertiajs/vue3`의 `useForm` 활용 및 에러 핸들링

## 7. 더미 데이터 세팅 (Factories & Seeders)
개발 및 테스트를 원활하게 진행하기 위해 초기 데이터를 자동으로 채워주는 기능이 필요합니다.

* **필요 작업:**
  * `PostFactory`, `TierFactory` 생성
  * `DatabaseSeeder`에 호출 로직을 추가하여 `php artisan migrate --seed` 명령 한 번으로 개발 환경 세팅이 완료되도록 구성

## 8. 테스트 코드 작성 (Testing)
기능의 안정성을 보장하기 위해 핵심 기능에 대한 자동화된 테스트가 뒷받침되어야 합니다. (현재 Laravel 패키지에 Pest 또는 PHPUnit이 포함되어 있습니다.)

* **필요 작업:**
  * 글 작성, 권한 체크, 포인트 적립 로직 등에 대한 Feature 및 Unit 테스트 작성
