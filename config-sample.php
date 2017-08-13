<?php
/**
 *  This is your Flingchart config file. You can enter your database credentials here.
 *  If the database you're using is also used for other purposes (e.g. Wordpress), I
 *  advice making a seperate account and giving it the rights for this application's
 *  tables only. 
 */

// The name of your database.
define('DB_NAME', 'flingchart');

// MySQL database username.
define('DB_USER', 'root');

// MySQL database password
define('DB_PASSWORD', '');

// MySQL hostname
define('DB_HOST', 'localhost');

// Database Charset to use in creating database tables.
define('DB_CHARSET', 'utf8');

// The Database Collate type. Don't change this if in doubt.
define('DB_COLLATE', '');

// If your flingchart is hosted on a subdomain, use this to set the domain.
// E.g., if your flingchart resides on http://test.localhost/chart, put the
// site root as '/chart/'. If it is hosted on http://test.localhost/, put
// the site root as '/'.
define('SITE_ROOT', '/');

// Your flingchart prefix. Change this if you wish to host multiple
// flingcharts on one site.
define('DB_PREFIX', '');

// The height of your graph.
define('GRAPH_HEIGHT', 600);

// The width of your graph.
define('GRAPH_WIDTH', 1000);
