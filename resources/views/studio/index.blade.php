<!DOCTYPE html>
<html>
<head>
    <title>Studio Musik</title>
</head>
<body>
    

    <h1>Data Booking Studio Musik</h1>

    {{-- Jika admin sudah login --}}
    @if(session('admin_logged_in'))

        <a href="{{ route('studio.create') }}">
            Tambah Booking
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top:10px;">
            @csrf

            <button type="submit">
                Logout Admin
            </button>
        </form>

    @else

        <a href="/login">
            Login Admin
        </a>

    @endif

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nama Band</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Durasi</th>

            {{-- Kolom aksi hanya untuk admin --}}
            @if(session('admin_logged_in'))
                <th>Aksi</th>
            @endif
        </tr>

        @foreach($studio as $s)

        <tr>

            <td>{{ $s->id }}</td>

            <td>{{ $s->nama_band }}</td>

            <td>{{ $s->tanggal }}</td>

            <td>{{ $s->jam }}</td>

            <td>{{ $s->durasi }} Jam</td>

            {{-- Tombol edit dan delete hanya untuk admin --}}
            @if(session('admin_logged_in'))

            <td>

                <a href="{{ route('studio.edit', $s->id) }}">
                    Edit
                </a>

                <br><br>

                <form action="{{ route('studio.destroy', $s->id) }}" method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="submit">
                        Delete
                    </button>

                </form>

            </td>

            @endif

        </tr>

        @endforeach

    </table>

</body>
</html>