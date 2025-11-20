# Laravel Blade Frontend - SaaS Project

## Project Overview
- **Type**: Laravel Frontend Application (Blade Templates)
- **Purpose**: Customer-facing SaaS application
- **Laravel Version**: 12.x
- **UI Framework**: Blade + Tailwind CSS
- **JavaScript**: Jquery

## Architecture
- **Frontend Only**: This project handles UI/UX for customers
- **API Communication**: Connects to separate FilamentPHP backend via API
- **Authentication**: Laravel Sanctum

## Project Structure
```
app/
├── Http/
│   ├── Controllers/      # Frontend controllers
│   ├── Requests/         # Form validations
│   └── Middleware/       # Custom middleware
├── Models/               # Eloquent models (if sharing DB)
├── Services/             # API service classes
└── View/
    └── Components/       # Blade components

resources/
├── views/
│   ├── layouts/         # Master layouts
│   ├── components/      # Reusable components
│   └── pages/           # Page templates
├── css/                 # Styles
└── js/                  # Frontend scripts

routes/
└── web.php             # Frontend routes
```

## Tech Stack Details
- **CSS Framework**: Tailwind CSS 3.x / Bootstrap 5.x
- **JavaScript**: Alpine.js / Vue.js
- **Icons**: Heroicons / FontAwesome
- **Forms**: Livewire / Standard forms
- **Asset Building**: Vite

## API Integration
- Backend API URL: {your-backend-url}
- Authentication: Token-based
- API Service classes in `app/Services/`
