<?php
// The deploy workflow generates config.local.php from the GitHub secrets DB_PASS and
// ADMIN_PASSWORD (plain text; it is hashed during the build). Never commit config.local.php.
// For a manual setup, copy this file to config.local.php on the server.
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_CHARSET', 'utf8mb4');

// Generate with: php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
define('ADMIN_PASSWORD_HASH', '$2y$10$replace-with-a-real-bcrypt-hash');
