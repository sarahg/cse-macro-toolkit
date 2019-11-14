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
    $macros_actions = $this->build_macro_object($macros);

    // Export Quick Replies to CSV.
    $this->export_quick_replies($macros_actions);
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

    $total_pages = ceil($meta['total_entries'] / DESK_API_BATCH_SIZE);

    $batch_id = 1;
    while ($batch_id <= $total_pages) {
      $endpoint = '/api/v2/macros?page=' . $batch_id . '&per_page=' . DESK_API_BATCH_SIZE;

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

  /**
   * Export Quick Replies to CSV.
   */
  public function export_quick_replies($macros_actions)
  {
    // Prep CSV file.
    $filename = 'quick-replies-' . time() . '.csv';
    $fp = fopen($filename, 'w');

    // Loop through our macros and pick out all the Quick Replies.
    // Export these to our CSV file.
    $count = 0;
    foreach ($macros_actions as $id => $row) {
      if (!empty($row['actions'])) {
        foreach ($row['actions'] as $action) {
          if ($action['type'] == 'set-case-note') {
            $quickReply = [$id, $row['title'], $action['value']];
            fputcsv($fp, $quickReply);
            $count++;
          }
        }
      }
    }

    fclose($fp);

    if ($count >= 1) {
      echo 'Exported ' . $count . ' Quick Replies to file: ' . $filename;
    }
  }
}
