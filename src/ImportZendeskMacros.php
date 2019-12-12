<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;

class ImportZendeskMacros
{

  public function __construct($filename)
  {
    $this->filename = $filename;

    // Format for ZD import
    $this->collectMacros($this->filename);
    echo "Collecting Macros for import...";

    // Update text with edits from CSE @todo

    // Import to ZD via ZD API @todo

  }

  /**
   * Pull Macros from CSV and format for import.
   * 
   * @return array
   *   Array of JSON objects, ready to import in ZD.
   */
  public function collectMacros($file)
  {
    // Retrieve JSON file.
    $string = file_get_contents($file);
    $data = json_decode($string);

    // Post to ZD with cURL.
    $count = 0;
    $types = [];
    foreach ($data as $macro) {
      foreach ($macro->actions as $action) {
        $types[] = $action->type;
      }
    }

    $unique_types = array_unique($types);
    krumo($unique_types);

      /*$converted = $this->convertMacro($macro);

      $api = new ZendeskApi('/api/v2/macros');
      $post = $api->postZendeskData($converted);

      if ($post) {
        $count++;
      }

    return "\nCreated " . $count . " macros in Zendesk!";*/
  }

  /**
   * Restructure a macro object for ZD import.
   */
  public function convertMacro($macro)
  {

  }

  /**
   * Return the ZD action equivalent to a 
   * given Desk action type.
   */
  static function actionMap($desk_action_type)
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
