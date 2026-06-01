<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Studio - Rumah Nada Brawijaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{ font-family:'Inter',sans-serif; }
        body{ background:#0a0a0a; }
    </style>
</head>
<body class="text-white min-h-screen">

<div class="border-b border-gray-800 bg-[#111111] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-yellow-500">Kalender Studio</h1>
            <p class="text-gray-400 text-sm sm:text-base">Lihat jadwal studio tanpa sistem booking.</p>
        </div>
        <a
            href="/"
            class="bg-yellow-500 hover:bg-yellow-400 text-black px-5 py-3 rounded-2xl font-black transition"
        >
            Kembali ke Home
        </a>
    </div>
</div>

@include('partials.calendar-highlight')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">

        <a
            href="/calendar?start={{ $prevWeek }}"
            class="bg-[#111111] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl transition"
        >
            ← Minggu Sebelumnya
        </a>

        <a
            href="/calendar?start={{ $nextWeek }}"
            class="bg-[#111111] border border-gray-700 hover:border-yellow-500 px-5 py-3 rounded-2xl transition"
        >
            Minggu Selanjutnya →
        </a>

    </div>

    <div class="overflow-x-auto rounded-3xl border border-gray-700">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr>
                    <th class="sticky left-0 top-0 z-30 bg-yellow-500 text-black p-3 sm:p-4 min-w-[110px] text-center text-sm sm:text-base font-black border border-gray-700">Waktu</th>
                    @foreach($days as $day)
                    <th data-date="{{ $day->format('Y-m-d') }}" class="sticky top-0 z-20 p-3 sm:p-4 min-w-[120px] sm:min-w-[150px] border border-gray-700 bg-[#111111] text-white">
                        <div class="font-black text-sm sm:text-base">{{ $day->locale('id')->translatedFormat('l') }}</div>
                        <div class="text-xs sm:text-sm mt-1">{{ $day->format('d-m-Y') }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($times as $time)
                <tr>
                    <td class="sticky left-0 z-20 bg-[#111111] border border-gray-700 text-center font-bold p-3 min-w-[110px]">{{ $time }}</td>
                    @foreach($days as $day)
                    <td data-date="{{ $day->format('Y-m-d') }}" class="border border-gray-700 h-24 p-2 align-top relative bg-[#101010]">
                        @if(in_array($day->format('Y-m-d'), $closedDates ?? []))
                            <div class="bg-red-500 text-white rounded-2xl p-2 h-full flex items-center justify-center text-center">
                                <div class="font-black text-xs sm:text-sm leading-tight">Studio Tutup</div>
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
                                    <div class="font-black text-xs sm:text-sm leading-tight">{{ $schedule->nama }}</div>
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

<footer class="border-t border-gray-800 mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 text-center text-sm text-gray-400 space-y-1">
        <div>© 2026 Rumah Nada Brawijaya</div>
    </div>
</footer>

</body>
</html>
