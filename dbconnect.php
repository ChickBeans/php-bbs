<?php

try {
    // 接続文字列をユーザー名パスワードを入力※hostはイコールで結ばない
    $db = new PDO('mysql:dbname=mini_bbs;host127.0.0.1;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    echo ('DB接続エラー：' . $e->getMessage());
}

// try {
//     // PDOオブジェクトを生成する
//     // PDO('接続文字列（dbname=dbname;hosthostIP;charset=utf-8）', 'useName', 'password')
//     $db = new PDO('mysql:dbname=mini_bbs;host127.0.0.1;charset=utf8', 'root', 'root');
// } catch (PDOException $e) {
//     echo 'DB接続エラー: ' . $e->getMessage();
// }
