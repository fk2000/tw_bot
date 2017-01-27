<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<title>キーワードエディタ</title>
</head>
<body>
<form method="post" action="editor.php">
注意：タグを含んだ文書を保存しないでください。
<table>
<tr>
<td>
<select name="file" size="20">
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
</td>
<td>
<textarea name="contents" cols="60" rows="20">
<?php
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
?>
</textarea>
</td>
</tr>
<tr>
<td align="right">
<input type="submit" name="open" value="開く">
</td>
<td align="right">
<input type="submit" name="save" value="保存">
</td>
</tr>
</table>
<input type="hidden" name="editfile" value="<?php print $file ?>">
</form>
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
