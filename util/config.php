<?php
// Error Reporting Configuration
ini_set('display_errors', '1'); // Display errors during development
ini_set('display_startup_errors', '1'); // Display errors during PHP startup
error_reporting(E_ALL); // Report all PHP errors
// Session Configuration
ini_set('session.gc_maxlifetime', '1440'); // Session lifetime (in seconds)
ini_set('session.cookie_lifetime', '1440'); // Cookie lifetime (in seconds)
ini_set('session.cookie_secure', '1'); // Use secure cookies (requires HTTPS)
ini_set('session.cookie_httponly', '1'); // Disallow JavaScript access to session cookies
ini_set('session.cookie_samesite', 'Strict'); // Restrict cross-site requests to avoid CSRF
ini_set('session.use_trans_sid', '0'); // Disable transparent SID support
ini_set('session.hash_function', 'sha256'); // Use a stronger hash function for sessions
// File Upload and POST Configuration
ini_set('upload_max_filesize', '10M'); // Maximum file upload size
ini_set('post_max_size', '12M'); // Maximum size of POST data
ini_set('max_input_time', '300'); // Max time to parse input (in seconds)
// Execution and Resource Limits
ini_set('max_execution_time', '300'); // Max script execution time (in seconds)
ini_set('memory_limit', '256M'); // Maximum amount of memory a script may consume
ini_set('output_buffering', '4096'); // Buffer output to reduce flush frequency
ini_set('zlib.output_compression', '1'); // Enable output compression for better performance
// Localization and Charset Configuration
ini_set('date.timezone', 'Europe/Rome'); // Set default timezone
ini_set('default_charset', 'UTF-8'); // Set default character encoding
// Security Hardening
ini_set('expose_php', '0'); // Hide PHP version information from HTTP headers
ini_set('allow_url_fopen', '0'); // Disable remote file handling for security
ini_set('disable_functions', 'exec,passthru,shell_exec,system'); // Disable risky PHP functions
ini_set('session.use_strict_mode', '1'); // Prevent session fixation attacks
ini_set('realpath_cache_size', '4096K'); // Increase realpath cache size for better performance
ini_set('realpath_cache_ttl', '600'); // Time-to-live for the realpath cache (in seconds)
ini_set('default_socket_timeout', '60'); // Default socket timeout (in seconds)
?>
