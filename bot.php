<?php

require_once('vendor/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

require_once('apiconfig.php');

include_once '/home/kusanagi/kusanagi_html/DocumentRoot/slackbot/SlackBot.php';
include_once '/home/kusanagi/kusanagi_html/DocumentRoot/slackbot/SlackBotInfo.php';

$consumer_key = API_KEY;
$consumer_secret = API_SECRET;
$access_token = ACCESS_TOKEN;
$access_token_secret = ACCESS_SECRET;

$twObj = new TwitterOAuth(
    $consumer_key,
    $consumer_secret,
    $access_token,
    $access_token_secret
);

$list = array(
    'Hello!',
    'done!',
);

//$header = 'Content-Type: text/plain; charset=utf-8';
//try {
//    $to = new TwistOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);

//    $status = $to->post('statuses/update', ['status' => $list[array_rand($list)]]);
//    header($header, true, 200);
//    echo "tweet done!: https://twitter.com/{$status->user->screen_name}/status/{$status->id_str}\n";
//} catch (TwistException $e) {
//    header($header, true, $e->getCode() ?: 500);
//    echo "tweet NG!: {$e->getMessage()}\n";
//}

$file="/home/kusanagi/kusanagi_html/DocumentRoot/tw_bot/keyword/keyword.txt";
if ($file) {
  $key = file_get_contents($file);
#  $key = htmlspecialchars($text, ENT_NOQUOTES);
} else {
  $key="コワーキング OR ベンチャー OR フリーランス OR \"起業 失敗\" OR エンジニア OR デザイナー OR \"地方創生 コミュニティ\" OR インキュベーション OR オープンイノベーション OR ビジネスモデル OR 新サービス OR AI OR 人工知能 OR VR OR ロボット";
}

//オプション設定
$options = array('q'=>$key,'count'=>'1','lang'=>'ja');
//array_toLog($options);

//検索
$json = $twObj->OAuthRequest(
    'https://api.twitter.com/1.1/search/tweets.json',
    'GET',
    $options
);

//もらったjsonを連想配列に変換
$jset = json_decode($json, true);

//tweetidを取得
foreach ($jset['statuses'] as $result) {
    $id = $result['id'];
	$name = $result['user']['name'];
	$userid = $result['user']['id'];
	$link = $result['user']['profile_image_url'];
	$content = $result['text'];
	$updated = $result['created_at'];
	$time = $time = date("Y-m-d H:i:s",strtotime($updated));
}
//公式RT投稿
if(isset($id)) {
    echo $twObj->OAuthRequest(
        'https://api.twitter.com/1.1/statuses/retweet/'.$id.'.json',
        'POST',
        array()
    );
}

$url = 'https://hooks.slack.com/services/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';

$message = $jset['statuses'][0]['text'] ;
//$screen_name = $jset->statuses->user->screen_name;
//$id_str = $jset->statuses->user->id_str;
$org_tweet = 'https://twitter.com/'.$userid.'/status/'.$id;
#$data = $link;
$data = " [".$org_tweet."] ";
#$data .= $message;

// ポスト情報を生成
$info = new SlackBotInfo($url, $data);
// こんな感じにパラメータを変更
$info->channel = '#bot';
$info->username = 'twitter';
$info->icon_emoji = ':twitter:';

// メッセージをポスト
$bot = new SlackBot();
$bot->post_message($info);

//log
function toLog($str){
    $filename = "log.txt";
    $fp = fopen($filename,"a");
    fputs($fp,$str);
    fclose($fp);
}
//array_toLog
function array_toLog($array_str){
    $filename = "log.txt";
    $fp = fopen($filename,"a");
    foreach($array_str as $a){
        fputs($fp,$a."\n");
    }
}

