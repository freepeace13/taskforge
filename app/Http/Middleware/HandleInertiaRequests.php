<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'layouts.workspace';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->id,
                        'authId' => $user->auth_id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                    : null,
                'tenant' => fn (): ?array => app()->bound(TenantContext::class)
                    ? [
                        'id' => app(TenantContext::class)->organization->id,
                        'slug' => app(TenantContext::class)->organization->slug,
                        'name' => app(TenantContext::class)->organization->name,
                        'role' => app(TenantContext::class)->role->value,
                    ]
                    : null,
                'organizations' => fn (): array => $user
                    ? $user->organizations()
                        ->orderBy('name')
                        ->get()
                        ->map(fn (Organization $org) => [
                            'id' => $org->id,
                            'name' => $org->name,
                            'slug' => $org->slug,
                            'role' => $org->pivot->role->value,
                        ])
                        ->values()
                        ->all()
                    : [],
            ],
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
