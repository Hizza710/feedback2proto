<?php
// コメント削除API
require_once __DIR__ . '/db_conn.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $post_id = $_POST['post_id'] ?? '';
    $commenter_name = $_POST['commenter_name'] ?? '';
    $comment_text = $_POST['comment_text'] ?? '';
    $indate = $_POST['indate'] ?? '';

    // 必須項目が空ならエラー
    if ($post_id === '' || $commenter_name === '' || $comment_text === '' || $indate === '') {
        echo json_encode(['success' => false, 'message' => '必要な情報が不足しています']);
        exit;
    }

    $pdo = db_conn();

    // コメントを削除（投稿ID・名前・本文・日時で特定）
    $stmt = $pdo->prepare('DELETE FROM gs_wf30_comments 
                           WHERE post_id = :post_id 
                           AND commenter_name = :commenter_name 
                           AND comment_text = :comment_text 
                           AND indate = :indate 
                           LIMIT 1');
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':commenter_name', $commenter_name, PDO::PARAM_STR);
    $stmt->bindValue(':comment_text', $comment_text, PDO::PARAM_STR);
    $stmt->bindValue(':indate', $indate, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
