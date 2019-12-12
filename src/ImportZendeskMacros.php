<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;
use stdClass;

class ImportZendeskMacros
{

  public function __construct(string $filename)
  {
    $this->filename = $filename;

    // Format for ZD import
    $macros = $this->collectMacros($this->filename);
    echo "Collecting and formatting macros for import...";

    // Update text with edits from CSE @todo
    // $edited = $this->editMacros($macros);
    // echo "\nUpdating text...";

    // Import to ZD via ZD API @todo
    // echo "\nPosting macros to Zendesk..."
  }

  /**
   * Pull Macros from CSV and format for import.
   * 
   * @return array
   *   Array of JSON objects, ready to import in ZD.
   */
  public function collectMacros(string $file)
  {
    // Retrieve JSON file.
    $string = file_get_contents($file);
    $data = json_decode($string);

    // Post to ZD with cURL.
    $count = 0;
    $converted = [];
    foreach ($data as $macro) {
      $converted[] = $this->convertMacro($macro);
    }
    return "\nConverted " . $count . " macros for Zendesk";
  }

  /**
   * Post a macro to Zendesk.
   * 
   * @param object $macro
   * @return boolean
   *   True for a successful post.
   */
  public function postMacro(StdClass $macro)
  {
    $api = new ZendeskApi('/api/v2/macros');
    if ($api->postZendeskData($macro)) {
      return true; 
    }
    return false;
  }

  /**
   * Restructure a macro object for ZD import.
   */
  public function convertMacro(array $macro)
  {
    $zd_macro = new stdClass();
    $zd_macro->title = $macro['title'];

    $zd_macro->actions = []; // @todo

    return json_encode($zd_macro);
  }

  /**
   * Return the ZD action equivalent to a 
   * given Desk action type.
   */
  static function actionMap(string $desk_action_type)
  {
    $map = [
      'set-case-status' => 'status',
      'set-case-quick-reply' => 'comment_value',
      'set-case-agent' => NULL,
      'set-case-group' => 'group_id', // Will need another map @todo
      'set-case-outbound-email-subject' => 'subject',

      // These will also need comment_mode_is_public = FALSE @todo
      'set-case-description' => 'comment_value',
      'set-case-note' => 'comment_value',

      'set-case-priority' => NULL,
      'set-case-labels' => 'set_tags',
      'append-case-labels' => 'current_tags'
    ];
    return $map[$desk_action_type];
  }

}
