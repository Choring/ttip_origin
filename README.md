기획서
ttip (띱) - 나만의 노하우 공유 커뮤니티
"유튜브보다 빠른 정보 습득, 3줄 요약으로 끝내는 진짜 노하우"
    - ttip은 정보의 홍수 속에서 핵심만 빠르게 소비하고 싶은 1040 세대를 위한 지식 공유 플랫폼입니다.

Core Values (핵심 가치)
    - 시간 단축 (Time-Saving): 모든 게시글은 '3줄 요약' 작성을 강제화하여 사용자들의 정보 습득 시간을 획기적으로 줄입니다.
    - 신뢰와 보상 (Trust & Reward): 양질의 정보를 제공하는 작성자에게 합당한 포인트와 등급(Tier)을 부여하여 자생적인 생태계를 구축합니다.
    - 바이럴 루프 (Viral Loop): 단순 링크가 아닌 '카드 뉴스 이미지' 자동 생성을 통해 SNS 공유를 유도하고 자연스러운 신규 유입을 창출합니다.

Tech Stack (기술 스택)
    - Backend: PHP (Laravel 11)
    - Frontend: Vue.js 3, Inertia.js (The Modern Monolith 구조)
    - Architecture: MVC + Service Layer Patter

Key Features (주요 기능)
1. 3줄 요약 강제화 및 검색 최적화
    - 게시글 작성 시 3줄 요약 필드 입력을 필수로 제한합니다.
    - 검색 알고리즘에서 '3줄 요약' 내용과 일치하는 항목에 1.5배의 가중치를 부여하여 최상단에 노출합니다.

2. 정밀한 포인트 산정 및 등급 엔진
    - 활동에 따른 정교한 포인트 계산 수식을 적용합니다.
    - $P_{total} = P_{base} + (Like \times W_1) + (Share \times W_2)$
    - 포인트 누적량에 따라 5단계의 등급(Tier) 및 전용 뱃지를 동적으로 부여합니다.

3. 현상금 에스크로 (Bounty Escrow) 시스템
    - 질문 작성 시 포인트를 선결제(시스템 홀딩)하여 '먹튀'를 원천 차단합니다.
    - 7일 경과 시 '좋아요' 수가 가장 많은 답변을 스케줄러(Batch)가 자동 채택하여 답변자의 권리를 보호합니다.

4. 명예의 전당 (실시간 랭킹 Batch)
    - 서버 부하를 최소화하기 위해 매 1시간 단위로 포인트와 조회수를 합산하는 스케줄러를 가동합니다.
    - 갱신된 데이터를 기반으로 우측 사이드바에 '명예의 전당'을 노출하여 사용자들의 성취감을 자극합니다.

5. 카드 뉴스 렌더링 (예정)
    - 사용자가 게시글을 외부로 공유할 때, 3줄 요약이 포함된 카드 뉴스 이미지를 백그라운드에서 자동 합성하여 제공합니다.

Architecture Highlight
비즈니스 로직의 파편화를 막고 추후 API/App 확장을 대비하기 위해 Service Layer를 적극 도입했습니다.

    - PointService: 트랜잭션 기반의 안전한 포인트 증감 및 정산 로직 캡슐화.
    - RankingService: 스케줄러에 연동되는 랭킹 집계 로직.

1. Design Philosophy (디자인 철학)
    - ttip은 '유튜브보다 빠른 정보 습득'을 위해 최소한의 시각적 요소로 최대의 정보 전달을 목표로 합니다.
    - Summary First: 모든 정보는 '3줄 요약'을 거쳐 사용자에게 전달됩니다.
    - Clean & Fast: 복잡한 사이드바 대신 상단 네비게이션을 활용하여 콘텐츠 집중도를 높였습니다.
    - Reward Visualization: 포인트와 랭킹 시스템을 시각화하여 사용자의 기여를 가시화합니다.

2. Design System (디자인 시스템)
서비스 전반에 사용된 핵심 디자인 요소입니다.
Colors
Primary		#FF5C00	로고, 포인트 컬러, 핵심 버튼
Secondary		#00C853	3줄 요약 체크 아이콘 (✅)
Background		#F9F9F9	전체 레이아웃 배경

