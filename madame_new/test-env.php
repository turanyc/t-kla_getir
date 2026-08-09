<?php
header('Content-Type: text/plain');
echo "THE_REQUEST: " . (isset($_SERVER['THE_REQUEST']) ? $_SERVER['THE_REQUEST'] : 'not set') . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
print_r($_SERVER);
