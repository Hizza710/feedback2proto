<?php
// DBリセット用スクリプト
require_once __DIR__ . '/db_conn.php';

// セキュリティ：リセットキーの取得
$reset_key = '';
if (is_file(__DIR__ . '/config.php')) {
    $cfg = require __DIR__ . '/config.php';
    if (is_array($cfg) && isset($cfg['reset_key'])) {
        $reset_key = (string)$cfg['reset_key'];
    }
}
if ($reset_key === '') {
    $reset_key = (string)getenv('RESET_KEY');
}

// ローカル環境のみ実行可
$host = $_SERVER['HTTP_HOST'] ?? '';
$is_local = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);
if (!$is_local) {
    http_response_code(403);
    exit('Forbidden: reset is allowed only on local environment.');
}

// リセットキーが必要な場合はチェック
if ($reset_key !== '') {
    $given = (string)($_GET['key'] ?? '');
    if ($given === '' || !hash_equals($reset_key, $given)) {
        http_response_code(403);
        exit('Forbidden: invalid reset key.');
    }
}

$confirmed = (($_GET['confirm'] ?? '') === '1');

if (!$confirmed) {
    // 確認画面を表示
?>
    <!doctype html>
    <html lang="ja">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>DBリセット確認</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <div class="page">
            <header class="topbar">
                <div class="brand">
                    <div class="badge">WF30</div>
                    <div class="titles">
                        <h1>DBリセット</h1>
                        <p>ローカルのデータを全削除</p>
                    </div>
                </div>
                <nav class="nav">
                    <a class="navbtn" href="select.php">投稿一覧</a>
                    <a class="navbtn" href="index.php">投稿する</a>
                </nav>
            </header>

            <section class="card">
                <div class="card-inner">
                    <div class="note">
                        <strong>確認：</strong> DBのデータ（投稿/コメント/スタンプ）を全て削除します。元に戻せません。
                    </div>

                    <div class="form-actions" style="margin-top:12px;">
                        <a class="navbtn" href="reset_db.php?confirm=1<?= $reset_key !== '' ? '&key=' . urlencode((string)($_GET['key'] ?? '')) : '' ?>">本当に削除する</a>
                        <a class="navbtn" href="select.php">やめる</a>
                    </div>
                </div>
            </section>
        </div>
    </body>

    </html>
<?php
    exit;
}

// ここから実際のリセット処理
try {
    $pdo = db_conn();

    // 外部キー制約がある場合も考慮して、一時的に無効化
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    try {
        $pdo->exec('TRUNCATE TABLE gs_wf30_comments');
        $pdo->exec('TRUNCATE TABLE gs_wf30_stamps');
        $pdo->exec('TRUNCATE TABLE gs_wf30_p1');
    } finally {
        // 失敗しても必ず戻す
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    // key付きで来た場合は維持したまま戻す
    $qs = 'reset=1';
    if ($reset_key !== '') {
        $qs .= '&key=' . urlencode((string)($_GET['key'] ?? ''));
    }
    header('Location: select.php?' . $qs);
    exit;
} catch (PDOException $e) {
    exit('DB Error: ' . $e->getMessage());
}
