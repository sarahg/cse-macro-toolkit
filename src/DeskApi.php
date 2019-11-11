<?php

namespace DeskMacrosToZendesk;

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

  public function __construct($endpoint, $config)
  {
    $this->endpoint = $endpoint;
    $this->config = $config;
  }

  public function GetDeskJson()
  {

    // @todo pull 100 at once
    // @todo pull all batches and concatenate

    // $total_macros = $macros['total_entries'];
    // $pages = $total_macros/100;

    // Call the Desk API.
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['desk_url'] . $this->endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Accept-Encoding: gzip, deflate',
        'Authorization: Basic ' . base64_encode($this->config['desk_username'] . ':' . $this->config['desk_password']),
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Host: ' . str_replace('https://', '', $this->config['desk_url']),
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
