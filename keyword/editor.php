<?php
require_once('../apiconfig.php');

// URL
define('TOKEN_URL', 'https://accounts.google.com/o/oauth2/token');
define('INFO_URL', 'https://www.googleapis.com/oauth2/v1/userinfo');

$params = array(
	'code' => $_GET['code'],
	'grant_type' => 'authorization_code',
	'redirect_uri' => CALLBACK_URL,
	'client_id' => CONSUMER_KEY,
	'client_secret' => CONSUMER_SECRET,
);

// POST送信
$options = array('http' => array(
	'method' => 'POST',
	'content' => http_build_query($params)
));

// アクセストークンの取得
$res = file_get_contents(TOKEN_URL, false, stream_context_create($options));

// レスポンス取得
$token = json_decode($res, true);
if(isset($token['error'])){
	echo 'エラー発生';
	exit;
}

$access_token = $token['access_token'];

$params = array('access_token' => $access_token);

// ユーザー情報取得
$res = file_get_contents(INFO_URL . '?' . http_build_query($params));

$result = json_decode($res, true);
//if($result=="") {
//print "こちらから<a href='https://www.kichij.org/tw_bot/'>ログイン</a>してください。";
//exit;
//}
//echo $res;

//exit;
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>キーワードエディタ</title>
    <!-- BootstrapのCSS読み込み -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Custom styles for this template -->
    <link href="../css/justified-nav.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">

      <div class="masthead">
        <h3 class="text-muted">Bot Project</h3>

        <nav class="navbar navbar-light bg-faded rounded mb-3">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-toggleable-md" id="navbarCollapse">
            <ul class="nav navbar-nav text-md-center justify-content-md-between">
              <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Projects</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Services</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Downloads</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">About</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </div>

      <!-- Jumbotron -->
      <div class="jumbotron">
        <h1>tw_bot keyword editor</h1>
        <p class="lead">News bot[仮名](@fukkhd)に拾ってもらいたいキーワードを編集する画面です。</p>
      </div>
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-lg-4">
          <h2>ユーザー情報</h2>
          <p>
            <dl>
              <dt>プロフィール画像</dt>
                <dd><img src="<?php echo $result['picture']; ?>" width="100"></dd>
              <dt>ID</dt>
                <dd><?php echo $result['id']; ?></dd>
              <dt>ユーザー名</dt>
                <dd><?php echo $result['name']; ?></dd>
              <dt>氏名</dt>
                <dd><?php echo $result['family_name']; ?> <?php echo $result['given_name']; ?></dd>
              <dt>Location</dt>
                <dd><?php echo $result['locale']; ?></dd>
            <dl>
          </p>
        </div>
        <form method="post" action="editor.php">
        <div class="col-lg-4">
          <h2>ファイル一覧</h2>
          <p>
            <select name="file" style="width: 200px">
              <?php
                // ファイル一覧を表示
                $dir = opendir("./");
                while($file = readdir($dir)) {
                  if(is_file("./$file")) {
                    if($file!="editor.php") {
                      print "<option>$file</option>\n";
                    }
                  }
                }
                closedir($dir);
              ?>
            </select>
          </p>
          <p><input type="submit" name="open" value="ファイルを開く"><!-- <a class="btn btn-primary" href="#" role="button" type="submit" name="open" >ファイルを開く &raquo;</a> --></p>
          <h2>ファイル内容</h2>
          <p>
            <textarea name="contents" cols="42" rows="20"><?php
                // ファイル内容を表示
                $file = $_POST['file'];
                if ($_POST['open'] && $file) {
                  $text = file_get_contents($file);
                  $text = htmlspecialchars($text, ENT_NOQUOTES);

                  //OR置換
                  $before_or = array(" OR ");
                  $after_crlf = array("\r\n");
                  $newtext1 = str_replace($before_or, $after_crlf, $text);

                  //AND置換
                  $before_space = array(" ");
                  $after_and = array(" AND ");
                  $newtext2 = str_replace($before_and, $after_space, $newtext1);

                  print $newtext2;
                }
              ?></textarea>
          </p>
          <p><input type="submit" name="save" value="ファイルを保存"><!-- <a class="btn btn-primary" href="#" role="button" type="submit" name="save" >ファイルを保存 &raquo;</a>--></p>
          <input type="hidden" name="editfile" value="<?php print $file ?>">
          </form>
        </div>
        <div class="col-lg-4">
          <h2>現在のtweet</h2>
          <p>
            <a class="twitter-timeline" href="https://twitter.com/fukkhd" data-lang="ja" data-width="300" data-height="800">Tweets by fukkhd
            </a><script src="//platform.twitter.com/widgets.js" async="" charset="utf-8"></script>
          </p>
        </div>
      </div>
<?php
// ファイルを保存
$editfile = $_POST['editfile'];

if ($_POST['save'] && $editfile) {
  $fp = @fopen($editfile, 'wt');
  if (!$fp) print "このファイルには書き込みできません。<br>\n";
  else {
    $contents = htmlspecialchars($_POST['contents'], ENT_NOQUOTES);

    //AND置換
    $berfore_and = array(" AND ");
    $after_space = array(" ");
    $newdata1 = str_replace($before_and, $after_space, $contents);

    //OR置換
    $before_crlf = array("\r\n");
    $after_or = array(" OR ");
    $newdata2 = str_replace($before_crlf, $after_or, $newdata1);

    //quote置換
    //$before_quote = array("&quot;");
    //$after_quote = array(""");
    //$newdata3 = str_replace($before_quote, $after_quote, $newdata2);

    fwrite($fp, $newdata2);
    fclose($fp);
    print "書き込み完了しました。<br>\nDATA=[".$newdata2."]<br>\n";
  }
}
?>
  </body>
</html>
