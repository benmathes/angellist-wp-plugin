<?
ob_start();

$results = array();
if (!empty($_GET['query'])) {
  $angellist_api_search = "http://api.angel.co/1/search?type=User,Startup&query=" . urlencode($_GET['query']);
  $ch = curl_init($angellist_api_search);
  curl_exec($ch);
  $results = ob_get_contents();
  $response = curl_getinfo($ch);

  // status codes in [200,300) are OK statuses
  if (!($response['http_code'] >= 200 && $response['http_code'] < 300)) {
    $results = array('Something went wrong when searching AngelList. Please try again in a few minutes');
  }
  curl_close($ch);
}
ob_end_clean();

echo($results);