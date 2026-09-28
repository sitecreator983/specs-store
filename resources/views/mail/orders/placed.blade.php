<x-mail::message>
# Η παραγγελία σου καταχωρήθηκε

Γεια σου {{ $order->first_name }},

Λάβαμε την παραγγελία #{{ $order->id }} και την καταχωρήσαμε επιτυχώς.

**Κωδικός παρακολούθησης:** {{ $order->tracking_code }}

**Σύνολο:** {{ number_format($order->total_cents / 100, 2, ',', '.') }}€

@foreach ($order->items as $item)
- {{ $item->product_name }} x {{ $item->quantity }} — {{ number_format($item->line_total_cents / 100, 2, ',', '.') }}€
@endforeach

<x-mail::button :url="route('orders.track', ['code' => $order->tracking_code])">
Προβολή παραγγελίας
</x-mail::button>

Θα επικοινωνήσουμε μαζί σου αν χρειαστεί επιβεβαίωση για την παράδοση.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
