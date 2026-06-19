@props(['category'])

<a href="{{ $category['href'] }}" class="classic-category-card">
    <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" loading="lazy">
    <span>
        <strong>{{ $category['name'] }}</strong>
        <small>{{ $category['count'] }}</small>
    </span>
</a>
