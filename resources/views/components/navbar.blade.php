<nav class="bg-white shadow-sm border-b">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20">
        <div class="flex justify-between h-[91px] items-center">
            <a href="/" class="font-bold text-xl text-gray-800">Desa Dukuhtengah</a>
            <div class="flex item-center gap-6">
                @auth
                <span class="text-sm text-gray-600">{{auth()->user()->nama_lengkap}}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:underline">Masuk</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>