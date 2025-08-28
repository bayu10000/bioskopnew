<h1>Daftar Order Details</h1>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Order</th>
            <th>Seat</th>
            <th>Showtime</th>
            <th>Film</th>
            <th>Harga</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($details as $d)
        <tr>
            <td>{{ $d->id }}</td>
            <td>{{ $d->order?->user?->name ?? '-' }}</td>
            <td>{{ $d->order_id }}</td>
            <td>{{ $d->seat?->nomor_kursi ?? '-' }}</td>
            <td>
                {{ $d->showtime?->tanggal }} {{ $d->showtime?->jam }}
            </td>
            <td>{{ $d->showtime?->film?->judul ?? '-' }}</td>
            <td>{{ number_format($d->harga ?? 0, 0, ',', '.') }}</td>
            <td>{{ $d->status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
