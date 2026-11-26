<?php

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

/**
 * Named GET routes that sit behind the access.control middleware.
 *
 * @return array<string, RoutingRoute>
 */
function protectedRoutes(bool $withParameters = true): array
{
    return collect(Route::getRoutes()->getRoutesByName())
        ->filter(fn (RoutingRoute $route) => in_array('GET', $route->methods())
            && in_array('access.control', $route->gatherMiddleware())
            && ($withParameters || $route->parameterNames() === []))
        ->all();
}

function urlFor(RoutingRoute $route): string
{
    // Dummy parameters are enough: authentication runs before the page loads anything.
    return route($route->getName(), array_fill_keys($route->parameterNames(), 1));
}

beforeEach(function () {
    $this->withoutVite();
});

it('protects every application page', function () {
    $routes = protectedRoutes();

    expect($routes)->not->toBeEmpty()
        ->and(array_keys($routes))->toContain('dashboard', 'products.list', 'sales.create', 'stock-movements.index');

    foreach ($routes as $route) {
        expect(in_array('auth', $route->gatherMiddleware()))->toBeTrue("{$route->getName()} is missing the auth middleware");
    }
});

it('redirects guests to the login page', function () {
    foreach (protectedRoutes() as $name => $route) {
        $this->get(urlFor($route))->assertRedirect(route('login'), "Guest was not redirected from {$name}");
    }
});

it('forbids signed-in users without the matching permission', function () {
    $this->actingAs(User::factory()->create());

    foreach (protectedRoutes(withParameters: false) as $name => $route) {
        $this->get(urlFor($route))
            ->assertForbidden()
            ->assertSee(__('Access denied'));
    }
});

it('grants exactly the pages a user has permission for', function () {
    $this->artisan('permissions:sync');
    $user = User::factory()->create();
    $user->givePermissionTo('products.list');

    $this->actingAs($user);

    $this->get(route('products.list'))->assertOk();
    $this->get(route('sales.index'))->assertForbidden();
    $this->get(route('dashboard'))->assertForbidden();
});

it('renders every page for an admin', function () {
    actingAsAdmin();

    // Give list pages at least one row so per-row links are rendered too.
    Customer::factory()->create();
    Supplier::factory()->create();

    foreach (protectedRoutes(withParameters: false) as $name => $route) {
        $this->get(urlFor($route))->assertOk();
    }
});

it('shows the signed-in user and navigation on the dashboard', function () {
    $admin = actingAsAdmin();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee($admin->name)
        ->assertSee(route('products.list'), false)
        ->assertSee(route('stock-movements.index'), false);
});

it('returns the branded 404 page for unknown URLs', function () {
    actingAsAdmin();

    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertSee(__('Page not found'));
});
