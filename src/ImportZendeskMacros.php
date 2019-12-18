<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;
use StdClass; // not necessary but VS Code made me do it

class ImportZendeskMacros
{

  public function __construct(string $filename)
  {
    $this->filename = $filename;

    // Retrieve all macros and merge in our text updates.
    $macros = $this->mergeMacros($this->filename);
    echo "Fetching and updating macros for import...";

    // Format objects for ZD.
    // echo "\nFormatting macros..."

    // Post to ZD.
    // echo "\nPosting macros to Zendesk..."
  }

  /**
   * Pull Macros from CSV and format for import.
   * 
   * @return array
   *   Array of JSON objects, ready to import in ZD.
   */
  public function mergeMacros(string $file)
  {

    // Get JSON of edited replies (which we pulled from our Google Sheet via gsjson),
    // and wrangle it a little to make the next step easier.
    $string_edited = file_get_contents('exports/replies-edited.json');
    $edited_replies = json_decode($string_edited);
    $edits = [];
    foreach ($edited_replies as $reply) {
      $edits[$reply->id] = $reply->text;
    }

    // Retrieve JSON of unedited, non-deprecated macros + actions.
    $string_orig = file_get_contents($file);
    $macros = json_decode($string_orig);
    
    // Merge edited replies into our main array of macros.
    $count = 0;
    foreach ($macros as $desk_id => $macro) {
      if (isset($macro->actions)) {
        foreach ($macro->actions as $key => $action) {
          // If there's an edited version of this reply/note, pull it in.
          if (isset($edits[$desk_id]) && in_array($action->type, ['set-case-quick-reply', 'set-case-note'])) {
            $macros->{$desk_id}->actions[$key]->values = $edits[$desk_id];
            $count++;
          }
        }
      }
    }

    echo "\nUpdated text on " . $count . " macros...";
    return $macros;
  }

  /**
   * Restructure a macro object for ZD import.
   */
  public function convertMacro(array $macro)
  {
    $zd_macro = new stdClass();
    $zd_macro->title = $macro['title'];

    $zd_macro->actions = []; // @todo see $this->actionMap

    return json_encode($zd_macro);
  }

  /**
   * Post a single macro to Zendesk.
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
   * Return the ZD action equivalent to a Desk action type.
   */
  static function actionMap(string $desk_action_type)
  {
    $map = [
      'set-case-outbound-email-subject' => 'subject',
      'set-case-quick-reply' => 'comment_value',

      // These will both need another map @todo
      'set-case-status' => 'status',
      'set-case-group' => 'group_id',

      // These will also need comment_mode_is_public = FALSE in ZD @todo
      'set-case-description' => 'comment_value',
      'set-case-note' => 'comment_value',

      // These are arrays in Desk but strings in ZD @todo
      'set-case-labels' => 'set_tags',
      'append-case-labels' => 'current_tags',

      // Deprecating these.
      'set-case-agent' => NULL,
      'set-case-priority' => NULL
    ];
    return $map[$desk_action_type];
  }

}
