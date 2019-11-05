<?php

use DeskMacrosToZendesk\DeskApi;
//use DeskMacrosToZendesk\Google;

/**
 * Return Quick Replies in a JSON object.
 */
class ExportQuickReplies
{

  function __construct()
  {
    // @todo  retrieve macros from ExportDeskMacros.php JSON files
    $macros = [];
    $quick_replies = quick_replies_to_json($macros);
  }

  /**
   * Retrieve Quick Reply text from the main Macros object.
   * @param array $macros
   *   Array of built-out macro objects, including Actions.
   * @return array
   *   JSON object of Quick Replies, keyed by titles.
   */
  function quick_replies_to_json($macros)
  {
    $quick_replies = [];

    // Pull the ID, name and text for quick replies.
    foreach ($macros as $id => $macro) {
      if ($macro['actions'][$id]['type'] == "set-case-quick-reply") {
        $quick_replies[] = [
          'id' => $id,
          'name' => $macro['title'],
          'text' => $macro['actions'][$id]['value']
        ];
      }
    }

    return json_encode($quick_replies);
  }

  /**
   * Push reply objects to a Google Spreadsheet.
   */
  public function repliesToGoogleSheet()
  {
    // @todo
  }

}