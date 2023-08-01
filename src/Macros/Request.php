<?php

use Illuminate\Http\Request;

Request::macro('ip2', function () {
    try {
        return request()->ip() == '127.0.0.1' ?
        file_get_contents('https://ipinfo.io/ip') :
        request()->ip();
    } catch (\Exception $ex) {
        return request()->ip();
    }
});
