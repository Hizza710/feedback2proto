<?php
// データベース接続情報のサンプル
return [
    'local' => [
        'host' => 'XXX.X.X.X', // ローカル用
        'db'   => 'XXXX-XXXXX_gs_wf30',
        'user' => 'root',
        'pass' => ''
    ],
    'production' => [
        'host' => 'my-mysql-host.db.sakura.ne.jp', // 本番用
        'db'   => 'my-database-name',
        'user' => 'my-username',
        'pass' => 'my-password'
    ]
];
