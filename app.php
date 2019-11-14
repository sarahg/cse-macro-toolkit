<?php

namespace DeskMacrosToZendesk;

use DeskMacrosToZendesk\ExportDeskMacros;

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

// All the errors!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

// Load our desired class based on the argument.
switch ($argv[1]) {
  case 'desk-export':
    $macros = new ExportDeskMacros();
  break;
  case 'zendesk-import':
    $macros = new ImportZendeskMacros();
  break;
}
