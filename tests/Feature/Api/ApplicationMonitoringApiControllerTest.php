<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class ApplicationMonitoringApiControllerTest extends TestCase
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
    public function admin_can_access_application_monitoring_index()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'summary',
                    'performance',
                    'trends',
                    'alerts',
                    'recommendations'
                ]
            ]);
    }

    /** @test */
    public function admin_can_access_application_monitoring_statistics()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'summary',
                    'performance',
                    'trends',
                    'alerts',
                    'recommendations'
                ]
            ]);
    }

    /** @test */
    public function admin_can_access_application_monitoring_performance()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'database_time_ms',
                    'cache_time_ms',
                    'session_time_ms',
                    'total_time_ms',
                    'test_successful'
                ]
            ]);
    }

    /** @test */
    public function admin_can_access_application_monitoring_trends()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/trends');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'trend',
                    'hourly',
                    'daily'
                ]
            ]);
    }

    /** @test */
    public function admin_can_access_application_monitoring_alerts()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/alerts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => []
            ]);
    }

    /** @test */
    public function admin_can_access_application_monitoring_report()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/report');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'period_hours',
                    'generated_at',
                    'summary',
                    'performance',
                    'trends',
                    'alerts',
                    'recommendations'
                ]
            ]);
    }

    /** @test */
    public function admin_can_cleanup_application_monitoring_data()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/application-monitoring/cleanup');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }

    /** @test */
    public function admin_can_export_application_monitoring_metrics()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/export');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'summary',
                    'performance',
                    'trends',
                    'alerts',
                    'recommendations'
                ]
            ]);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_index()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_statistics()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/statistics');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_performance()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/performance');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_trends()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/trends');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_alerts()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/alerts');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_application_monitoring_report()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/report');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_cleanup_application_monitoring_data()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/v1/application-monitoring/cleanup');

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_export_application_monitoring_metrics()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/export');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_application_monitoring()
    {
        $response = $this->getJson('/api/v1/application-monitoring');

        $response->assertStatus(401);
    }

    /** @test */
    public function application_monitoring_statistics_returns_correct_data_structure()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/statistics');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertArrayHasKey('app_name', $data['summary']);
        $this->assertArrayHasKey('app_version', $data['summary']);
        $this->assertArrayHasKey('app_environment', $data['summary']);
        $this->assertArrayHasKey('app_debug', $data['summary']);

        $this->assertArrayHasKey('database_time_ms', $data['performance']);
        $this->assertArrayHasKey('cache_time_ms', $data['performance']);
        $this->assertArrayHasKey('session_time_ms', $data['performance']);
        $this->assertArrayHasKey('total_time_ms', $data['performance']);
        $this->assertArrayHasKey('test_successful', $data['performance']);

        $this->assertArrayHasKey('trend', $data['trends']);
        $this->assertArrayHasKey('hourly', $data['trends']);
        $this->assertArrayHasKey('daily', $data['trends']);
    }

    /** @test */
    public function application_monitoring_performance_returns_numeric_values()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/performance');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertIsNumeric($data['database_time_ms']);
        $this->assertIsNumeric($data['cache_time_ms']);
        $this->assertIsNumeric($data['session_time_ms']);
        $this->assertIsNumeric($data['total_time_ms']);
        $this->assertIsBool($data['test_successful']);
    }

    /** @test */
    public function application_monitoring_trends_returns_correct_structure()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/trends');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertIsString($data['trend']);
        $this->assertIsArray($data['hourly']);
        $this->assertIsArray($data['daily']);
    }

    /** @test */
    public function application_monitoring_alerts_returns_array()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/alerts');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertIsArray($data);
    }

    /** @test */
    public function application_monitoring_report_returns_correct_period()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/report');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertArrayHasKey('period_hours', $data);
        $this->assertArrayHasKey('generated_at', $data);
        $this->assertIsString($data['generated_at']);
    }

    /** @test */
    public function application_monitoring_cleanup_returns_success_message()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/application-monitoring/cleanup');

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('message', $data);
    }

    /** @test */
    public function application_monitoring_export_returns_all_sections()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/application-monitoring/export');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('performance', $data);
        $this->assertArrayHasKey('trends', $data);
        $this->assertArrayHasKey('alerts', $data);
        $this->assertArrayHasKey('recommendations', $data);
    }
}
