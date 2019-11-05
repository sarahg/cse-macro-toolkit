<?php

/**
 * Return Quick Replies in a JSON object.
 */
class Replies
{

  // @todo pass in macros object

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