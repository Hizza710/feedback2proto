<?php
require_once('db_conn.php');
require_once('lib.php');
$pdo = db_conn();

$sql = "SELECT id, name, title, URL, URLgit, question, indate
        FROM gs_wf30_p1
        ORDER BY indate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

// コメントを取得
$comment_sql = "SELECT post_id, commenter_name, comment_text, indate 
                FROM gs_wf30_comments 
                ORDER BY indate ASC";
$comment_stmt = $pdo->prepare($comment_sql);
$comment_stmt->execute();
$all_comments = $comment_stmt->fetchAll();

// 投稿IDごとにコメントを整理
$comments_by_post = [];
foreach ($all_comments as $c) {
    $comments_by_post[$c['post_id']][] = $c;
}

// スタンプを取得
$stamp_sql = "SELECT post_id, stamp_type, COUNT(*) as count 
              FROM gs_wf30_stamps 
              GROUP BY post_id, stamp_type";
$stamp_stmt = $pdo->prepare($stamp_sql);
$stamp_stmt->execute();
$all_stamps = $stamp_stmt->fetchAll();

// 投稿IDごとにスタンプを整理
$stamps_by_post = [];
foreach ($all_stamps as $s) {
    $stamps_by_post[$s['post_id']][$s['stamp_type']] = $s['count'];
}

