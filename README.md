# suwon-lawyer-theme (Della Theme)

## 구조

```
della-theme/
├── .github/workflows/deploy.yml   # CI: CSS 빌드 (배포 단계는 추가 가능)
├── assets/
│   ├── css/
│   │   ├── style.css              # 원본 (PurgeCSS 대상)
│   │   ├── style.min.css          # 빌드 결과
│   │   └── critical.min.css      # 빌드 결과 (인라인용)
│   ├── js/
│   │   └── main.js                # 원본 (필요 시 main.min.js 빌드 추가)
│   └── images/
├── template-parts/
├── inc/
├── functions.php, header.php, footer.php, index.php, front-page.php, single.php, page.php, …
└── style.css                      # WP 테마 인식용 (주석만)
```

## CSS 빌드

1. **PurgeCSS** — 사용되지 않는 선택자 제거  
   `npm run purgecss`  
   → `assets/css/style.css` 덮어쓰기 (첫 실행 전 `npm install` 필요)

2. **미니파이** — 전체·크리티컬 압축  
   `php build-css.php`  
   → `assets/css/style.min.css`, `assets/css/critical.min.css` 생성 (크리티컬 구간: `/* della-critical-end */` 마커 기준)

한 번에: `npm run build:css` (purge 후 미니파이)

## 캐시 (Cache-Control)

- **테마**: `della-theme/.htaccess` 에서 CSS/JS/이미지에 `max-age=31536000, immutable` 적용.
- **사이트 루트**: `www/.htaccess` 에 동일 규칙 추가해 두었음 (테마·업로드 정적 자산). Apache `mod_headers`, `mod_expires` 필요.
- **업로드만 별도 적용 시**: `cache-headers-uploads.htaccess.example` 를 `wp-content/uploads/.htaccess` 로 복사해 사용.
