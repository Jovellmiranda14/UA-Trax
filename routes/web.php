<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
 Route::get('/', function () {
      return view('welcome');
  });


  use App\Http\Controllers\TicketController;

  Route::get('/tickets/{id}', [TicketController::class, 'show']);