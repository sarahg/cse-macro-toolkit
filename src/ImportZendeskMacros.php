<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;

class ImportZendeskMacros
{

  public function __construct($filename)
  {
    $this->filename = $filename;

    // Fetch CSV and convert to JSON
    // Format for ZD import
    $macros = $this->collectMacros();
    echo "Collecting Macros for import...";

    // Import to ZD via ZD API
    $result = $this->createMacros($macros);
    echo "\nImported " . $result . " Macros to Zendesk";
  }

  /**
   * Pull Macros from CSV and format for import.
   * 
   * @return array
   *   Array of JSON objects, ready to import in ZD.
   */
  public function collectMacros()
  {
    // Convert the CSV to an array.
    $data = $this->readCSV();

    // Assemble for ZD import.
    $macros = [];
    foreach ($data as $macro) {

      if ($macro['Status'] !== 'Done') {
        continue;
      }

      $isPublic = $macro['Type'] == 'Public reply' ? TRUE : FALSE;

      $macros[] = [
        'title' => $macro['Title'],
        'actions' => [
          'comment_value' => $macro['Text'],
          'comment_mode_is_public' => $isPublic,
        ]
      ];
    }

    return $macros;
  }

  /**
   * Post Macros to the Zendesk API.
   * 
   * @return int $count
   *   Number of Macros created.
   */
  public function createMacros()
  {
    $api = new ZendeskApi();
    $result = 0;
    return $result;
  }

}
