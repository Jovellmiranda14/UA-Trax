<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class EquipmentAdminController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all(); // Retrieve all tickets from the database
        return view('filament.pages.equipment-admin-dashboard', compact('tickets'));
    }
}
