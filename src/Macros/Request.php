<?php

use Illuminate\Http\Request;

/**
 * Super nice new IP that returns your localhost public IP instead of
 * returning your localhost IP.
 */
Request::macro('public_ip', function () {
    try {
        $ip = request()->ip();

        // Check for forwarded IP address
        if (request()->header('x-forwarded-for')) {
            $ip = explode(',', request()->header('x-forwarded-for'))[0];
        }

        return $ip === '127.0.0.1' ?
            trim(file_get_contents('https://ipinfo.io/ip')) :
            $ip;
    } catch (Exception $ex) {
        return request()->ip();
    }
});
