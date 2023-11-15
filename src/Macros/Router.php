<?php

use Illuminate\Routing\Route;

Route::macro('withMiddlewareWhen', function ($condition, $middleware) {
    if (call_user_func($condition, request())) {
        $this->middleware($middleware);
    }

    return $this;
});
