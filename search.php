<?


$results = array();
if (!empty($_GET['query'])) {
  $angellist_api_search = "https://api.angel.co/1/search?query=" . urlencode($_GET['query']);
  $ch = curl_init($angellist_api_search);
  $results = curl_exec($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  if (!($response['http_code'] >= 200 && $response['http_code'] < 300)) {
    $results = array('Something went wrong when searching AngelList. Please try again in a few minutes');
  }

}


