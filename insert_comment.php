<?php
// データベース接続ファイルを読み込む
require_once 'db_conn.php';

// フォームから送信された値を受け取る
$post_id = $_POST['post_id'] ?? '';
$commenter_name = $_POST['commenter_name'] ?? '';
$comment_text = $_POST['comment_text'] ?? '';

// 必須項目が空ならエラー
if (empty($post_id) || empty($commenter_name) || empty($comment_text)) {
    header('Location: select.php?error=empty');
    exit;
}

try {
    $pdo = db_conn();

    // コメントをDBに保存
    $sql = "INSERT INTO gs_wf30_comments (post_id, commenter_name, comment_text, indate) VALUES (:post_id, :commenter_name, :comment_text, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':commenter_name', $commenter_name, PDO::PARAM_STR);
    $stmt->bindValue(':comment_text', $comment_text, PDO::PARAM_STR);
    $stmt->execute();

    // 投稿一覧ページに戻る
    header('Location: select.php#post-' . $post_id);
    exit;
} catch (PDOException $e) {
    exit('DB Error: ' . $e->getMessage());
}
