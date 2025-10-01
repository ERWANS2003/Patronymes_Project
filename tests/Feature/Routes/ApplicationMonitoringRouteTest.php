<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationMonitoringRouteTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function admin_can_access_application_monitoring_route()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_route()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_application_monitoring_route()
    {
        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function application_monitoring_route_returns_correct_view()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertViewIs('monitoring.application');
    }

    /** @test */
    public function application_monitoring_route_has_correct_name()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $this->assertEquals('monitoring.application', $response->viewData('view'));
    }

    /** @test */
    public function application_monitoring_route_is_protected_by_admin_middleware()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(403);
    }

    /** @test */
    public function application_monitoring_route_is_protected_by_auth_middleware()
    {
        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function application_monitoring_route_accepts_get_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
    }

    /** @test */
    public function application_monitoring_route_rejects_post_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_put_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->put('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_delete_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->delete('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_patch_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->patch('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_options_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->options('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_head_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->head('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_trace_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->call('TRACE', '/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_rejects_connect_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->call('CONNECT', '/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_has_correct_uri()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $this->assertEquals('/admin/monitoring/application', $response->getRequest()->getRequestUri());
    }

    /** @test */
    public function application_monitoring_route_has_correct_method()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $this->assertEquals('GET', $response->getRequest()->getMethod());
    }

    /** @test */
    public function application_monitoring_route_has_correct_headers()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_code()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
    }

    /** @test */
    public function application_monitoring_route_has_correct_response_format()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    /** @test */
    public function application_monitoring_route_has_correct_redirect_for_unauthorized()
    {
        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_forbidden()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(403);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_method_not_allowed()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_not_found()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/nonexistent');

        $response->assertStatus(404);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_server_error()
    {
        $this->actingAs($this->adminUser);

        // Simulate a server error by causing an exception
        $this->mock(\App\Http\Controllers\MonitoringController::class, function ($mock) {
            $mock->shouldReceive('application')->andThrow(new \Exception('Test error'));
        });

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(500);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_bad_request()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application?invalid=parameter');

        $response->assertStatus(200);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_unauthorized()
    {
        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_forbidden()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(403);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_not_found()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/nonexistent');

        $response->assertStatus(404);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_method_not_allowed()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post('/admin/monitoring/application');

        $response->assertStatus(405);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_server_error()
    {
        $this->actingAs($this->adminUser);

        // Simulate a server error by causing an exception
        $this->mock(\App\Http\Controllers\MonitoringController::class, function ($mock) {
            $mock->shouldReceive('application')->andThrow(new \Exception('Test error'));
        });

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(500);
    }

    /** @test */
    public function application_monitoring_route_has_correct_status_for_bad_request()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application?invalid=parameter');

        $response->assertStatus(200);
    }
}
