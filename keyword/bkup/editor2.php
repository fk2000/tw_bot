<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>テキストエディタ</title>
</head>
<body>
<form method="post" action="editor2.php">
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
    print "<option>$file</option>\n";
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
  $text = htmlspecialchars($text);
  print $text;
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
  $fp = @fopen($editfile, 'w');
  if (!$fp) {
    print "このファイルには書き込みできません。<br>\n";
  } else {
    $contents = htmlspecialchars($_POST['contents']);
    fwrite($fp, $contents);
    fclose($fp);
    print "書き込み完了しました。<br>\n";
  }
}
?>
</body>
</html>
