<?php
// 投稿データをCSVでダウンロードする
require_once('db_conn.php');
$pdo = db_conn();

// 投稿データを取得
$sql = "SELECT id, name, title, URL, URLgit, question, indate
        FROM gs_wf30_p1
        ORDER BY indate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

// ファイル名を作成
$filename = 'wf30_cards_' . date('Ymd_His') . '.csv';

// CSVダウンロード用のヘッダー
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// CSV出力
$out = fopen('php://output', 'w');
fputcsv($out, ['id', 'name', 'title', 'URL', 'URLgit', 'question', 'indate']);
foreach ($rows as $r) {
  fputcsv($out, [
    $r['id'],
    $r['name'],
    $r['title'],
    $r['URL'],
    $r['URLgit'],
    $r['question'],
    $r['indate'],
  ]);
}
fclose($out);
exit;
