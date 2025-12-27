<?php
require_once('db_conn.php');

$name     = isset($_POST['name']) ? trim($_POST['name']) : '';
$title    = isset($_POST['title']) ? trim($_POST['title']) : '';
$URL      = isset($_POST['URL']) ? trim($_POST['URL']) : '';
$URLgit   = isset($_POST['URLgit']) ? trim($_POST['URLgit']) : '';
$question = isset($_POST['question']) ? trim($_POST['question']) : '';

if ($name === '') {
    exit('name is required');
}

$pdo = db_conn();

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

header('Location: index.php?done=1');
exit;
