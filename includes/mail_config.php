<?php
// mail_config.php — put your Gmail / app password here
// define('MAIL_HOST', 'smtp.gmail.com');
// define('MAIL_PORT', 587);
// define('MAIL_USER', 'ahmedsahal@gmail.com');        // <- Thay thế bằng Gmail của bạn
// define('MAIL_PASS', 'Your App PAssword');   // <- Thay thế bằng Mật khẩu ứng dụng từ Google
// define('MAIL_FROM', 'ahmedsahal@gmail.com');
// define('MAIL_FROM_NAME', 'University SMS');
// define('MAIL_SECURE', 'tls'); // use 'tls' for port 587

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die('.env file not found');
}

$env = parse_ini_file($envPath);

define('MAIL_HOST', $env['MAIL_HOST']);
define('MAIL_PORT', $env['MAIL_PORT']);
define('MAIL_USER', $env['MAIL_USER']);
define('MAIL_PASS', $env['MAIL_PASS']);
define('MAIL_FROM', $env['MAIL_FROM']);
define('MAIL_FROM_NAME', $env['MAIL_FROM_NAME']);
define('MAIL_SECURE', $env['MAIL_SECURE']);


<?php
require_once __DIR__ . '/../includes/mail_config.php';

echo MAIL_HOST . '<br>';
echo MAIL_USER . '<br>';
