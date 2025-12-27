<?php
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
        <header class="topbar">
            <div class="brand">
                <div class="badge">WF30</div>
                <div class="titles">
                    <h1>相互学習ページ</h1>
                    <p>作品URLとGitHubを貼って、質問を投げる</p>
                </div>
            </div>
            <nav class="nav">
                <a class="navbtn" href="select.php">みんなの投稿を見る</a>
                <a class="navbtn" href="export_csv.php">CSVで保存</a>
            </nav>
        </header>

        <section class="card">
            <div class="card-inner">
                <div class="h2">みんな創ってみたよ！遊んで意見頂戴♪</div>

                <?php if ($done): ?>
                    <a class="rainbow-cta" href="select.php" aria-label="みんなの投稿を見に行く">
                        <span class="rainbow-cta-title">送信完了。ありがとう。</span>
                        <span class="rainbow-cta-sub">次は一覧でフィードバックを見よう。→ みんなの投稿を見る</span>
                    </a>
                <?php endif; ?>

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
            </div>
        </section>
    </div>

</body>

</html>