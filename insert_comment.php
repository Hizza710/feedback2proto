<?php
require_once 'db_conn.php';

// POSTデータ取得
$post_id = $_POST['post_id'] ?? '';
$commenter_name = $_POST['commenter_name'] ?? '';
$comment_text = $_POST['comment_text'] ?? '';

// バリデーション
if (empty($post_id) || empty($commenter_name) || empty($comment_text)) {
    header('Location: select.php?error=empty');
    exit;
}

try {
    $pdo = db_conn();

    // コメント登録
    $sql = "INSERT INTO gs_wf30_comments (post_id, commenter_name, comment_text, indate) VALUES (:post_id, :commenter_name, :comment_text, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':commenter_name', $commenter_name, PDO::PARAM_STR);
    $stmt->bindValue(':comment_text', $comment_text, PDO::PARAM_STR);
    $stmt->execute();

    // 元のページにリダイレクト
    header('Location: select.php#post-' . $post_id);
    exit;
} catch (PDOException $e) {
    exit('DB Error: ' . $e->getMessage());
}
