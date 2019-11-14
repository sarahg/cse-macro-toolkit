<?php

namespace DeskMacrosToZendesk;

use Dotenv\Dotenv;

/**
 * Fetch data from the Desk API with cURL.
 * 
 * @throws Exception If cURL returns an error
 * @param string $endpoint Desk API endpoint
 *   See https://dev.desk.com/API/using-the-api/
 * @return array
 */
class DeskApi
{

  public function __construct($endpoint)
  {
    // Retrieve API credentials from our .env file.
    $dotenv = Dotenv::create(dirname(__DIR__, 1));
    $dotenv->load();

    // Desk API endpoint to query.
    $this->endpoint = $endpoint;
  }

  /**
   * Make a cURL request to the Desk API.
   * 
   * @return array $data
   *   Decoded JSON response from Desk.
   */
  public function getDeskData()
  {
    // Call the Desk API.
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $_ENV['DESK_URL'] . $this->endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Accept-Encoding: gzip, deflate',
        'Authorization: Basic ' . base64_encode($_ENV['DESK_USERNAME'] . ':' . $_ENV['DESK_PASSWORD']),
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Host: ' . str_replace('https://', '', $_ENV['DESK_URL']),
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

    $data = json_decode($response, true);
    return $data;
  }
}
