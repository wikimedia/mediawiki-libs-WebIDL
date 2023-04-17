<?php
$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config-library.php';

$cfg['directory_list'][] = 'tests';
$cfg['suppress_issue_types'] = [];

# Exclude peg-generated output
$cfg['exclude_file_list'][] = "src/Grammar.php";

return $cfg;
