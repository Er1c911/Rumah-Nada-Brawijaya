<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin</title>

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

    </style>

</head>

<body class="min-h-screen flex items-center justify-center px-4">
    

    <!-- CARD -->

    <div class="w-full max-w-md bg-[#111111] border border-gray-700 rounded-3xl p-8 shadow-2xl">

        <!-- TITLE -->

        <div class="text-center mb-6 sm:mb-8">

            <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-2xl bg-yellow-500 flex items-center justify-center shadow-xl">

                <span class="text-black text-2xl sm:text-3xl font-black">

                    ♪

                </span>

            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-yellow-500 mt-4 sm:mt-5">

                Login Admin

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

        <!-- FORM -->

        <form action="/login" method="POST">

            @csrf

            <!-- USERNAME -->

            <div class="mb-5">

                <label class="block text-sm text-gray-400 mb-2">

                    Username

                </label>

                <input
                    type="text"
                    name="username"
                    required
                    class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500"
                >

            </div>

            <!-- PASSWORD -->

            <div class="mb-6">

                <label class="block text-sm text-gray-400 mb-2">

                    Password

                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full bg-[#151515] border border-gray-700 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-yellow-500"
                >

            </div>

            <!-- BUTTON -->

            <button
                type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-black py-2 sm:py-3 rounded-2xl transition duration-300 shadow-lg text-sm sm:text-base"
            >

                Login

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

    <!-- ANTI INSPECT -->

    <script>

        /*
        |--------------------------------------------------------------------------
        | Disable Right Click
        |--------------------------------------------------------------------------
        */

        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        /*
        |--------------------------------------------------------------------------
        | Disable Shortcut Inspect
        |--------------------------------------------------------------------------
        */

        document.onkeydown = function(e) {

            // F12
            if (e.keyCode == 123) {
                return false;
            }

            // CTRL + SHIFT + I
            if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
                return false;
            }

            // CTRL + SHIFT + J
            if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
                return false;
            }

            // CTRL + U
            if (e.ctrlKey && e.keyCode == 85) {
                return false;
            }

            // CTRL + S
            if (e.ctrlKey && e.keyCode == 83) {
                return false;
            }

        };

        /*
        |--------------------------------------------------------------------------
        | Disable Drag
        |--------------------------------------------------------------------------
        */

        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });

        /*
        |--------------------------------------------------------------------------
        | Disable Select Text
        |--------------------------------------------------------------------------
        */

        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });

    </script>

</body>
</html>