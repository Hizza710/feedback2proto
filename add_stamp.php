<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once 'db_conn.php';

$post_id = $_POST['post_id'] ?? '';
$stamp_type = $_POST['stamp_type'] ?? '';
$user_name = $_POST['user_name'] ?? 'Anonymous';

if ($post_id === '' || $stamp_type === '') {
    echo json_encode(['success' => false, 'error' => 'missing_parameters']);
    exit;
}

if (!ctype_digit((string)$post_id)) {
    echo json_encode(['success' => false, 'error' => 'invalid_post_id']);
    exit;
}

try {
    $pdo = db_conn();

    $sql = "INSERT INTO gs_wf30_stamps (post_id, stamp_type, user_name, indate)
            VALUES (:post_id, :stamp_type, :user_name, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', (int)$post_id, PDO::PARAM_INT);
    $stmt->bindValue(':stamp_type', $stamp_type, PDO::PARAM_STR);
    $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
    $stmt->execute();

    $count_sql = "SELECT stamp_type, COUNT(*) as count
                  FROM gs_wf30_stamps
                  WHERE post_id = :post_id
                  GROUP BY stamp_type";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->bindValue(':post_id', (int)$post_id, PDO::PARAM_INT);
    $count_stmt->execute();
    $stamps = $count_stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'stamps' => $stamps]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'db_error', 'detail' => $e->getMessage()]);
    exit;
}