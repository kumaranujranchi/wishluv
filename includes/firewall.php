<?php
/**
 * Wishluv Firewall - Basic IP-based Rate Limiting
 * Blocks IPs that request too frequently to prevent server load/shocks.
 */

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return explode(',', $ipaddress)[0];
}

$ip = get_client_ip();
if ($ip !== 'UNKNOWN' && $ip !== '127.0.0.1') {
    // We use a simple session-based rate limit first for speed/low resource usage
    // For more advanced, we would use a file/DB, but this helps catch simple bots.
    if (!isset($_SESSION)) {
        session_start();
    }

    $now = time();
    $limit = 60; // 60 requests
    $window = 60; // per 60 seconds

    if (!isset($_SESSION['rate_limit_hits'])) {
        $_SESSION['rate_limit_hits'] = [];
    }

    // Clean up old hits
    $_SESSION['rate_limit_hits'] = array_filter($_SESSION['rate_limit_hits'], function($timestamp) use ($now, $window) {
        return $timestamp > ($now - $window);
    });

    // Add current hit
    $_SESSION['rate_limit_hits'][] = $now;

    if (count($_SESSION['rate_limit_hits']) > $limit) {
        header('HTTP/1.1 429 Too Many Requests');
        header('Retry-After: 60');
        die("Too many requests from this device. Please wait a minute and try again.");
    }
}
?>
