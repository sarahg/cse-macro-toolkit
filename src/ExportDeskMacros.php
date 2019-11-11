<?php

namespace DeskMacrosToZendesk;
use DeskMacrosToZendesk\DeskApi;

define('DESK_API_BATCH_SIZE', 100);

class ExportDeskMacros
{

  public function __construct()
  {
    // All the macros!
    $macros = $this->fetchAllMacros();
    echo 'Pulled ' . count($macros) . ' Macros from the Desk API.';

    // Retrieve Actions for enabled macros.
    $macros_parsed = $this->build_macro_object($macros);
    krumo($macros_parsed);
    
  }

  /**
   * Retrieve all our enabled macros from the Desk API.
   */
  public function fetchAllMacros()
  {
    $data = [];
    $endpoint = '/api/v2/macros';
    $api = new DeskApi($endpoint);
    $meta = $api->GetDeskJson();

    $per_page = DESK_API_BATCH_SIZE;
    $total = $meta['total_entries'];
    $total_pages = ceil($total / $per_page);

    $batch_id = 1;
    while ($batch_id <= $total_pages) {
      $endpoint = '/api/v2/macros?page=' . $batch_id . '&per_page=' . $per_page;

      $api = new DeskApi($endpoint);
      $json = $api->GetDeskJson();

      if (!empty($json['_embedded']['entries'])) {
        foreach ($json['_embedded']['entries'] as $macro) {
          if ($macro['enabled'] == TRUE) {
            array_push($data, $macro);
          }
        }
      }

      $batch_id++;
    }
    return $data;
  }

  /**
   * Parse macros and pull the data we want to migrate.
   * 
   * @param array $data
   *   Array of Macros.
   * @return array $content
   *   Enabled macros with relevant fields.
   */
  public function build_macro_object($data)
  {
    $content = [];
    foreach ($data as $macro) {
      $id = $macro['id'];
      $content[$id] = [
        'title' => $macro['name'],
        'actions' => $this->get_macro_actions($id),
        'folders' => array_values($macro['folders']) // @todo is this useful in ZD?
      ];
    }
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
  public function get_macro_actions($id)
  {
    $endpoint = '/api/v2/macros/' . $id . '/actions';
    $api = new DeskApi($endpoint);
    $macro = $api->GetDeskJson();

    $actions = [];
    foreach ($macro['_embedded']['entries'] as $action) {
      if ($action['enabled'] == TRUE) {
        $actions[] = [
          'type' => $action['type'],
          'value' => $action['value']
        ];
      }
    }
    return $actions;
  }
}
