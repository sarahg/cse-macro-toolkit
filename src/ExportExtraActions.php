<?php

namespace DeskMacrosToZendesk;

class ExportExtraActions extends ExportDeskMacros
{

  public function __construct()
  {

    // Retrieve Macros from Desk. Skip deprecated macros.
    $deprecated_ids = [];

    // deprecated.csv was manually generated from Google Sheets
    if (($file = fopen('exports/deprecated.csv', "r")) !== FALSE) {
      $deprecated_ids = fgetcsv($file);
    }
    echo 'Fetching macros via the Desk API...';
    $macros = $this->fetchAllMacros($deprecated_ids);

    // Build an array of all Macros with their Action data.
    echo "\nFetching macro actions via the Desk API...";
    $macros_actions = $this->buildMacroActions($macros);

    // Export Actions
    echo "\nExporting macro objects to JSON...";
    $this->exportAllActions($macros_actions);
  }

  /**
   * Export all Actions to a JSON file.
   */
  public function exportAllActions($macros_actions)
  {

    $count = 0;
    $exports = [];

    foreach ($macros_actions as $id => $row) {
      $exports[$id]['title'] = $row['title'];
      
      if (!empty($row['actions'])) {
        foreach ($row['actions'] as $action) {
          $exports[$id]['actions'][] = [$action['type'], $action['value']];
          $count++;
        }
      }
    }

    $json = json_encode($exports, JSON_PRETTY_PRINT);
    $filename = 'exports/all-actions-' . time() . '.json';
    if (file_put_contents($filename, $json)) {
      echo "\nExported " . $count . " Actions to file: " . $filename;
    }
    else {
      echo "Error writing JSON. :(";
    }

  }
}
