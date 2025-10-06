{{-- resources/views/filament/admin/actions/order-qr-code-modal.blade.php --}}

<div class="p-4 text-center">
    @if ($qrHash)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Hash unik tiket: <code class="font-bold text-primary-600 dark:text-primary-400">{{ $qrHash }}</code>
        </p>
        
        <div class="mx-auto" style="width: 200px; height: 200px;">
            {{-- Fungsi QrCode::generate() yang diimpor dari package SimpleSoftwareIO/QrCode --}}
            {!! QrCode::size(200)->generate($qrHash) !!}
        </div>

        <p class="mt-4 text-lg font-semibold text-green-600 dark:text-green-400">
            TIKET AKTIF
        </p>
    @else
        <p class="text-danger-600 dark:text-danger-400">
            QR Code tidak tersedia untuk pesanan ini.
        </p>
    @endif
</div>