<?php
/**
 * 공용 하단 — 사업자 정보(전자상거래법 제10조) + 이용약관·개인정보처리방침·환불정책 링크
 * 사용: $gpFooterBase = '../';  include __DIR__ . '/../libs/footer.php';   (사이트 최상위까지의 상대 경로)
 */
require_once __DIR__ . '/../Server/business_info.php';
$gpFooterBase = $gpFooterBase ?? '';
$gpBizNoDigits = preg_replace('/\D/', '', (string) GP_BUSINESS_INFO['biz_no']);
?>
<style>
  .gp-legal-footer{margin-top:40px;padding:28px 16px 40px;border-top:1px solid #e3e6eb;background:#fafbfc;color:#5f6773;font-size:12.5px;line-height:1.75;text-align:left}
  .gp-legal-footer .gp-inner{max-width:1120px;margin:0 auto}
  .gp-legal-footer nav{display:flex;flex-wrap:wrap;gap:6px 18px;margin-bottom:12px;font-size:13px}
  .gp-legal-footer nav a{color:#3b4250;text-decoration:none}
  .gp-legal-footer nav a.gp-privacy{font-weight:800;color:#1f2a37}
  .gp-legal-footer dl{display:flex;flex-wrap:wrap;gap:0 16px;margin:0}
  .gp-legal-footer dl div{display:flex;gap:6px}
  .gp-legal-footer dt{font-weight:700}
  .gp-legal-footer dd{margin:0}
  .gp-legal-footer .gp-biz-missing{color:#c0392b}
  .gp-legal-footer .gp-copy{margin-top:10px;color:#8a919c}
</style>
<footer class="gp-legal-footer">
  <div class="gp-inner">
    <nav aria-label="약관 및 정책">
      <a href="<?= $gpFooterBase ?>policy/terms.php">이용약관</a>
      <a class="gp-privacy" href="<?= $gpFooterBase ?>policy/privacy.php">개인정보처리방침</a>
      <a href="<?= $gpFooterBase ?>policy/refund.php">교환·반품·환불 안내</a>
    </nav>
    <dl>
      <div><dt>상호</dt><dd><?= gpBiz('company') ?></dd></div>
      <div><dt>대표자</dt><dd><?= gpBiz('ceo') ?></dd></div>
      <div><dt>사업자등록번호</dt><dd><?= gpBiz('biz_no') ?>
        <?php if ($gpBizNoDigits !== ''): ?>
          <a href="https://www.ftc.go.kr/bizCommPop.do?wrkr_no=<?= $gpBizNoDigits ?>" target="_blank" rel="noopener">[사업자정보확인]</a>
        <?php endif; ?></dd></div>
      <div><dt>통신판매업 신고번호</dt><dd><?= gpBiz('mail_order_no') ?></dd></div>
      <div><dt>본사</dt><dd><?= gpBiz('address') ?></dd></div>
      <div><dt>연구소(교육센터)</dt><dd><?= gpBiz('lab_address') ?></dd></div>
      <div><dt>전화</dt><dd><?= gpBiz('phone') ?></dd></div>
      <div><dt>팩스</dt><dd><?= gpBiz('fax') ?></dd></div>
      <div><dt>이메일</dt><dd><?= gpBiz('email') ?></dd></div>
      <div><dt>개인정보 보호책임자</dt><dd><?= gpBiz('privacy_officer') ?></dd></div>
      <div><dt>호스팅 제공자</dt><dd><?= gpBiz('hosting') ?></dd></div>
    </dl>
    <p class="gp-copy">© <?= gpBiz('company') ?>. All Rights Reserved.</p>
  </div>
</footer>
