<?php
/**
 * 사업자 정보 — 전자상거래법 제10조(사업자 신원 표시), 개인정보보호법 제30조(처리방침) 표시용
 * libs/footer.php, policy/*.php 에서 사용한다. 빈 값은 화면에 "(입력 필요)" 로 보인다.
 * 값이 바뀌면 이 파일만 수정해서 서버에 올린다.
 */
const GP_BUSINESS_INFO = array(
    'company'         => '차이나는마마톡 주식회사',   // 상호
    'ceo'             => '김현주',                    // 대표자 성명
    'biz_no'          => '814-81-01523',              // 사업자등록번호
    'mail_order_no'   => '',   // 통신판매업 신고번호 (예: 제2026-울산중구-0000호)
    'address'         => '울산 중구 종가로406-21 729호',   // 본사 소재지
    'lab_address'     => '울산 북구 가재길96 1동',          // 연구소(교육센터)
    'phone'           => '052-298-7910',              // 고객센터 전화
    'fax'             => '052-298-7911',
    'email'           => 'chaitalk7910@naver.com',    // 고객센터 이메일
    'cs_hours'        => '',   // 고객센터 운영시간 (예: 평일 10:00~17:00, 점심 12:00~13:00)
    'privacy_officer' => '',   // 개인정보 보호책임자 (성명/직책)
    'privacy_contact' => '',   // 개인정보 보호책임자 연락처 (전화 또는 이메일)
    'hosting'         => '',   // 호스팅 서비스 제공자
    'courier'         => '',   // 배송 택배사 (개인정보 처리위탁 고지)
    'return_fee'      => '',   // 단순 변심 반품 왕복 배송비 (예: 9,000원)
    'return_address'  => '',   // 반품 주소
    'effective_date'  => '',   // 약관·개인정보처리방침 시행일 (예: 2026-12-01)
);

/** 화면 표시용 값 — HTML 이스케이프, 빈 값은 "(입력 필요)" */
function gpBiz(string $key): string
{
    $value = trim((string) (GP_BUSINESS_INFO[$key] ?? ''));
    return $value === '' ? '<span class="gp-biz-missing">(입력 필요)</span>' : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
