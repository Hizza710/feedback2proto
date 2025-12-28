<?php
// データベース接続ファイルを読み込む
require_once('db_conn.php');

// フォームから送信された値を受け取る
$name     = isset($_POST['name']) ? trim($_POST['name']) : '';
$title    = isset($_POST['title']) ? trim($_POST['title']) : '';
$URL      = isset($_POST['URL']) ? trim($_POST['URL']) : '';
$URLgit   = isset($_POST['URLgit']) ? trim($_POST['URLgit']) : '';
$question = isset($_POST['question']) ? trim($_POST['question']) : '';

// 名前が空ならエラー
if ($name === '') {
    exit('name is required');
}

$pdo = db_conn();

// 投稿データをDBに保存
$sql = "INSERT INTO gs_wf30_p1 (name, title, URL, URLgit, question, indate)
        VALUES (:name, :title, :URL, :URLgit, :question, NOW(6))";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'     => $name,
    ':title'    => $title,
    ':URL'      => $URL,
    ':URLgit'   => $URLgit,
    ':question' => $question,
]);

// 完了後はトップページにリダイレクト
header('Location: index.php?done=1');
exit;
