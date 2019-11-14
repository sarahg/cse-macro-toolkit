<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\DeskApi;

define('DESK_API_BATCH_SIZE', 100);

class ExportDeskMacros
{

  public function __construct()
  {
    // Retrieve Macros from Desk.
    echo 'Fetching macros via the Desk API...';
    $macros = $this->fetchAllMacros();

    // Build an array of all Macros with their Action data.
    echo "\nFetching macro actions via the Desk API...";
    $macros_actions = $this->buildMacroActions($macros);

    // Export Quick Replies, a type of Action, to CSV.
    // These contain text we need to edit before migrating.
    echo "\nExporting macros and actions to CSV...";
    $this->exportQuickReplies($macros_actions);
  }

  /**
   * Retrieve all our enabled macros from the Desk API.
   * 
   * @return array $data
   *   All enabled Desk macros, keyed by macro ID.
   */
  public function fetchAllMacros()
  {
    $data = [];

    // Query the API to determine how many batches we need to run.
    $api = new DeskApi('/api/v2/macros');
    $meta = $api->getDeskData();
    $total_pages = ceil($meta['total_entries'] / DESK_API_BATCH_SIZE);

    // Make API calls in batches to retrieve Macro data.
    $batch_id = 1;
    while ($batch_id <= $total_pages) {
      $api = new DeskApi('/api/v2/macros?page=' . $batch_id . '&per_page=' . DESK_API_BATCH_SIZE);
      $json = $api->getDeskData();

      if (!empty($json['_embedded']['entries'])) {
        foreach ($json['_embedded']['entries'] as $macro) {
          if ($macro['enabled'] == TRUE) {
            array_push($data, $macro);
          }
        }
      }

      $batch_id++;
    }

    echo "\nPulled " . count($data) . " Macros from Desk.";
    return $data;
  }

  /**
   * Retrieve actions for each enabled macro.
   * 
   * @param array $data
   *   Array of macros.
   * @return array $content
   *   Enabled macro actions.
   */
  public function buildMacroActions($data)
  {
    $content = [];
    foreach ($data as $macro) {
      $id = $macro['id'];
      $content[$id] = [
        'title' => $macro['name'],
        'actions' => $this->fetchMacroActions($id),
        'folders' => array_values($macro['folders']) // @todo is this useful in ZD?
      ];
    }
    return $content;
  }

  /**
   * Get Macro Actions.
   * 
   * @param int $id
   *   The Desk Macro ID.
   * @return array $actions
   *   The Macro's Actions with their types and values.
   */
  public function fetchMacroActions($id)
  {
    $api = new DeskApi('/api/v2/macros/' . $id . '/actions');
    $macro = $api->getDeskData();

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
  public function exportQuickReplies($macros_actions)
  {
    // Prep CSV file.
    $filename = 'exports/quick-replies-' . time() . '.csv';
    $fp = fopen($filename, 'w');
    fputcsv($fp, ['ID', 'Type', 'Title', 'Text']); // Header row.

    // Loop through our macros and pick out all the Quick Replies/Notes.
    // Export these to our CSV file.
    $count = 0;
    foreach ($macros_actions as $id => $row) {
      if (!empty($row['actions'])) {
        foreach ($row['actions'] as $action) {
          if (in_array($action['type'], ['set-case-quick-reply', 'set-case-note'])) {
            $quickReply = [$id, $action['type'], $row['title'], $action['value']];
            fputcsv($fp, $quickReply);
            $count++;
          }
        }
      }
    }

    fclose($fp);
    echo "\nExported " . $count . " Quick Replies to file: " . $filename;
  }
}