Typography
    - Font: Pretendard / Noto Sans KR
    - Hierarchy: 페이지 제목(28px, Bold) > 카드 제목(18px, Semi-bold) > 본문(14~16px, Regular)

3. Key UI Components (주요 UI 구성)
    - 3-Line Summary Card: 유저가 가장 먼저 만나는 정보 단위로, 제목과 함께 강제된 3줄 요약 정보를 노출합니다.
    - Smart Writing Editor: 본문 작성 전 3줄 요약을 필수 입력하게 설계된 전용 에디터입니다.
    - Success Feedback Modal: 게시글 등록 시 보상 체계를 한눈에 보여주는 인터랙티브 요소입니다.

4. UI Gallery (Screenshots)
    - Main Dashboard: ![Main](path/to/main.png)
    - Category Grid: ![Category](path/to/category.png)
    - Post Detail: ![Detail](path/to/detail.png)
    - Write Page: ![Write](path/to/write.png)

---

## 1. 사전 요구사항 (Prerequisites)

프로젝트를 구동하기 위해 아래의 환경이 "예외 없이" 준비되어야 합니다.

* **PHP:** >= 8.2 (Nginx 사용 시 **Non Thread Safe (NTS)** 버전 권장)
* **Composer:** 패키지 의존성 관리 도구
* **Node.js & npm:** 프론트엔드 에셋 빌드 도구
* **Database:** MySQL 8.0 이상 (또는 MariaDB)
* **Web Server:** Nginx 권장 (PHP-FPM 연동)

---

## 2. 설치 및 실행 방법 (Installation & Usage)

**1) 저장소 클론 및 디렉터리 이동**
\`\`\`bash
git clone [레포지토리 주소]
cd ttip
\`\`\`

**2) 백엔드 의존성 설치**
\`\`\`bash
composer install
\`\`\`

**3) 프론트엔드 의존성 설치**
\`\`\`bash
npm install
\`\`\`

**4) 환경 변수 파일 설정 및 앱 키 생성**
\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`
*(이후 아래의 '환경 변수 설정' 섹션을 참고하여 `.env` 파일의 DB 정보를 수정하세요.)*

**5) 데이터베이스 마이그레이션**
\`\`\`bash
php artisan migrate
\`\`\`

**6) 로컬 개발 서버 실행 (터미널 2개 필요)**
\`\`\`bash
# 터미널 1: 프론트엔드(Vite) 빌드 서버
npm run dev

# 터미널 2: 라라벨 백엔드 서버
php artisan serve
\`\`\`
*서버가 가동되면 브라우저에서 `http://127.0.0.1:8000`으로 접속하여 확인합니다.*

---

## 3. 환경 변수 설정 (.env)

`.env` 파일에서 데이터베이스 연결 정보를 로컬 환경에 맞게 정확히 수정해야 합니다.

\`\`\`env
APP_NAME=ttip
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ttip       # 미리 생성해둔 데이터베이스 이름
DB_USERNAME=root       # 로컬 DB 사용자명
DB_PASSWORD=           # 로컬 DB 비밀번호 (없으면 공란)
\`\`\`

---

## 4. 핵심 디렉터리 구조 (Directory Structure)

Laravel + Inertia.js 아키텍처에 기반한 주요 폴더 구조입니다.

\`\`\`text
ttip/
├── app/                    # 백엔드 핵심 로직 (Controllers, Models 등)
│   ├── Http/Controllers/   # API 및 페이지 렌더링 컨트롤러
│   └── Models/             # 데이터베이스 모델 (데이터 무결성 관리)
├── bootstrap/              # 프레임워크 부트스트랩 및 캐시 폴더
├── config/                 # 서비스 전역 설정 파일
├── database/               # 마이그레이션(스키마) 및 시더 파일
├── public/                 # 웹 서버의 Document Root (컴파일된 에셋, index.php)
├── resources/              # 프론트엔드 소스코드
│   └── js/
│       ├── Pages/          # Vue 메인 페이지 컴포넌트 (Welcome.vue 등)
│       ├── Components/     # 재사용 가능한 Vue UI 컴포넌트
│       └── app.js          # Inertia 및 Vue 앱 초기화 스크립트
├── routes/                 # 라우팅 설정 (web.php, api.php 등)
└── vite.config.js          # Vite 빌드 설정 파일
\`\`\`