<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_admin_sees_the_dashboard_with_navigation(): void
    {
        $this->artisan('permissions:sync');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('products.list'), false)
            ->assertSee($admin->name);
    }

    public function test_users_without_permission_are_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertForbidden()
            ->assertSee(__('Access denied'));
    }

    public function test_unknown_urls_return_the_custom_404_page(): void
    {
        $this->artisan('permissions:sync');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee(__('Page not found'));
    }

    public function test_every_page_without_parameters_renders_for_an_admin(): void
    {
        $this->artisan('permissions:sync');
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        // Give list pages at least one row so per-row links are rendered too.
        Customer::factory()->create();
        Supplier::factory()->create();

        $routes = collect(Route::getRoutes()->getRoutesByName())
            ->filter(fn ($route, $name) => in_array('GET', $route->methods())
                && in_array('access.control', $route->gatherMiddleware())
                && $route->parameterNames() === []);

        $this->assertNotEmpty($routes);

        foreach ($routes as $name => $route) {
            $this->get(route($name))->assertOk();
        }
    }
}
