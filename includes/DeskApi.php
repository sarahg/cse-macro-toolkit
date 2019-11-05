<?php

/**
 * Fetch data from the Desk API with cURL.
 * 
 * @throws Exception If cURL returns an error
 * @param string $endpoint Desk API endpoint
 *   See https://dev.desk.com/API/using-the-api/
 * @return array
 */
class DeskApi {

  // @todo pass this in
  public $endpoint;
  private $config;

  // @todo pull 100 at once
  // @todo pull all batches and concatenate

  public function __init($endpoint, $config)
  {

    // Call the Desk API.
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

}