<?php

/**
 * @file app.php
 */

 /*if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}*/

// All the errors!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer packages.
require __DIR__ . '/vendor/autoload.php';
// @todo register custom classes

$arg = 'macros'; // @todo pass through command-line input
switch ($arg) {
  case 'macros':
    $macros = new ExportDeskMacros();
    break;
  case 'quickreplies':
    $replies = new ExportQuickReplies();
    break;
}
