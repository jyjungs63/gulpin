# 글로벌에스이 홈페이지 운영 및 SSL 적용 검토안

현재 홈페이지는 카페24의 **PHP 7.4** 환경에서 운영 중입니다. 운영을 유지하는 방안과 PHP 8.4로 전환해 HTTPS를 적용하는 방안을 비교했습니다.

## 운영 방안 비교

| 구분 | 작업 범위 | 예상 추가 비용 | 장점 | 고려 사항 |
| --- | --- | --- | --- | --- |
| **1안. 현 상태 유지** | PHP 7.4 유지 | 없음 | 기존 기능과 운영 환경을 유지하며 작업에 따른 서비스 중단을 피할 수 있음 | HTTP 보안 경고가 남음. PHP 7.4의 공식 지원이 종료되어 중장기 전환 필요 |
| **2안. PHP 8.4 전환 + SSL Basic** | PHP 업그레이드, SSL Basic 설치, HTTPS 적용 | **설치비 11,000원(1회)** · 연간 인증서 비용 없음 | HTTPS 암호화 적용. 인증서 갱신 비용 부담이 적음 | 대상 호스팅 상품 및 도메인 신청 자격 확인 필요. 기존 홈페이지의 PHP 8.4 호환성 점검 필요 |
| **3안. PHP 8.4 전환 + 유료 SSL** | PHP 업그레이드, 유료 인증서 설치, HTTPS 적용 | **인증서 상품별 연간 비용** | 기업 정책이나 외부 요구사항에 맞춰 인증서 상품을 선택할 수 있음 | 연간 갱신 비용 발생. 필요한 보증이나 지원 범위는 상품별로 확인 |

## 검토 의견

| 운영 조건 | 권장 방안 |
| --- | --- |
| 개인정보 입력이나 결제 기능이 없는 소개형 홈페이지이며 개편이 예정됨 | **1안으로 단기 유지**하고 개편 시 PHP 업그레이드와 HTTPS 적용을 함께 진행 |
| 브라우저 보안 경고 해소가 필요하거나 문의·회원가입·결제 기능을 운영함 | 신청 자격이 충족되면 **2안 우선 검토** |
| 기업 정책상 지정 인증서나 별도 보증이 필요함 | **3안 검토** |

## 작업 전 확인 사항

- **백업 및 복구** · 카페24에서 PHP 버전을 변경하면 웹 파일과 DB가 초기화됩니다. 작업 전 두 항목을 모두 백업하고 서버 외부에 보관한 뒤 복구 가능 여부를 확인합니다. [카페24 PHP 버전 변경 안내](https://help.cafe24.com/faq/web-hosting/introduce/new-renewal-change/change-php-version-web-hosting/)
- **비용 구분** · SSL Basic 설치비 11,000원은 카페24 공식 요금입니다. 검토안에 기재된 DB 백업 작업비 11,000원(1회)은 별도 견적 항목으로 보고 적용 여부를 확인합니다. [SSL Basic 신청 안내](https://help.cafe24.com/faq/ssl-certificate/new-renewal-change/ssl_basic_what_and_how_to_apply/) · [카페24 웹 파일·DB 백업 안내](https://help.cafe24.com/faq/web-hosting/introduce/backup-restore/data_db_backup_restore/)
- **전환 및 접속 확인** · 현재 호스팅 상품과 도메인 기준으로 SSL Basic 신청 가능 여부 및 PHP 버전 변경 조건을 확인합니다. PHP 8.4에서 주요 기능을 점검하고, 두 주소(`globalse.co.kr`, `www.globalse.co.kr`)의 HTTPS 접속이 정상인 상태에서 HTTP 자동 전환을 설정합니다. [SSL 인증서 비교](https://help.cafe24.com/faq/ssl-certificate/new-renewal-change/ssl-certificate-type-comparison/) · [설치 및 전환 안내](https://help.cafe24.com/docs/ssl-certificate/ssl-certificate-authentication-installation/)
