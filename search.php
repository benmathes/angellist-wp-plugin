<?


$results = array();
if (!empty($_GET['query'])) {
  $angellist_api_search = "https://api.angel.co/1/search?type=User,Startup&query=" . urlencode($_GET['query']);
  $ch = curl_init($angellist_api_search);
  $results = curl_exec($ch);
  $response = curl_getinfo($ch);

  if (!($response['http_code'] >= 200 && $response['http_code'] < 300)) {
    $results = array('Something went wrong when searching AngelList. Please try again in a few minutes');
  }

  // flushes buffers and sends down results
  curl_close($ch);
}

