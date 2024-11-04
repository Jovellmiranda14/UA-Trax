<div class="p-4 bg-white rounded-lg shadow">
    <h2 class="text-lg font-bold mb-4">Ticket Overview</h2>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <h3 class="text-md font-semibold">Latest Ticket Queues</h3>
            <ul>
                @forelse ($latestQueues as $queue)
                    <li>{{ $queue->name }} ({{ $queue->created_at->diffForHumans() }})</li>
                @empty
                    <li>No latest ticket queues available.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h3 class="text-md font-semibold">Latest Accepted Tickets</h3>
            <ul>
                @forelse ($latestAccepted as $ticket)
                    <li>{{ $ticket->subject }} ({{ $ticket->accepted_at->diffForHumans() }})</li>
                @empty
                    <li>No latest accepted tickets available.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h3 class="text-md font-semibold">Latest Resolved Tickets</h3>
            <ul>
                @forelse ($latestResolved as $ticket)
                    <li>{{ $ticket->subject }} ({{ $ticket->resolved_at->diffForHumans() }})</li>
                @empty
                    <li>No latest resolved tickets available.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
