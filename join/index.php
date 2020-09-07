<!-- エラーチェック、 -->
<?php
session_start();

// フォームが送信された場合
if (!empty($_POST)) {

	if ($_POST['name'] === '') {
		// 変数['name属性']で変数を持てる
		$error['name'] = 'blank';
	}
	if ($_POST['email'] === '') {
		// 変数['email属性']で変数を持てる
		$error['email'] = 'blank';
	}

	// // strlenは文字数チェック
	// if (strlen($_POST['password'] < 4)) {
	// 	// 変数['password属性']で変数を持てる
	// 	$error['password'] = 'length';
	// }

	if ($_POST['password'] === '') {
		// 変数['password属性']で変数を持てる
		$error['password'] = 'blank';
	}
	// $errorがエラーなしだった場合
	if (empty($error)) {
		// $_SESSION['join']に連想配列$_POSTを代入
		$_SESSION['join'] = $_POST;
		header('Location: check.php');
		exit();
	}
}

// urlのaction=rewriteもしくは$_SESSION['join']が設定されている場合
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
	// $_POSTに$_SESSION['join']の値を入れる
	$_POST = $_SESSION['join'];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>会員登録</h1>
		</div>

		<div id="content">
			<p>次のフォームに必要事項をご記入ください。</p>
			<form action="" method="post" enctype="multipart/form-data">
				<dl>
					<dt>ニックネーム<span class="required">必須</span></dt>
					<dd>
						<input type="text" name="name" size="35" maxlength="255" value="<?php echo (htmlspecialchars($_POST['name'])) ?>" />
						<!-- ニックネーム入力チェック -->
						<?php if ($error['name'] === 'blank') : ?>
							<p class="error">*ニックネームを入力してください</p>
						<?php endif ?>
					</dd>
					<dt>メールアドレス<span class="required">必須</span></dt>
					<dd>
						<input type="text" name="email" size="35" maxlength="255" value="<?php echo (htmlspecialchars($_POST['email'])) ?>" />
						<!-- メールアドレス入力チェック -->
						<?php if ($error['email'] === 'blank') : ?>
							<p class="error">*メールアドレスを入力してください</p>
						<?php endif ?>
					<dt>パスワード<span class="required">必須</span></dt>
					<dd>
						<input type="password" name="password" size="10" maxlength="20" value="<?php echo (htmlspecialchars($_POST['password'])) ?>" />
						<!-- パスワード入力チェック -->
						<!-- <?php if ($error['password'] === 'length') : ?>
							<p class="error">*パスワードは４文字以上で入力してください</p>
						<?php endif ?> -->
						<?php if ($error['password'] === 'blank') : ?>
							<p class="error">*パスワードを入力してください</p>
						<?php endif ?>
					</dd>
					<dt>写真など</dt>
					<dd>
						<input type="file" name="image" size="35" value="test" />
					</dd>
				</dl>
				<div><input type="submit" value="入力内容を確認する" /></div>
			</form>
		</div>
</body>

</html>