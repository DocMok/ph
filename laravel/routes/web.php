<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function() {
    return 'nothing here';
});
