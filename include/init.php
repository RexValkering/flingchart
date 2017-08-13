<?php
/**
 *  This file contains functions we want to call on initialization, after all
 *  functionality has been loaded.
 */

// Check if the database exists, and if not, install it.
check_db();