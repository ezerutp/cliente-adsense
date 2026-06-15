@props([
    'cities' => [],
    'categories' => [],
    'labels',
])

<form action="{{ $labels['action'] }}" method="GET" class="mx-auto grid w-full max-w-5xl gap-3 rounded-[1.75rem] bg-white p-3 shadow-2xl shadow-black/20 md:grid-cols-[1fr_1fr_1.4fr_auto]" aria-label="{{ $labels['formLabel'] }}">
    <label class="sr-only" for="city">{{ $labels['cityLabel'] }}</label>
    <select id="city" name="city" class="h-12 rounded-full border-[#E5E7EB] bg-[#F8F8F8] px-4 text-sm text-[#222222] focus:border-[#E91E63] focus:ring-[#E91E63]">
        <option value="">{{ $labels['cityPlaceholder'] }}</option>
        @foreach ($cities as $city)
            <option value="{{ Str::slug($city) }}">{{ $city }}</option>
        @endforeach
    </select>

    <label class="sr-only" for="category">{{ $labels['categoryLabel'] }}</label>
    <select id="category" name="category" class="h-12 rounded-full border-[#E5E7EB] bg-[#F8F8F8] px-4 text-sm text-[#222222] focus:border-[#E91E63] focus:ring-[#E91E63]">
        <option value="">{{ $labels['categoryPlaceholder'] }}</option>
        @foreach ($categories as $category)
            <option value="{{ Str::slug($category) }}">{{ $category }}</option>
        @endforeach
    </select>

    <label class="sr-only" for="query">{{ $labels['queryLabel'] }}</label>
    <input id="query" name="query" type="search" placeholder="{{ $labels['queryPlaceholder'] }}" class="h-12 rounded-full border-[#E5E7EB] bg-[#F8F8F8] px-4 text-sm text-[#222222] placeholder:text-[#6B7280] focus:border-[#E91E63] focus:ring-[#E91E63]">

    <x-button type="submit" size="lg" class="h-12 px-8">{{ $labels['submitLabel'] }}</x-button>
</form>
