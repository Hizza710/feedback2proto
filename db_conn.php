/* ver2 */

<?php
function db_conn()
{
  // 設定ファイルを読み込み（パスワードは config.php に分離）
  $config = require_once __DIR__ . '/config.php';

  // 環境を自動判定（localhostならローカル、それ以外なら本番）
  $is_local = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

  if ($is_local) {
    // ローカル環境(XAMPP)の設定
    $host = $config['local']['host'];
    $db   = $config['local']['db'];
    $user = $config['local']['user'];
    $pass = $config['local']['pass'];
  } else {
    // 本番環境(さくらサーバー)の設定
    $host = $config['production']['host'];
    $db   = $config['production']['db'];
    $user = $config['production']['user'];
    $pass = $config['production']['pass'];
  }

  $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

  try {
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
  } catch (PDOException $e) {
    exit('DB Connection Error: ' . $e->getMessage());
  }
}
