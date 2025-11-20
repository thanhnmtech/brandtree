# Frontend Coding Standards

## Laravel Blade Best Practices

### Controllers
- Keep controllers thin, move logic to services
- Use dependency injection
- Type hint all parameters and return types
- Return views or JSON responses appropriately
- Use FormRequest for validation

Example:
```php
public function show(ProductService $service, int $id): View
{
    $product = $service->getProduct($id);
    return view('products.show', compact('product'));
}
```

### Blade Templates
- Use Blade components for reusable UI elements
- Prefer `@props` over `$attributes` when possible
- Keep logic minimal in views
- Use `@include` for partials
- Use `@section` and `@yield` for layouts
- Always escape output with `{{ }}` unless HTML is trusted

Example:
```blade
<x-card title="{{ $product->name }}">
    <x-slot:footer>
        <x-button wire:click="addToCart">Add to Cart</x-button>
    </x-slot:footer>
</x-card>
```

### Blade Components
- Create in `resources/views/components/` or `app/View/Components/`
- Use class-based components for complex logic
- Use anonymous components for simple UI
- Properly type hint component properties

### Routes
- Use resource routes when appropriate
- Group related routes
- Name all routes for easy URL generation
- Use route model binding
- Apply middleware appropriately

Example:
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
});
```

## API Service Pattern

Always create service classes for API calls:
```php
// app/Services/ApiService.php
class ApiService
{
    protected $client;

    public function __construct(protected string $baseUrl)
    {
        $this->client = Http::withToken(session('api_token'))
            ->baseUrl($this->baseUrl);
    }

    public function get(string $endpoint): array
    {
        return $this->client->get($endpoint)->json();
    }
}

// app/Services/ProductService.php
class ProductService
{
    public function __construct(protected ApiService $api) {}

    public function getAll(): Collection
    {
        $data = $this->api->get('/products');
        return collect($data['data']);
    }
}
```

## Frontend Performance

- Lazy load images with `loading="lazy"`
- Use CSS/JS minification via Vite
- Implement pagination for large datasets
- Use HTTP caching headers
- Optimize images before upload
- Use CDN for static assets

## JavaScript Guidelines

- Use Alpine.js for simple interactions
- Keep JavaScript modular
- Use event delegation when possible
- Avoid inline scripts
- Use `defer` or `async` for script loading

Example (Alpine.js):
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

## CSS/Tailwind Guidelines

- Use Tailwind utility classes
- Create custom components in CSS when needed
- Follow mobile-first approach
- Use CSS variables for theming
- Keep specificity low

## Form Handling

- Always use CSRF protection
- Client-side validation + server-side validation
- Display errors clearly
- Use FormRequest classes for validation
- Sanitize user input

Example:
```blade
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <x-input name="name" :value="old('name')" />
    @error('name')
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</form>
```

## Security

- Never trust user input
- Use `{{ }}` for output escaping
- Use `{!! !!}` only for trusted HTML
- Implement rate limiting
- Use HTTPS only
- Validate all API responses

## Error Handling

- Use try-catch for API calls
- Show user-friendly error messages
- Log errors appropriately
- Handle 404, 500 errors gracefully

## Naming Conventions

- Views: kebab-case (products/show-details.blade.php)
- Components: PascalCase (ProductCard.php)
- Routes: kebab-case (product-details)
- CSS classes: Follow Tailwind conventions
