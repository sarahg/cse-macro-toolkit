<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ZendeskApi;

class DeleteZendeskMacros
{

  public function __construct(string $ids)
  {
    $this->ids = $ids;

    // This is destructive.
    // Add a confirmation step.
    // @todo

    $deleted = $this->deleteMacros($ids);
    if ($deleted) {
      echo "Deleted macros.";
    }
  }

  /**
   * Delete macros.
   * 
   * @param string $ids
   *   Comma-separated list of macro IDs for
   *   macros we're deleting, or 'all' to delete
   *   everything.
   * 
   * @return boolean
   */
  public function deleteMacros(string $ids)
  {
      if ($ids == 'all') {
        // Retrieve list of IDs for all macros
        $ids = $this->getAllMacroIDs();

        // @todo Export a backup. 
      }

      // API call to ZD to run the delete.
      return true;
  }

  /**
   * Get all our macro IDs.
   */
  public function getAllMacroIDs()
  {
      $ids = '';
      return $ids;
  }


}