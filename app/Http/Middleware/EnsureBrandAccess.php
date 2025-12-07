<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBrandAccess
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has access to the brand.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $brand = $request->route('brand');

        // If brand is not already resolved (string slug or ID), resolve it
        if (!$brand instanceof Brand) {
            // Try to find by slug first (since we use getRouteKeyName = 'slug')
            // Laravel's route model binding should handle this, but just in case
            $brand = Brand::where('slug', $brand)->first()
                  ?? Brand::find($brand);
        }

        if (!$brand) {
            abort(404, __('messages.brand.not_found'));
        }

        $user = $request->user();

        // Check if user is owner or a member of the brand
        $hasAccess = $brand->created_by === $user->id ||
                     $brand->members()->where('user_id', $user->id)->exists();

        if (!$hasAccess) {
            abort(403, __('messages.brand.no_access'));
        }

        // Share brand with all views for easy access
        view()->share('currentBrand', $brand);

        return $next($request);
    }
}
