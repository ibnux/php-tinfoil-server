<?php
/**
 * Reserved for scanning folder and add it to database
 */
include "../vendor/autoload.php";
require '../config.php';
require 'function.php';

$dbpath = '../'.$dbpath;
require '../function.php';

$db = getDatabase();

