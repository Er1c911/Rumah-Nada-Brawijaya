<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rumah Nada Brawijaya</title>

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

    </style>

</head>

<body class="text-white min-h-screen">
    

<!-- HEADER -->

<div class="border-b border-gray-800 bg-[#111111] sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">

        <div>

            <h1 class="text-2xl sm:text-3xl font-black text-yellow-500">

                Rumah Nada Brawijaya

            </h1>

            <p class="text-gray-400 text-sm sm:text-base">

                Booking Studio Musik Online

            </p>

        </div>

        <div class="flex gap-3">

            <a
                href="/booking"
                class="bg-yellow-500 hover:bg-yellow-400 text-black px-5 py-3 rounded-2xl font-black transition"
            >

                Booking Sekarang

            </a>

            <a
                href="/calendar"
                class="bg-[#1a1a1a] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl font-bold transition"
            >

                Lihat Kalender

            </a>

        </div>

    </div>
</div>

@include('partials.calendar-highlight')

<!-- CONTENT -->

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- SUCCESS -->

    @if(session('success'))

    <div class="bg-green-500/20 border border-green-500 text-green-400 rounded-2xl px-4 py-3 mb-6">

        {{ session('success') }}

    </div>

    @endif

    <!-- NAVIGATION -->

    <div class="flex items-center justify-between mb-6">

        <a
            href="/?start={{ $prevWeek }}"
            class="bg-[#111111] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl transition"
        >

            ← Minggu Sebelumnya

        </a>

        <a
            href="/?start={{ $nextWeek }}"
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

                        @if(in_array($day->format('Y-m-d'), $closedDates ?? []))
                            <div class="bg-red-500 text-white rounded-2xl p-2 h-full flex items-center justify-center text-center">
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

                                <div class="bg-yellow-500 text-black rounded-2xl p-2 h-full flex items-center justify-center text-center">

                                    <div class="font-black text-xs sm:text-sm leading-tight">

                                        {{ $schedule->nama }}

                                    </div>

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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        <div class="flex justify-center gap-3 flex-wrap">
            <a
                href="https://wa.me/6282143857754"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-3 bg-[#25D366] hover:bg-[#1ebe57] text-white font-bold px-5 py-3 rounded-full shadow-sm transition transform hover:-translate-y-0.5 hover:shadow-lg"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-6 h-6">
                    <path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                </svg>
                Hubungi Admin via WhatsApp
            </a>
        </div>

    </div>

    <footer class="border-t border-gray-800 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 text-center text-sm text-gray-400 space-y-1">
            <div>© 2026 Rumah Nada Brawijaya</div>
            <div>
                Designed & maintained by 
                <a href="https://github.com/Er1c911" target="_blank" rel="noopener noreferrer" class="text-white underline hover:text-yellow-400">
                    Eric Fausta
                </a>
            </div>
        </div>
    </footer>

</body>
</html>