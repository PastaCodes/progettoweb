<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
ini_set('session.gc_maxlifetime', '1440'); // Lifetime in seconds
ini_set('session.cookie_lifetime', '1440');
ini_set('session.cookie_secure', 1); // Forces secure cookie transmission
ini_set('session.cookie_httponly', 1); // Prevents access to session cookies via JavaScript
ini_set('session.cookie_secure', '1'); // Use only with HTTPS
ini_set('session.cookie_samesite', 'Strict'); // Prevent CSRF

ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');
ini_set('max_input_time', '300');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');
ini_set('output_buffering', '4096');
ini_set('date.timezone', 'Europe/Rome');
ini_set('default_charset', 'UTF-8');
ini_set('max_execution_time', '300');
ini_set('disable_functions', 'exec,passthru,shell_exec,system');
ini_set('realpath_cache_size', '4096K');
ini_set('realpath_cache_ttl', '600');
ini_set('default_socket_timeout', '60');
?>
