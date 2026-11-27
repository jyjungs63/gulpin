<?php
if (!defined('GULPIN_PASSWORD_POLICY')) {
    http_response_code(404);
    exit;
}

return [
    // PG 도입 시 false로 변경하고 password_admin_view 컬럼의 사본을 삭제한다.
    'enabled' => false,
];
