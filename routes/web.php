<?php

use Illuminate\Support\Facades\Route;


// Redirect the root ('/') route to the login page
Route::get('/', function () {
    return redirect('/login');
});
