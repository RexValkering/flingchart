<?php
// Check for config.
if (!file_exists(dirname(__FILE__) . '/../config.php')) {
    echo "Graph not yet configured. Please configure the graph by copying config-sample.php to config.php and entering the correct information.";
    exit();
}

// First, the site configuration file.
require_once(dirname(__FILE__) . '/../config.php');

// Dependent on the configuration is the database file.
require_once(dirname(__FILE__) . '/database.php');

// Finish with the initialization file.
require_once(dirname(__FILE__) . '/init.php');