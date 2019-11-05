<?php

class ExportMacros
{

  public function __init()
  {
    // Retrieve API credentials from secrets.json
    // @todo Use PHP dotenv package
    $config = json_decode(file_get_contents('secrets.json'), true);

    // Make an API call to Desk and retrieve Macros.
    // Macro objects include top-level data and Actions;
    // Actions, if they exist, will include up to 24 action values.
    $endpoint = '/api/v2/macros';
    $data = new DeskApi($endpoint, $config);
    
    return macro_object($data);
  }

  /**
   * Parse macros and pull the data we want to migrate.
   * 
   * @return array $content
   *   Enabled macros with their actions.
   */
  public function build_macro_object($data)
  {

    // @todo Loop through pages
    // $total_macros = $macros['total_entries'];
    // $pages = $total_macros/100;

    $content = [];
    foreach ($data['_embedded']['entries'] as $macro) {
      if ($macro['enabled'] == TRUE) {
        $id = $macro['id'];
        $content[$id] = [
          'title' => $macro['name'],
          'actions' => get_macro_actions($id),
          'folders' => array_values($macro['folders']) // @todo is this useful in ZD?
        ];
      }
    }

    return $content;
  }

  /**
   * Get macro actions.
   * 
   * @param int $id
   *   The Desk macro ID.
   * @return array $actions
   *   Action types and values for the macro.
   */
  public function get_macro_actions($id)
  {
    $macro = fetch_desk_data('/api/v2/macros/' . $id . '/actions');
    $actions = [];
    foreach ($macro['_embedded']['entries'] as $action) {
      if ($action['enabled'] == TRUE) {
        $actions[$id] = [
          'type' => $action['type'],
          'value' => $action['value']
        ];
      }
    }
    return $actions;
  }

}