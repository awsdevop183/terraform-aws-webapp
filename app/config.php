<?php
/**
 * Application Configuration
 * Reads from environment variables set by Apache
 */
return [
    'db_host' => getenv('DB_HOST') ?: '',
    'db_port' => getenv('DB_PORT') ?: '3306',
    'db_name' => getenv('DB_NAME') ?: '',
    'db_user' => getenv('DB_USER') ?: '',
    'db_pass' => getenv('DB_PASS') ?: '',
];
