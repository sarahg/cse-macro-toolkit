<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

// Retrieve a JSON object of Desk macros.
$macros = fetch_desk_data('/api/v2/macros');
// @todo Loop through pages
// $total_macros = $macros['total_entries'];
// $pages = $total_macros/100;

// Pull the fields we want to migrate.
$content = [];
foreach ($macros['_embedded']['entries'] as $macro) {
  if ($macro['enabled'] == TRUE) {
    $id = $macro['id'];
    $content[$id] = [
      'title' => $macro['name'],
      'actions' => get_macro_actions($id),
      'folders' => array_values($macro['folders'])
    ];
  }
}

//krumo($content);

// @todo Build CSV file.
// echo 'Export complete: ' . $filename;

/**
 * Fetch data from the Desk API.
 * 
 * @throws Exception If cURL returns an error
 * @param string $endpoint Desk API endpoint
 *   See https://dev.desk.com/API/using-the-api/
 * @return array
 */
function fetch_desk_data($endpoint)
{
  // Retrieve API credentials from secrets.json
  $config = file_get_contents('secrets.json');
  $config = json_decode($config, true);

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $config['desk_url'] . $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
      'Accept: application/json',
      'Accept-Encoding: gzip, deflate',
      'Authorization: Basic ' . base64_encode($config['username'] . ':' . $config['password']),
      'Cache-Control: no-cache',
      'Connection: keep-alive',
      'Content-Type: application/json',
      'Host: ' . str_replace('https://', '', $config['desk_url']),
      //'Postman-Token: ',
      //'User-Agent: PostmanRuntime/7.18.0'
    ],
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    throw new Exception('cURL Error #: ' . $err);
  }

  return json_decode($response, true);
}

/**
 * Get macro actions.
 */
function get_macro_actions($id)
{
  // @todo
  $macro = fetch_desk_data('/api/v2/macros/'. $id .'/actions');
  krumo($macro);
}
