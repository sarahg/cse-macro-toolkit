<?php

namespace DeskMacrosToZendesk;

use Dotenv\Dotenv;
use DeskMacrosToZendesk\ExportDeskMacros;
use DeskMacrosToZendesk\ImportZendeskMacros;

if (php_sapi_name() != 'cli') {
  die("This application must be run on the command line.");
}

// All the errors!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load env vars and Composer packages.
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

// Load our desired class based on the argument passed via CLI.
$error = 'Invalid command. Please see https://github.com/sarahg/export_desk_macros#commands';
if (isset($argv[1])) {
  switch ($argv[1]) {
    case 'export-replies':
      new ExportDeskMacros();
    break;
    case 'export-all':
      new ExportExtraActions();
    break;
    case 'zendesk-import':
      new ImportZendeskMacros($_ENV['ZENDESK_IMPORT_FILE']);
    break;
    case 'delete-all':
      new DeleteZendeskMacros($ids);
    default:
      echo $error;
  }
}
else {
  echo $error;
}
