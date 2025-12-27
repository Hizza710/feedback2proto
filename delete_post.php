<?php
// 投稿削除処理
require_once __DIR__ . '/db_conn.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $post_id = $_POST['post_id'] ?? '';

    if ($post_id === '') {
        echo json_encode(['success' => false, 'message' => 'post_idが必要です']);
        exit;
    }

    $pdo = db_conn();

    // トランザクション開始
    $pdo->beginTransaction();

    try {
        // 関連するコメントを削除
        $stmt = $pdo->prepare('DELETE FROM gs_wf30_comments WHERE post_id = :post_id');
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // 関連するスタンプを削除
        $stmt = $pdo->prepare('DELETE FROM gs_wf30_stamps WHERE post_id = :post_id');
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // 投稿本体を削除
        $stmt = $pdo->prepare('DELETE FROM gs_wf30_p1 WHERE id = :id');
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // コミット
        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // ロールバック
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
