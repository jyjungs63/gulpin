<?php

if (!defined('GULPIN_INTERNAL_BOOTSTRAP')) {
    define('GULPIN_INTERNAL_BOOTSTRAP', true);
}
require_once __DIR__ . '/../_private/dbinit.php';

header('Content-Type: application/json; charset=utf-8');

function addCenter(array &$data, string $sido, string $gugun, array $center): void
{
    if (!$sido || !$gugun || !$center['name'] || !$center['address'] || !$center['phone']) {
        return;
    }
    if (!isset($data[$sido])) {
        $data[$sido] = array();
    }
    if (!isset($data[$sido][$gugun])) {
        $data[$sido][$gugun] = array();
    }
    foreach ($data[$sido][$gugun] as $existing) {
        if ($existing['name'] === $center['name'] && $existing['address'] === $center['address']) {
            return;
        }
    }
    $data[$sido][$gugun][] = $center;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sido = isset($_GET['sido']) ? trim((string) $_GET['sido']) : '';
        if ($sido !== '') {
            $stmt = $conn->prepare(
                'SELECT sido, gugun, name, area, dong, address, phone,
                        zip_code, road_address, address_detail
                 FROM gulpin_centers
                 WHERE sido = ?
                 ORDER BY id'
            );
            $stmt->bind_param('s', $sido);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query(
                'SELECT sido, gugun, name, area, dong, address, phone,
                        zip_code, road_address, address_detail
                 FROM gulpin_centers
                 ORDER BY id'
            );
        }
        $data = array();

        while ($row = $result->fetch_assoc()) {
                addCenter($data, $row['sido'], $row['gugun'], array(
                'name' => $row['name'],
                'area' => $row['area'],
                'dong' => $row['dong'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'zipCode' => $row['zip_code'],
                'roadAddress' => $row['road_address'] ?: $row['address'],
                'addressDetail' => $row['address_detail']
                ));
            }

        if (isset($stmt)) {
            $stmt->close();
        }

        echo json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(array('error' => 'method_not_allowed'), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $data = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : null;
    if ($data === null) {
        http_response_code(400);
        echo json_encode(array('error' => 'invalid_data'), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $conn->begin_transaction();
    $conn->query('DELETE FROM gulpin_centers');
    $stmt = $conn->prepare(
        'INSERT INTO gulpin_centers
            (sido, gugun, name, area, dong, address, phone, zip_code, road_address, address_detail)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($data as $sido => $guguns) {
        if (!is_array($guguns)) {
            continue;
        }
        foreach ($guguns as $gugun => $centers) {
            if (!is_array($centers)) {
                continue;
            }
            foreach ($centers as $center) {
                if (!is_array($center) || empty($center['name']) || empty($center['address']) || empty($center['phone'])) {
                    throw new InvalidArgumentException('센터 필수 정보가 없습니다.');
                }
                $name = (string) $center['name'];
                $area = (string) ($center['area'] ?? '');
                $dong = (string) ($center['dong'] ?? '');
                $address = (string) $center['address'];
                $phone = (string) $center['phone'];
                $zipCode = (string) ($center['zipCode'] ?? '');
                $roadAddress = (string) ($center['roadAddress'] ?? $address);
                $addressDetail = (string) ($center['addressDetail'] ?? '');
                $stmt->bind_param(
                    'ssssssssss',
                    $sido,
                    $gugun,
                    $name,
                    $area,
                    $dong,
                    $address,
                    $phone,
                    $zipCode,
                    $roadAddress,
                    $addressDetail
                );
                $stmt->execute();
            }
        }
    }

    $stmt->close();
    $conn->commit();
    echo json_encode(array('success' => true), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if (isset($conn) && $conn->thread_id) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(array(
        'error' => 'branch_api_failed',
        'message' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
