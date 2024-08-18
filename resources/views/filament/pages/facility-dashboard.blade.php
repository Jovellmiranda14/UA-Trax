<x-filament::page>

<link rel="stylesheet" href="{{ asset('css/filament/filament/equipment.css') }}">
<h1 class="custom-heading">Facility Admin Dashboard</h1>

<!-- Ticket Queue Table -->
<table class="ticket-queue">
    <thead>
        <tr>
            <th>Ticket ID</th>
            <th>Customer Name</th>
            <th>Subject</th>
            <th>Description</th>
            <th>Department</th>
            <th>Location</th>
            <th>Priority</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($this->getTicketsProperty() as $ticket)
            <tr>
                <td>{{ $ticket->id }}</td>
                <td>{{ $ticket->email}}</td>
                <td>{{ $ticket->subject }}</td>
                <td>{{ $ticket->description }}</td>
                <td>{{ $ticket->department }}</td>
                <td>{{ $ticket->location }}</td>
                <td>{{ $ticket->priority }}</td>
                <td>{{ $ticket->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div id="ticket-details-modal" style="display:none;">
    <div class="modal-content">
        <span id="modal-close" style="cursor:pointer;">&times;</span>
        <h2>Ticket Details</h2>
        <div id="ticket-details"></div>
    </div>
</div>

<style>
    /* Modal styles */
    #ticket-details-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 600px;
        position: relative;
    }

    #modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        color: #333;
    }
</style>

</x-filament::page>
