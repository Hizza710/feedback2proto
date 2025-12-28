<?php
// 投稿削除API
require_once __DIR__ . '/db_conn.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $post_id = $_POST['post_id'] ?? '';

    // post_idが空ならエラー
    if ($post_id === '') {
        echo json_encode(['success' => false, 'message' => 'post_idが必要です']);
        exit;
    }

    $pdo = db_conn();

    // トランザクション開始
    $pdo->beginTransaction();

    try {
        // 関連コメント削除
        $stmt = $pdo->prepare('DELETE FROM gs_wf30_comments WHERE post_id = :post_id');
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // 関連スタンプ削除
        $stmt = $pdo->prepare('DELETE FROM gs_wf30_stamps WHERE post_id = :post_id');
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        // 投稿本体削除
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
