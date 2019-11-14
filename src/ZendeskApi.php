<?php

namespace DeskMacrosToZendesk;

/**
 * Fetch data from the Zendesk API with cURL.
 * 
 * @throws Exception If cURL returns an error
 * @param string $endpoint API endpoint
 *   See https://developer.zendesk.com/rest_api/docs/support/macros#create-macro
 * @return array
 */
class ZendeskApi
{

  public function __construct()
  {
    $this->endpoint = '/api/v2/macros.json';
  }

  /**
   * Make a cURL POST request to the Zendesk API.
   */
  public function postZendeskData($data)
  {
    // Call the Zendesk API.
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://'. $_ENV['ZENDESK_SUBDOMAIN'] .'.zendesk.com' . $this->endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . base64_encode($_ENV['ZENDESK_EMAIL'] . ':' . $_ENV['ZENDESK_PASSWORD']),
        'Content-Type: application/json',
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
