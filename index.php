<?php
session_start();
require('dbconnect.php');
// session変数が登録されているか否かでログイン状態を判別する
// $_SESSIONにid,timeが設定されている場合
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // idに該当する情報を取得
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  // idにsessionIdを設定
  $members->execute(array($_SESSION['id']));
  // memberに取得した行を代入
  $member = $members->fetch();
} else {
  // ログインページに遷移
  header('Location: login.php');
  exit();
}

// 投稿するが押されダミーフォームが発動された場合
if (!empty($_POST)) {
  // テキストエリアに何かしら入っていた場合
  if ($_POST['message'] !== '') {
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW()');
    $message->execute(array(
      // member_idにログインで使用したmembersテーブル内のidを設定することでpostsテーブルと関連付けることができる。
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));
  }
  // リロードした際に再び同じ内容が投稿されるのを防ぐ
  header('Location: index.php');
  exit();
}

// ページネーションの設定
$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
// ２つを比較して値が大きい方を$pageに代入する。
// 0や-1などといった入力に対する対処
$page = max($page, 1);

// 投稿数を超えるページに入力対策
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;

// members,postsそれぞれのテーブルを関連付けてデータを取得する
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created ASC LIMIT ?,5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// REを押した時に処理が走り、クエリにidを持つようになるから、$_REQUEST['res']で、返信の処理を行うことができる。
if (isset($_REQUEST['res'])) {
  // 返信の処理
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
  $response->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@' . $table['name'] . ' ' . $table['message'];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
      <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
      <form action="" method="post">
        <dl>
          <dt><?php echo htmlspecialchars($member['name'], ENT_QUOTES) ?>さん、メッセージをどうぞ</dt>
          <dd>
            <textarea name="message" cols="50" rows="5"><?php echo htmlspecialchars($message, ENT_QUOTES) ?></textarea>
            <!-- どの投稿における返信かを判別する -->
            <input type="hidden" name="reply_post_id" value="<?php echo htmlspecialchars($_REQUEST['res'], ENT_QUOTES) ?>" />
          </dd>
        </dl>
        <div>
          <p>
            <input type="submit" value="投稿する" />
          </p>
        </div>
      </form>

      <?php foreach ($posts as $post) : ?>

        <div class="msg">
          <img src="member_picture/<?php echo (htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="48" height="48" alt="<?php echo (htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
          <p><?php echo (htmlspecialchars($post['message'], ENT_QUOTES)); ?><span class="name">（<?php echo (htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>[<a href="index.php?res=<?php echo (htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]</p>
          <p class="day"><a href="view.php?id=<?php echo htmlspecialchars($post['id']) ?>"></a>

            <?php if ($post['reply_message_id'] > 0) : ?>
              <a href="view.php?id=<?php echo htmlspecialchars($post['reply_message_id']) ?>"><?php echo (htmlspecialchars($post['created'], ENT_QUOTES)); ?>
                返信元のメッセージ</a>
            <?php endif ?>

            <!-- ログインidとメンバーidが同じ場合削除ボタンを表示する -->
            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              [<a href="delete.php?id=<?php echo htmlspecialchars($post['id']) ?>" style="color: #F33;">削除</a>]
          </p>
        <?php endif ?>
        </div>

      <?php endforeach ?>

      <ul class="paging">
        <?php if ($page > 1) : ?>
          <li><a href="index.php?page=<?php echo ($page - 1) ?>">前のページへ</a></li>
        <?php else : ?>
          <li>前のページへ</li>
        <?php endif ?>
        <?php if ($page < $maxPage) : ?>
          <li><a href="index.php?page=<?php echo ($page + 1) ?>">次のページへ</a></li>
        <?php else : ?>
          <li>次のページへ</li>
        <?php endif ?>
      </ul>
    </div>
  </div>
</body>

</html>