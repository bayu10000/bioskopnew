<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Pesanan #{{ $order->id }}</title>
    
    <style>
        /* CSS Umum untuk tampilan layar */
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .ticket-container { width: 450px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
        
        /* 💡 PERBAIKAN LOGO CSS */
        .ticket-header { 
            background-color: midnightblue; /* Latar Belakang Hitam/Gelap */
            color: white; /* Default teks di header (untuk 'PHILE') */
            padding: 20px; 
            text-align: center; 
        }
        .ticket-header h2 { 
            margin: 0 0 5px 0; 
            font-size: 1.8em; 
            font-weight: 800;
        }
        .ticket-header h2 .cine {
            color: #dc3545; /* Merah untuk 'CINE' */
            padding: 0;
            background-color: transparent; 
            margin-right: 2px;
        }
        /* AKHIR PERBAIKAN LOGO CSS */

        .ticket-details { padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; border-bottom: 2px dashed #ccc; }
        .detail-full { grid-column: 1 / 3; } 
        .detail-item strong { display: block; color: #555; font-size: 0.85em; margin-bottom: 3px; }
        .detail-item span { font-size: 1em; font-weight: 600; color: #333; }
        .qr-section { padding: 20px; text-align: center; }
        .qr-section img { display: block; margin: 0 auto 15px auto; }
        .status-badge { display: inline-block; padding: 8px 15px; border-radius: 5px; font-weight: bold; margin-top: 10px; }
        .valid-status { background-color: #28a745; color: white; }
        .print-btn-area { text-align: center; padding: 10px 20px 20px 20px; }
        .print-btn { background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 1em; }

        /* CSS KHUSUS UNTUK PRINT */
        @media print {
            body { 
                background: none; 
                margin: 0; 
                padding: 0;
                min-height: auto;
                display: block;
            }
            .ticket-container {
                width: 100%; 
                max-width: 450px;
                margin: 20px auto;
                box-shadow: none;
                border: 1px solid #333; 
            }
            .print-btn-area { 
                display: none; 
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        
        <div class="ticket-header">
            <h2><span class="cine">CINE</span>PHILE</h2>
            {{-- <p>Pesanan ID: {{ $order->id }}</p> --}}
        </div>

        <div class="ticket-details">
            {{-- Nama Pelanggan --}}
            <div class="detail-item detail-full">
                <strong>PELANGGAN</strong>
                <span>{{ $order->user->name ?? 'Pengguna Tidak Ditemukan' }}</span>
            </div>
            <div class="detail-item">
                <strong>USERNAME</strong>
                <span>{{ $order->user->username ?? 'Pengguna Tidak Ditemukan' }}</span>
            </div>
            
            {{-- Tanggal Pembelian --}}
            <div class="detail-item detail-full">
                <strong>TANGGAL PEMBELIAN</strong>
                <span>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('l, d F Y - H:i') ?? '-' }} WIB</span>
            </div>
            
            <div class="detail-item">
                <strong>FILM</strong>
                <span>{{ $order->showtime->film->judul ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <strong>RUANGAN</strong>
                <span>{{ $order->showtime->ruangan->nama ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <strong>TANGGAL TAYANG</strong>
                <span>{{ \Carbon\Carbon::parse($order->showtime->tanggal)->translatedFormat('d F Y') ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <strong>JAM TAYANG</strong>
                <span>{{ \Carbon\Carbon::parse($order->showtime->jam)->format('H:i') ?? '-' }} WIB</span>
            </div>
            <div class="detail-item">
                <strong>NOMOR KURSI</strong>
                <span>{{ $order->seats->pluck('nomor_kursi')->implode(', ') }} ({{ count($order->seats) }} Tiket)</span>
            </div>
            <div class="detail-item">
                <strong>TOTAL HARGA</strong>
                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="qr-section">
            @if($order->status === 'paid' && $order->qr_code_hash)
                @php
                    $qrUrl = route('ticket.view', ['hash' => $order->qr_code_hash]);
                @endphp
                
                {!! QrCode::size(150)->generate($qrUrl) !!} 

                <p class="status-badge valid-status">TIKET SUDAH DIBAYAR & VALID</p>
                <small style="display: block; color: #999; margin-top: 5px;">{{ $order->qr_code_hash }}</small>
                
            @else
                <p class="status-badge bg-warning text-dark">TIKET BELUM DIBAYAR (Status: {{ ucfirst($order->status) }})</p>
            @endif
        </div>

        <div class="print-btn-area">
            <button onclick="window.print()" class="print-btn">
                Download e-ticket
            </button>
        </div>
    </div>
</body>
</html>