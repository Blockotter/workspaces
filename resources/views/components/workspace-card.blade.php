<div
    class="h-64 w-64 m-4 rounded-lg shadow-lg relative bg-cover bg-gray-900/60 bg-blend-darken flex justify-center flex-col items-center bg-[url('{{ $workspace->getAttribute('image_url') }}')]">
    @if ($workspace->getPrice())
        <div class="absolute top-4 right-4">
            <span class="text-2xl text-white font-extrabold">
                ${{ $workspace->getPrice() }}<sub>/hr</sub>
            </span>
        </div>
    @endif
    <div class="text-center p-16">
        <h1 class="text-xl font-extrabold mb-2 text-white">{{ $workspace->getName() }}</h1>
        <p class="text-gray-100 font-bold">{{ $workspace->getAttribute('place') }}</p>
    </div>
    <!-- Push div to bottom of parent -->
    <div class="absolute bottom-0 p-4 w-full text-xl">
        <div class="float-left">
            @if ($workspace->getAmenityEmojis())
                <span class="absolute bottom-4">{{ $workspace->getAmenityEmojis() }}</span>
            @endif
        </div>
        <div class="float-right">
            @if ($workspace->getAttribute('email'))
                <a href="mailto:{{ $workspace->getAttribute('email') }}" class="hover:text-2xl mr-2">📬</a>
            @endif

            @if ($workspace->getAttribute('phone_number'))
                <a href="tel:{{ $workspace->getAttribute('phone_number') }}" class="hover:text-2xl mr-2">☎️</a>
            @endif

            @if ($workspace->getAttribute('website'))
                <a href="{{ $workspace->getAttribute('website') }}" target="_blank" class="hover:text-2xl">🌎</a>
            @endif
        </div>
    </div>
</div>
