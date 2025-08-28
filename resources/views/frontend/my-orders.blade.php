<x-layout>

    <h2 class="text-xl font-bold mb-4">My Orders</h2>

    @forelse($orders as $order)
        <div class="border p-4 mb-4 rounded shadow">
            <h3 class="font-semibold text-lg">
                Order #{{ $order->id }}
                <span class="text-sm text-gray-500">
                    ({{ $order->created_at->format('d M Y H:i') }})
                </span>
            </h3>

            <ul class="mt-2 space-y-1">
                @foreach($order->seats as $seat)
                    <li class="flex items-center justify-between border-b py-1">
                        <span>
                            🎟️ Kursi: {{ $seat->nomor_kursi }} |
                            🎬 Film: {{ $order->showtime->film->judul ?? '-' }} |
                            ⏰ Jam: {{ $order->showtime->jam ?? '-' }}
                        </span>

                        <span class="px-2 py-1 rounded text-white text-sm
                            @if($order->status === 'pending') bg-gray-500
                            @elseif($order->status === 'paid') bg-green-600
                            @else bg-red-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 text-right font-bold text-lg">
                Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}
            </div>
        </div>
    @empty
        <p class="text-gray-500">Anda belum memiliki pesanan.</p>
    @endforelse

</x-layout>