?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>WF30 投稿一覧</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="page">
        <header class="topbar">
            <div class="brand">
                <div class="titles">
                    <img src="img/logo.png" alt="相互学習ページ" class="logo-image">
                </div>
            </div>
            <nav class="nav">
                <a class="navbtn" href="index.php">投稿する</a>
                <a class="navbtn" href="export_csv.php">CSVで保存</a>
                <?php
                $host = $_SERVER['HTTP_HOST'] ?? '';
                $is_local = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);
                if ($is_local):
                    $cfg = is_file(__DIR__ . '/config.php') ? (require __DIR__ . '/config.php') : [];
                    $reset_key = (is_array($cfg) && isset($cfg['reset_key'])) ? (string)$cfg['reset_key'] : '';
                    $reset_href = 'reset_db.php';
                    if ($reset_key !== '') {
                        $reset_href .= '?key=' . urlencode($reset_key);
                    }
                ?>
                    <a class="navbtn" href="<?= h($reset_href); ?>">DBリセット</a>
                <?php endif; ?>
            </nav>
        </header>

        <section class="card">
            <div class="card-inner">
                <div class="deck">
                    <?php foreach ($rows as $r): ?>
                        <article class="post" id="post-<?= h($r['id']); ?>">
                            <div class="post-header-wide">
                                <div class="post-title-section">
                                    <?php if (trim((string)$r['title']) !== ''): ?>
                                        <div class="post-work-title">
                                            【<?= h($r['title']); ?>】
                                            <span class="post-title-author"><?= h($r['name']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="post-work-title">
                                            <span class="post-title-author"><?= h($r['name']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (trim((string)$r['question']) !== ''): ?>
                                        <div class="post-question-label">『<?= nl2br(h($r['question'])); ?>』</div>
                                    <?php endif; ?>
                                </div>
                                <div class="post-actions-vertical">
                                    <div class="post-links">
                                        <?php if (trim((string)$r['URL']) !== ''): ?>
                                            <a class="link-icon" href="<?= h($r['URL']); ?>" target="_blank" rel="noopener" title="作品URL">🔗</a>
                                        <?php endif; ?>
                                        <?php if (trim((string)$r['URLgit']) !== ''): ?>
                                            <a class="link-icon github" href="<?= h($r['URLgit']); ?>" target="_blank" rel="noopener" title="GitHub">
                                                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                                                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <button class="delete-post-btn" data-post-id="<?= h($r['id']); ?>" title="この投稿を削除">🪽カードを外す🪽</button>
                                </div>
                            </div>

                            <!-- フィードフォワードセクション -->
                            <?php
                            $comment_count = isset($comments_by_post[$r['id']]) ? count($comments_by_post[$r['id']]) : 0;
                            ?>
                            <div class="feedforward-section">
                                <?php if (isset($comments_by_post[$r['id']])): ?>
                                    <?php foreach ($comments_by_post[$r['id']] as $comment): ?>
                                        <div class="comment-item">
                                            <div class="comment-header">
                                                <span class="comment-name"><?= h($comment['commenter_name']); ?></span>
                                                <span class="comment-time"><?= h($comment['indate']); ?></span>
                                                <button class="delete-comment-btn"
                                                    data-post-id="<?= h($r['id']); ?>"
                                                    data-commenter-name="<?= h($comment['commenter_name']); ?>"
                                                    data-comment-text="<?= h($comment['comment_text']); ?>"
                                                    data-indate="<?= h($comment['indate']); ?>"
                                                    title="このコメントを削除">×</button>
                                            </div>
                                            <div class="comment-text"><?= nl2br(h($comment['comment_text'])); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- コメント投稿フォーム -->
                            <div class="comment-form-section">
                                <form class="comment-form" action="insert_comment.php" method="post">
                                    <input type="hidden" name="post_id" value="<?= h($r['id']); ?>">
                                    <div class="comment-input-row">
                                        <input class="comment-input" type="text" name="commenter_name" placeholder="あなたの名前" required maxlength="64">
                                        <textarea class="comment-textarea" name="comment_text" placeholder="コメントを書く..." required></textarea>
                                    </div>
                                    <div class="comment-actions">
                                        <div class="stamps-and-feedforward">
                                            <div class="comment-stamps" aria-label="stamps">
                                                <?php
                                                foreach (STAMPS_AVAILABLE as $stamp):
                                                    $count = isset($stamps_by_post[$r['id']][$stamp]) ? $stamps_by_post[$r['id']][$stamp] : 0;
                                                ?>
                                                    <button class="stamp-btn" type="button" data-post-id="<?= h($r['id']); ?>" data-stamp="<?= $stamp; ?>">
                                                        <?= $stamp; ?><?= $count > 0 ? ' ' . (int)$count : ''; ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="feedforward-title">💬 フィードフォワード (<?= $comment_count; ?>)</div>
                                        </div>

                                        <button class="comment-btn" type="submit">コメントする</button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (count($rows) === 0): ?>
                    <div class="note">まだ投稿がありません</div>
                <?php endif; ?>

            </div>
        </section>

        <!-- フッターロゴ -->
        <footer class="footer-logo">
            <img src="img/logo_wf30.png" alt="WF30" class="footer-logo-image">
            <div class="copyright">©︎HIRO710</div>
        </footer>
    </div>

    <script>
        // 投稿削除機能
        document.querySelectorAll('.delete-post-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('この投稿を削除しますか？（コメントとスタンプも全て削除されます）')) {
                    return;
                }

                const postId = this.dataset.postId;
                const formData = new FormData();
                formData.append('post_id', postId);

                try {
                    const res = await fetch('delete_post.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();

                    if (data && data.success) {
                        alert('投稿を削除しました');
                        location.reload();
                    } else {
                        alert('削除に失敗しました: ' + (data.message || '不明なエラー'));
                    }
                } catch (err) {
                    console.error('削除エラー', err);
                    alert('削除に失敗しました');
                }
            });
        });

        // コメント削除機能
        document.querySelectorAll('.delete-comment-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('このコメントを削除しますか？')) {
                    return;
                }

                const postId = this.dataset.postId;
                const commenterName = this.dataset.commenterName;
                const commentText = this.dataset.commentText;
                const indate = this.dataset.indate;

                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('commenter_name', commenterName);
                formData.append('comment_text', commentText);
                formData.append('indate', indate);

                try {
                    const res = await fetch('delete_comment.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();

                    if (data && data.success) {
                        alert('コメントを削除しました');
                        location.reload();
                    } else {
                        alert('削除に失敗しました: ' + (data.message || '不明なエラー'));
                    }
                } catch (err) {
                    console.error('削除エラー', err);
                    alert('削除に失敗しました');
                }
            });
        });

        // スタンプ機能（JSONでない応答でも原因が分かるようにする）
        document.querySelectorAll('.stamp-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.dataset.postId;
                const stamp = this.dataset.stamp;
                const userName = prompt('あなたの名前を入力してください（任意）', 'Anonymous');

                if (userName === null) return; // キャンセル

                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('stamp_type', stamp);
                formData.append('user_name', userName);

                try {
                    const res = await fetch('add_stamp.php', {
                        method: 'POST',
                        body: formData
                    });

                    const txt = await res.text();

                    if (!res.ok) {
                        console.error('add_stamp.php HTTP error', res.status, txt);
                        alert('スタンプ登録に失敗しました（HTTP ' + res.status + '）。Consoleを確認してください。');
                        return;
                    }

                    let data;
                    try {
                        data = JSON.parse(txt);
                    } catch (e) {
                        console.error('add_stamp.php returned non-JSON', txt);
                        alert('サーバ応答がJSONではありません。Consoleを確認してください。');
                        return;
                    }

                    if (data && data.success) {
                        location.reload();
                        return;
                    }

                    console.error('add_stamp.php returned JSON but failed', data);
                    alert('スタンプ登録に失敗しました。Consoleを確認してください。');

                } catch (err) {
                    console.error('fetch failed', err);
                    alert('通信に失敗しました。Consoleを確認してください。');
                }
            });
        });
    </script>

</body>

</html>