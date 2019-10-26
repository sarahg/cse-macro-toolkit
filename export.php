<?php

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

// All the errors!
// @todo does this matter for CLI?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer packages.
require __DIR__ . '/vendor/autoload.php';

// @todo maybe it'd be better to build this as a Class
// this would run on __init()
$macro_master_list = build_macro_object();
// @todo store the master list in MySQL (or somewhere) for the ZD import

quick_replies_to_google_docs($macro_master_list);


/**
 * Fetch data from the Desk API with cURL.
 * 
 * @throws Exception If cURL returns an error
 * @param string $endpoint Desk API endpoint
 *   See https://dev.desk.com/API/using-the-api/
 * @return array
 */
function fetch_desk_data($endpoint)
{
  // @todo I suppose we could use Guzzle for this
  
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
 * Retrieve all macros.
 * 
 * @return array
 */
function build_macro_object()
{
  // Retrieve a JSON object of Desk macros.
  $macros = fetch_desk_data('/api/v2/macros');

  // @todo Loop through pages
  // $total_macros = $macros['total_entries'];
  // $pages = $total_macros/100;

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
}

/**
 * Get macro actions.
 * 
 * @param int $id
 *   The Desk macro ID.
 * @return array $actions
 *   Action types and values for the macro.
 */
function get_macro_actions($id)
{
  $macro = fetch_desk_data('/api/v2/macros/' . $id . '/actions');
  $actions = [];
  foreach ($macro['_embedded']['entries'] as $action) {
    if ($action['enabled'] == TRUE) {
      $actions[$id] = [
        'type' => $action['type'],
        'value' => $action['value']
      ];
    }
  }
  return $actions;
}

/**
 * Get macro Quick Reply text.
 * We're editing this in a Google Sheet, so pull this separately.
 * @todo export straight to Google?
 * 
 * @param int $id
 *   The Desk macro ID.
 * @return string $reply_text
 *   Text for the Quick Reply.
 */
function get_macro_quick_reply_text($id)
{ 

}

/**
 * Push Quick Reply text content to a Google Spreadsheet.
 */
function quick_replies_to_google_docs()
{

}