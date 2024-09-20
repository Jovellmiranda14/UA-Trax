<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Auth\Login;

/*<?php

use Illuminate\Support\Facades\Route;
// Route::get('/', function () {
//     return view('welcome');
// });

return the login.php to the web.php
 */

// Redirect the root ('/') route to the login page
Route::get('/', function () {
    return redirect('/user/login');
});

// Define the login route to point to your custom login logic
// Route::get('/login', [Login::class, 'render'])
//     ->name('login');

  use App\Http\Controllers\TicketController;

  Route::get('/tickets/{id}', [TicketController::class, 'show']);