<?
// 
var_dump($_GET);

$ch = curl_init();
$content = curl_exec($ch);
$response = curl_getinfo( $ch );
curl_close ( $ch );

var_dump($content, $response);
