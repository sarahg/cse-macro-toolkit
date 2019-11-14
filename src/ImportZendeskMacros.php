<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;

class ImportZendeskMacros
{

  public function __construct()
  {
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
    $macros = [];
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
