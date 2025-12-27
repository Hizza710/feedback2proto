<?php
require_once('db_conn.php');
$pdo = db_conn();

$sql = "SELECT id, name, title, URL, URLgit, question, indate
        FROM gs_wf30_p1
        ORDER BY indate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

$filename = 'wf30_cards_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

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
