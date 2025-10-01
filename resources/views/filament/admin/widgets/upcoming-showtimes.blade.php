<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-2">🎬 Jadwal Hari Ini & Besok</h2>

        <ul class="divide-y divide-gray-200">
            @forelse($this->getShowtimes() as $showtime)
                <li class="py-2">
                    <span class="font-semibold">{{ $showtime->film->judul }}</span>
                    <br>
                    <span class="text-sm text-gray-500">
                        {{ $showtime->tanggal }} - {{ $showtime->jam }} 
                        ({{ $showtime->ruangan->nama }})
                    </span>
                </li>
            @empty
                <li class="py-2 text-gray-400">Tidak ada jadwal tayang.</li>
            @endforelse
        </ul>
    </x-filament::card>
</x-filament::widget>
