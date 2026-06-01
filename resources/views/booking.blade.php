<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking Studio</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            font-family:'Inter',sans-serif;
        }

        body{
            background:#0a0a0a;
        }

        input[type="date"]::-webkit-calendar-picker-indicator{
            filter:invert(1);
            cursor:pointer;
        }

        select option{
            background:#151515;
            color:white;
        }

    </style>

</head>

<body class="min-h-screen flex items-center justify-center px-4 py-10 text-white">
    

<!-- CONTAINER -->

<div class="w-full max-w-2xl">

    <!-- CARD -->

    <div class="bg-[#111111] border border-gray-700 rounded-3xl p-6 sm:p-8 shadow-2xl">

        <!-- HEADER -->

        <div class="text-center mb-8">

            <div class="w-16 h-16 mx-auto rounded-2xl bg-yellow-500 flex items-center justify-center shadow-xl">

                <span class="text-black text-3xl font-black">

                    ♪

                </span>

            </div>

            <h1 class="text-3xl sm:text-4xl font-black text-yellow-500 mt-5">

                Booking Studio

            </h1>

            <p class="text-gray-400 mt-2 text-sm sm:text-base">

                Rumah Nada Brawijaya

            </p>

        </div>

        <!-- ERROR -->

        @if(session('error'))

        <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-2xl px-4 py-3 mb-5 text-sm">

            {{ session('error') }}

        </div>

        @endif

        <!-- SUCCESS -->

        @if(session('success'))

        <div class="bg-green-500/20 border border-green-500 text-green-400 rounded-2xl px-4 py-3 mb-5 text-sm">

            {{ session('success') }}

        </div>

        @endif

        <!-- VALIDATION -->

        @if ($errors->any())

        <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-2xl px-4 py-3 mb-5 text-sm">

            <ul class="list-disc ml-5 space-y-1">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif

        <!-- FORM -->

        <form action="/booking" method="POST">

            @csrf

            <!-- NAMA -->

            <div class="mb-5">

                <label class="block text-sm text-gray-400 mb-2">

                    Nama Lengkap

                </label>

                <input
                    type="text"
                    name="nama"
                    required
                    value="{{ old('nama') }}"
                    class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition"
                >

            </div>

            <!-- TELEPON -->

            <div class="mb-5">

                <label class="block text-sm text-gray-400 mb-2">

                    Nomor Telepon

                </label>

                <input
                    type="text"
                    name="telepon"
                    required
                    value="{{ old('telepon') }}"
                    class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition"
                >

            </div>

            <!-- TANGGAL -->

            <div class="mb-5">

                <label class="block text-sm text-gray-400 mb-2">

                    Pilih Hari

                </label>

                <input
                    type="date"
                    name="tanggal"
                    required
                    min="{{ date('Y-m-d') }}"
                    value="{{ old('tanggal') }}"
                    class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition"
                >

            </div>

            <!-- GRID JAM -->

            <div class="grid sm:grid-cols-2 gap-5 mb-6">

                <!-- JAM MULAI -->

                <div>

                    <label class="block text-sm text-gray-400 mb-2">

                        Jam Mulai

                    </label>

                    <select
                        name="jam_mulai"
                        required
                        class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition"
                    >

                        <option value="">

                            -- Pilih Jam --

                        </option>

                        @foreach($times as $time)

                        <option
                            value="{{ $time }}"
                            {{ old('jam_mulai') == $time ? 'selected' : '' }}
                        >

                            {{ $time }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <!-- JAM SELESAI -->

                <div>

                    <label class="block text-sm text-gray-400 mb-2">

                        Jam Selesai

                    </label>

                    <select
                        name="jam_selesai"
                        required
                        class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition"
                    >

                        <option value="">

                            -- Pilih Jam --

                        </option>

                        @foreach($times as $time)

                        <option
                            value="{{ $time }}"
                            {{ old('jam_selesai') == $time ? 'selected' : '' }}
                        >

                            {{ $time }}

                        </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <!-- INFO -->

            <div class="bg-[#151515] border border-gray-700 rounded-2xl p-4 mb-6">

                <h3 class="font-bold text-yellow-500 mb-2">

                    Informasi Booking

                </h3>

                <ul class="text-sm text-gray-400 space-y-2">

                    <li>• Studio buka pukul 09.00 - 21.00</li>

                    <li>• Booking minimal 1 jam</li>

                    <li>• Tidak bisa booking jadwal yang sudah terisi</li>

                    <li>• Tidak bisa booking pada hari studio tutup</li>

                    <li>• Booking harus menunggu persetujuan admin</li>

                </ul>

            </div>

            <!-- BUTTON -->

            <button
                type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-black py-3 rounded-2xl transition duration-300 shadow-lg"
            >

                Booking Sekarang

            </button>

        </form>

        <!-- BACK -->

        <a
            href="/"
            class="block text-center mt-5 text-gray-400 hover:text-yellow-500 transition"
        >

            ← Kembali ke Home

        </a>

    </div>

</div>

</body>
</html>
</body>
</html>