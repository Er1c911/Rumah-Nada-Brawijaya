<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            font-family:'Inter',sans-serif;
        }

        body{
            background:#0a0a0a;
            user-select:none;
            -webkit-user-select:none;
            -ms-user-select:none;
        }

        input[type="date"].date-input {
            -webkit-appearance: none;
            appearance: none;
        }

        input[type="date"].date-input::-webkit-calendar-picker-indicator {
            display: none;
        }

        input[type="date"].date-input::-webkit-clear-button,
        input[type="date"].date-input::-webkit-inner-spin-button {
            display: none;
        }

        input[type="date"].date-input::-ms-expand {
            display: none;
        }

    </style>

</head>

<body class="text-white min-h-screen">
    

<!-- HEADER -->

<div class="border-b border-gray-800 bg-[#111111] sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">

        <div>

            <h1 class="text-2xl sm:text-3xl font-black text-yellow-500">

                Dashboard Admin

            </h1>

            <p class="text-gray-400 text-sm sm:text-base">

                Rumah Nada Brawijaya

            </p>

        </div>

        <a
            href="/logout"
            class="bg-red-500 hover:bg-red-400 text-white px-4 py-2 rounded-2xl text-sm font-bold transition"
        >

            Logout

        </a>

    </div>

</div>

<!-- CONTENT -->

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    @if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-400 rounded-3xl px-4 py-3 mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-3xl px-4 py-3 mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-3xl px-4 py-3 mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- SPREADSHEET DATA -->

    <div class="mb-10">
        <div class="bg-[#111111] border border-gray-700 rounded-3xl p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-black text-yellow-500">Spreadsheet Data</h2>
                <a
                    href="{{ $scheduleSpreadsheetUrl }}"
                    target="_blank"
                    class="bg-yellow-500 hover:bg-yellow-400 text-black px-5 py-3 rounded-2xl font-bold transition"
                >
                    Buka Jadwal Spreadsheet
                </a>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                Jadwal terakhir diperbarui: {{ $scheduleSpreadsheetUpdatedAt }}
            </div>
        </div>
    </div>

    <!-- BOOKING PENDING -->

    <div class="mb-10">

        <h2 class="text-2xl font-black text-yellow-500 mb-5">

            Booking Pending

        </h2>

        @if($bookings->count() == 0)

        <div class="bg-[#111111] border border-gray-700 rounded-3xl p-6 text-gray-400">

            Tidak ada booking pending

        </div>

        @else

        <div class="grid gap-5">

            @foreach($bookings as $booking)

            <div class="bg-[#111111] border border-gray-700 rounded-3xl p-5">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                    <!-- INFO -->

                    <div>

                        <h3 class="text-xl font-black text-yellow-500 mb-2">

                            {{ $booking->nama }}

                        </h3>

                        <div class="space-y-1 text-gray-300 text-sm sm:text-base">

                            <p>

                                Nomor Telepon :
                                {{ $booking->telepon }}

                            </p>

                            <p>

                                Tanggal :
                                {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}

                            </p>

                            <p>

                                Jam :
                                {{ $booking->jam_mulai }}
                                -
                                {{ $booking->jam_selesai }}

                            </p>

                        </div>

                    </div>

                    <!-- BUTTON -->

                    <div class="flex gap-3">

                        <!-- APPROVE -->

                        <form action="/approve/{{ $booking->id }}" method="POST">

                            @csrf

                            <button
                                class="bg-green-500 hover:bg-green-400 text-white px-5 py-3 rounded-2xl font-bold transition"
                            >

                                Tambah ke Jadwal

                            </button>

                        </form>

                        <!-- REJECT -->

                        <form action="/reject/{{ $booking->id }}" method="POST">

                            @csrf

                            <button
                                class="bg-red-500 hover:bg-red-400 text-white px-5 py-3 rounded-2xl font-bold transition"
                            >

                                Tolak

                            </button>

                        </form>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endif

    </div>

    <!-- MANUAL SCHEDULE -->

    <div class="mb-10">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-5">

            <h2 class="text-2xl font-black text-yellow-500">

                Tambah Jadwal Manual

            </h2>

            <p class="text-gray-400 text-sm max-w-xl">

                Gunakan fitur ini ketika ada user yang belum menggunakan sistem booking.

            </p>

        </div>

        <div class="bg-[#111111] border border-gray-700 rounded-3xl p-6">

            <form action="/admin/schedule" method="POST" class="grid gap-4 lg:grid-cols-2">

                @csrf

                <div>
                    <label class="block text-sm text-gray-400 mb-2">Nama</label>
                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        required
                        class="w-full rounded-2xl border border-gray-700 bg-[#0f0f0f] px-4 py-3 text-white focus:border-yellow-500 focus:outline-none"
                    >
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">Nomor Telepon (opsional)</label>
                    <input
                        type="text"
                        name="telepon"
                        value="{{ old('telepon') }}"
                        class="w-full rounded-2xl border border-gray-700 bg-[#0f0f0f] px-4 py-3 text-white focus:border-yellow-500 focus:outline-none"
                    >
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="tanggal">Tanggal</label>
                    <div class="relative">
                        <input
                            id="tanggal"
                            type="date"
                            name="tanggal"
                            value="{{ old('tanggal') }}"
                            required
                            class="date-input w-full rounded-2xl border border-gray-700 bg-[#0f0f0f] px-4 py-3 pr-11 text-white focus:border-yellow-500 focus:outline-none"
                        >
                        <button type="button" onclick="openDatePicker()" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-white focus:outline-none" aria-label="Pilih tanggal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1 .9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zm0-13H5V6h14v1z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Jam Mulai</label>
                        <select
                            name="jam_mulai"
                            required
                            class="w-full rounded-2xl border border-gray-700 bg-[#0f0f0f] px-4 py-3 text-white focus:border-yellow-500 focus:outline-none"
                        >
                            @for($i = 9; $i <= 22; $i++)
                                <option value="{{ sprintf('%02d.00', $i) }}" {{ old('jam_mulai') == sprintf('%02d.00', $i) ? 'selected' : '' }}>{{ sprintf('%02d.00', $i) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Jam Selesai</label>
                        <select
                            name="jam_selesai"
                            required
                            class="w-full rounded-2xl border border-gray-700 bg-[#0f0f0f] px-4 py-3 text-white focus:border-yellow-500 focus:outline-none"
                        >
                            @for($i = 10; $i <= 23; $i++)
                                <option value="{{ sprintf('%02d.00', $i) }}" {{ old('jam_selesai') == sprintf('%02d.00', $i) ? 'selected' : '' }}>{{ sprintf('%02d.00', $i) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button
                        class="bg-yellow-500 hover:bg-yellow-400 text-black px-5 py-3 rounded-2xl font-bold transition"
                    >
                        Simpan Jadwal Manual
                    </button>
                </div>

            </form>

        </div>

    </div>

    <!-- CLOSE DAY -->

    <div class="mb-10">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-5">

            <h2 class="text-2xl font-black text-red-500">

                Tutup Operasional Studio

            </h2>

            <p class="text-gray-400 text-sm max-w-xl">

                Pilih hari yang ingin ditutup dari operasi studio.

            </p>

        </div>

        <div class="bg-[#111111] border border-red-700 rounded-3xl p-6">

            <form action="/admin/close-day" method="POST" class="grid gap-4 lg:grid-cols-2">

                @csrf

                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="tanggal_tutup">Tanggal Tutup</label>
                    <div class="relative">
                        <input
                            id="tanggal_tutup"
                            type="date"
                            name="tanggal_tutup"
                            value="{{ old('tanggal_tutup') }}"
                            required
                            class="date-input w-full rounded-2xl border border-red-700 bg-[#0f0f0f] px-4 py-3 pr-11 text-white focus:border-red-500 focus:outline-none"
                        >
                        <button type="button" onclick="openCloseDatePicker()" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-white focus:outline-none" aria-label="Pilih tanggal tutup">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1 .9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zm0-13H5V6h14v1z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2">Alasan (opsional)</label>
                    <input
                        type="text"
                        name="alasan"
                        value="{{ old('alasan') }}"
                        class="w-full rounded-2xl border border-red-700 bg-[#0f0f0f] px-4 py-3 text-white focus:border-red-500 focus:outline-none"
                    >
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button
                        class="bg-red-500 hover:bg-red-400 text-white px-5 py-3 rounded-2xl font-bold transition"
                    >
                        Tutup Studio
                    </button>
                </div>

            </form>

            @if($closures->count())
                <div class="mt-6 rounded-3xl border border-red-700 bg-[#0f0f0f] p-5">
                    <h3 class="text-lg font-black text-red-500 mb-4">Hari Tutup Saat Ini</h3>
                    <div class="grid gap-3">
                        @foreach($closures as $closure)
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-red-700 bg-[#111111] p-4">
                                <div>
                                    <div class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($closure->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</div>
                                    @if($closure->alasan)
                                        <div class="text-xs text-gray-500 mt-1">Alasan: {{ $closure->alasan }}</div>
                                    @endif
                                </div>
                                <form action="/admin/open-day/{{ $closure->id }}" method="POST">
                                    @csrf
                                    <button class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-2xl font-bold transition">
                                        Buka Kembali
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- NAVIGATION -->

    <div class="flex items-center justify-between mb-6">

        <a
            href="/admin?start={{ $prevWeek }}"
            class="bg-[#111111] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl transition"
        >

            ← Minggu Sebelumnya

        </a>

        <a
            href="/admin?start={{ $nextWeek }}"
            class="bg-[#111111] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl transition"
        >

            Minggu Selanjutnya →

        </a>

    </div>

    <!-- CALENDAR -->

    <div class="overflow-x-auto rounded-3xl border border-gray-700">

        <table class="w-full border-collapse text-sm">

            <!-- HEADER -->

            <thead>

                <tr>

                    <!-- WAKTU -->

                    <th class="sticky left-0 top-0 z-30 bg-yellow-500 text-black p-3 sm:p-4 min-w-[110px] text-center text-sm sm:text-base font-black border border-gray-700">

                        Waktu

                    </th>

                    <!-- HARI -->

                    @foreach($days as $day)

                    <th data-date="{{ $day->format('Y-m-d') }}" class="
                        sticky top-0 z-20
                        p-3 sm:p-4
                        min-w-[120px] sm:min-w-[150px]
                        border border-gray-700
                        bg-[#111111]
                        text-white
                    ">

                        <div class="font-black text-sm sm:text-base">

                            {{ $day->locale('id')->translatedFormat('l') }}

                        </div>

                        <div class="text-xs sm:text-sm mt-1">

                            {{ $day->format('d-m-Y') }}

                        </div>

                    </th>

                    @endforeach

                </tr>

            </thead>

            <!-- BODY -->

            <tbody>

                @foreach($times as $time)

                <tr>

                    <!-- JAM -->

                    <td class="sticky left-0 z-20 bg-[#111111] border border-gray-700 text-center font-bold p-3 min-w-[110px]">

                        {{ $time }}

                    </td>

                    <!-- HARI -->

                    @foreach($days as $day)

                    <td data-date="{{ $day->format('Y-m-d') }}" class="
                        border border-gray-700
                        h-24
                        p-2
                        align-top
                        relative
                        bg-[#101010]
                    ">

                        @if(in_array($day->format('Y-m-d'), $closedDates))

                            <div class="bg-red-500 text-white rounded-2xl p-2 h-full flex flex-col items-center justify-center text-center">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="mb-2 h-6 w-6 fill-current">
                                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 15H6V8h12v11zm-6-3.5l4-4-1.4-1.4L12 13.1 10.4 11.5 9 12.9l4 4z"/>
                                </svg>

                                <div class="font-black text-xs sm:text-sm leading-tight">

                                    Studio Tutup

                                </div>

                            </div>

                        @else

                            @foreach($schedules as $schedule)

                            @if(

                                $schedule->tanggal == $day->format('Y-m-d')

                                &&

                                $schedule->jam_mulai <= explode(' - ', $time)[0]

                                &&

                                $schedule->jam_selesai > explode(' - ', $time)[0]

                            )

                            <div class="bg-yellow-500 text-black rounded-2xl p-2 h-full flex flex-col items-center justify-center text-center">

                                <!-- BUTTON USER -->

                                <button
                                    onclick="openModal(
                                        '{{ $schedule->nama }}',
                                        '{{ $schedule->telepon ?? '-' }}',
                                        '{{ $schedule->tanggal }}',
                                        '{{ $schedule->jam_mulai }}',
                                        '{{ $schedule->jam_selesai }}'
                                    )"
                                    class="font-black text-xs sm:text-sm leading-tight hover:underline"
                                >

                                    {{ $schedule->nama }}

                                </button>

                                <!-- DELETE -->

                                <form
                                    action="/delete-schedule/{{ $schedule->id }}"
                                    method="POST"
                                    class="mt-2"
                                >

                                    @csrf

                                    <button
                                        onclick="return confirm('Hapus jadwal ini?')"
                                        class="bg-red-500 hover:bg-red-400 text-white text-[10px] sm:text-xs px-3 py-1 rounded-xl transition"
                                    >

                                        Hapus

                                    </button>

                                </form>

                            </div>

                            @endif

                        @endforeach

                        @endif

                    </td>

                    @endforeach

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

<!-- MODAL -->

<div
    id="userModal"
    class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[999]"
>

    <div class="bg-[#111111] border border-gray-700 rounded-3xl p-8 w-[90%] max-w-md">

        <!-- TITLE -->

        <h2 class="text-2xl font-black text-yellow-500 mb-6 text-center">

            Detail Booking

        </h2>

        <!-- CONTENT -->

        <div class="space-y-4 text-sm sm:text-base">

            <div>

                <p class="text-gray-400">

                    Nama User

                </p>

                <p
                    id="modalNama"
                    class="font-bold text-white mt-1"
                ></p>

            </div>

            <div>

                <p class="text-gray-400">

                    Nomor Telepon

                </p>

                <p
                    id="modalTelepon"
                    class="font-bold text-white mt-1"
                ></p>

            </div>

            <div>

                <p class="text-gray-400">

                    Tanggal Booking

                </p>

                <p
                    id="modalTanggal"
                    class="font-bold text-white mt-1"
                ></p>

            </div>

            <div>

                <p class="text-gray-400">

                    Jam Booking

                </p>

                <p
                    id="modalJam"
                    class="font-bold text-white mt-1"
                ></p>

            </div>

        </div>

        <!-- BUTTON -->

        <button
            onclick="closeModal()"
            class="w-full mt-8 bg-yellow-500 hover:bg-yellow-400 text-black font-black py-3 rounded-2xl transition"
        >

            Tutup

        </button>

    </div>

</div>

<!-- ANTI INSPECT -->

<script>

    /*
    |--------------------------------------------------------------------------
    | MODAL DETAIL USER
    |--------------------------------------------------------------------------
    */

    function openModal(nama, telepon, tanggal, jamMulai, jamSelesai)
    {
        document.getElementById('userModal').classList.remove('hidden');

        document.getElementById('userModal').classList.add('flex');

        document.getElementById('modalNama').innerText =
            nama;

        document.getElementById('modalTelepon').innerText =
            telepon;

        document.getElementById('modalTanggal').innerText =
            tanggal;

        document.getElementById('modalJam').innerText =
            jamMulai + ' - ' + jamSelesai;
    }

    function closeModal()
    {
        document.getElementById('userModal').classList.add('hidden');

        document.getElementById('userModal').classList.remove('flex');
    }

    function openDatePicker()
    {
        const input = document.getElementById('tanggal');

        if (!input) {
            return;
        }

        if (typeof input.showPicker === 'function') {
            input.showPicker();
            return;
        }

        input.focus();
    }

    function openCloseDatePicker()
    {
        const input = document.getElementById('tanggal_tutup');

        if (!input) {
            return;
        }

        if (typeof input.showPicker === 'function') {
            input.showPicker();
            return;
        }

        input.focus();
    }

    /*
    |--------------------------------------------------------------------------
    | DISABLE RIGHT CLICK
    |--------------------------------------------------------------------------
    */

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    /*
    |--------------------------------------------------------------------------
    | DISABLE INSPECT
    |--------------------------------------------------------------------------
    */

    document.onkeydown = function(e) {

        if (e.keyCode == 123) {
            return false;
        }

        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            return false;
        }

        if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            return false;
        }

        if (e.ctrlKey && e.keyCode == 85) {
            return false;
        }

        if (e.ctrlKey && e.keyCode == 83) {
            return false;
        }

    };

    /*
    |--------------------------------------------------------------------------
    | DISABLE DRAG
    |--------------------------------------------------------------------------
    */

    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
    });

    /*
    |--------------------------------------------------------------------------
    | DISABLE SELECT
    |--------------------------------------------------------------------------
    */

    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
    });

</script>

@include('partials.calendar-highlight')

</body>
</html>