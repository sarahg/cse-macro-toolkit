<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\DeskApi;

class ExportDeskMacros
{

  public function __construct()
  {
    // Retrieve API credentials from secrets.json
    // @todo Use PHP dotenv package
    $config = json_decode(file_get_contents('secrets.json'), true);

    // Make an API call to Desk and retrieve Macros.
    // Macro objects include top-level data and Actions;
    // Actions, if they exist, will include up to 24 action values.
    $endpoint = '/api/v2/macros';
    $api = new DeskApi($endpoint, $config);
    $json = $api->GetDeskJson();

    krumo($json);
    //var_dump($this->build_macro_object($data));
  }

  /**
   * Parse macros and pull the data we want to migrate.
   * 
   * @param array $data
   *   Array of JSON-formatted Macros.
   * @return array $content
   *   Enabled macros with relevant fields.
   */
  public function build_macro_object($data)
  {
    $content = [];
    foreach ($data['_embedded']['entries'] as $macro) {
      if ($macro['enabled'] == TRUE) {
        $id = $macro['id'];
        $content[$id] = [
          'title' => $macro['name'],
          'actions' => $this->get_macro_actions($id),
          'folders' => array_values($macro['folders']) // @todo is this useful in ZD?
        ];
      }
    }

    // @todo write to JSON files instead
    return $content;
  }

  /**
   * Get macro actions.
   * 
   * @param int $id
   *   The Desk macro ID.
   * @return array $actions
   *   Action types and values for the macro.
   */
  private function get_macro_actions($id)
  {
    $endpoint = '/api/v2/macros/' . $id . '/actions';
    $macro = new DeskApi($endpoint);

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

}