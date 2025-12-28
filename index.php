<?php
// 送信完了かどうかを判定
$done = isset($_GET['done']);
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>WF30 相互学習フォーム</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="page">
        <!-- ヘッダー（ロゴとメニュー） -->
        <header class="topbar">
            <div class="brand">
                <div class="titles">
                    <img src="img/logo.png" alt="相互学習ページ" class="logo-image">
                </div>
            </div>
            <nav class="nav">
                <a class="navbtn" href="select.php">みんなの投稿を見る</a>
                <a class="navbtn" href="export_csv.php">CSVで保存</a>
            </nav>
        </header>

        <!-- メインのカード部分 -->
        <section class="card">
            <div class="card-inner">
                <?php if ($done): ?>
                    <!-- 送信完了メッセージ -->
                    <a class="rainbow-cta rainbow-cta-fullscreen" href="select.php" aria-label="みんなの投稿を見に行く">
                        <span class="rainbow-cta-title">♪ 送信完了♪</span>
                        <span class="rainbow-cta-sub">開発、おつかれさま！<br>休んだ後は、メンバーからのフィードバックのギフトを楽しみに。<br><br>ここをクリックすると、皆からのギフトが見られるよ</span>
                    </a>
                <?php else: ?>
                    <div class="h2">創った作品、気楽に投稿を！皆の意見を聞いてみよう♪</div>

                    <!-- 投稿フォーム -->
                    <form class="form" action="insert.php" method="post">
                        <input class="input" type="text" name="name" required maxlength="64" placeholder="名前（例: Hiro）">

                        <input class="input" type="text" name="title" maxlength="128" placeholder="作品タイトル（例: 相互フィードバックボード）">

                        <input class="input" type="url" name="URL" placeholder="公開URL（例: https://example.com）">

                        <input class="input" type="url" name="URLgit" placeholder="GitURL（例: https://github.com/yourname/yourrepo）">

                        <textarea class="textarea" name="question" placeholder="意見求むポイント（質問、意見がほしいこと）"></textarea>

                        <div class="form-actions">
                            <button class="btn" type="submit">投稿する</button>
                            <button class="btn ghost" type="reset">入力をリセット</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <!-- フッター（ロゴと著作権） -->
        <footer class="footer-logo">
            <img src="img/logo_wf30.png" alt="WF30" class="footer-logo-image">
            <div class="copyright">©︎HIRO710</div>
        </footer>
    </div>

</body>

</html>