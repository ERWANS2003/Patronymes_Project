<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Livewire\ApplicationMonitoring;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Services\ApplicationMonitoringService;

class ApplicationMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function it_can_render_application_monitoring_component()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertStatus(200);
    }

    /** @test */
    public function it_can_load_application_metrics_on_mount()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('metrics', []);
        $component->assertSet('loading', false);
        $component->assertSet('autoRefresh', false);
    }

    /** @test */
    public function it_can_refresh_application_metrics()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->call('refreshApplicationMetrics');

        $component->assertSet('loading', false);
    }

    /** @test */
    public function it_can_toggle_auto_refresh()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('autoRefresh', false);

        $component->call('toggleAutoRefresh');

        $component->assertSet('autoRefresh', true);

        $component->call('toggleAutoRefresh');

        $component->assertSet('autoRefresh', false);
    }

    /** @test */
    public function it_can_export_metrics()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->call('exportMetrics');

        $component->assertStatus(200);
    }

    /** @test */
    public function it_can_clear_alerts()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->call('clearAlerts');

        $component->assertStatus(200);
    }

    /** @test */
    public function it_can_get_application_status()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $status = $component->call('getApplicationStatus');

        $this->assertContains($status, ['unknown', 'error', 'healthy', 'warning']);
    }

    /** @test */
    public function it_can_get_status_color()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $color = $component->call('getStatusColor');

        $this->assertContains($color, ['success', 'warning', 'danger', 'secondary']);
    }

    /** @test */
    public function it_can_get_status_icon()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $icon = $component->call('getStatusIcon');

        $this->assertStringContains('fas fa-', $icon);
    }

    /** @test */
    public function it_can_get_status_text()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $text = $component->call('getStatusText');

        $this->assertIsString($text);
        $this->assertNotEmpty($text);
    }

    /** @test */
    public function it_can_get_status_details()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $details = $component->call('getStatusDetails');

        $this->assertIsString($details);
    }

    /** @test */
    public function it_can_handle_metrics_loading_state()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('loading', false);

        $component->call('refreshApplicationMetrics');

        $component->assertSet('loading', false);
    }

    /** @test */
    public function it_can_handle_auto_refresh_state()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('autoRefresh', false);

        $component->call('toggleAutoRefresh');

        $component->assertSet('autoRefresh', true);
    }

    /** @test */
    public function it_can_handle_metrics_data_structure()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('metrics', []);

        $component->call('refreshApplicationMetrics');

        $component->assertSet('metrics', []);
    }

    /** @test */
    public function it_can_handle_export_metrics_response()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $response = $component->call('exportMetrics');

        $this->assertNotNull($response);
    }

    /** @test */
    public function it_can_handle_clear_alerts_response()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->call('clearAlerts');

        $component->assertStatus(200);
    }

    /** @test */
    public function it_can_handle_status_methods_with_different_metrics()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        // Test with empty metrics
        $component->set('metrics', []);
        $status = $component->call('getApplicationStatus');
        $this->assertEquals('unknown', $status);

        // Test with error metrics
        $component->set('metrics', ['performance' => ['error' => 'Test error']]);
        $status = $component->call('getApplicationStatus');
        $this->assertEquals('error', $status);

        // Test with successful metrics
        $component->set('metrics', ['performance' => ['test_successful' => true]]);
        $status = $component->call('getApplicationStatus');
        $this->assertEquals('healthy', $status);

        // Test with warning metrics
        $component->set('metrics', ['performance' => ['test_successful' => false]]);
        $status = $component->call('getApplicationStatus');
        $this->assertEquals('warning', $status);
    }

    /** @test */
    public function it_can_handle_status_color_with_different_statuses()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        // Test with healthy status
        $component->set('metrics', ['performance' => ['test_successful' => true]]);
        $color = $component->call('getStatusColor');
        $this->assertEquals('success', $color);

        // Test with warning status
        $component->set('metrics', ['performance' => ['test_successful' => false]]);
        $color = $component->call('getStatusColor');
        $this->assertEquals('warning', $color);

        // Test with error status
        $component->set('metrics', ['performance' => ['error' => 'Test error']]);
        $color = $component->call('getStatusColor');
        $this->assertEquals('danger', $color);

        // Test with unknown status
        $component->set('metrics', []);
        $color = $component->call('getStatusColor');
        $this->assertEquals('secondary', $color);
    }

    /** @test */
    public function it_can_handle_status_icon_with_different_statuses()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        // Test with healthy status
        $component->set('metrics', ['performance' => ['test_successful' => true]]);
        $icon = $component->call('getStatusIcon');
        $this->assertEquals('fas fa-check-circle', $icon);

        // Test with warning status
        $component->set('metrics', ['performance' => ['test_successful' => false]]);
        $icon = $component->call('getStatusIcon');
        $this->assertEquals('fas fa-exclamation-triangle', $icon);

        // Test with error status
        $component->set('metrics', ['performance' => ['error' => 'Test error']]);
        $icon = $component->call('getStatusIcon');
        $this->assertEquals('fas fa-times-circle', $icon);

        // Test with unknown status
        $component->set('metrics', []);
        $icon = $component->call('getStatusIcon');
        $this->assertEquals('fas fa-question-circle', $icon);
    }

    /** @test */
    public function it_can_handle_status_text_with_different_statuses()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        // Test with healthy status
        $component->set('metrics', ['performance' => ['test_successful' => true]]);
        $text = $component->call('getStatusText');
        $this->assertEquals('Application is running', $text);

        // Test with warning status
        $component->set('metrics', ['performance' => ['test_successful' => false]]);
        $text = $component->call('getStatusText');
        $this->assertEquals('Application issues detected', $text);

        // Test with error status
        $component->set('metrics', ['performance' => ['error' => 'Test error']]);
        $text = $component->call('getStatusText');
        $this->assertEquals('Application error', $text);

        // Test with unknown status
        $component->set('metrics', []);
        $text = $component->call('getStatusText');
        $this->assertEquals('Status unknown', $text);
    }

    /** @test */
    public function it_can_handle_status_details_with_different_statuses()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        // Test with healthy status
        $component->set('metrics', ['performance' => ['test_successful' => true]]);
        $details = $component->call('getStatusDetails');
        $this->assertEquals('All systems operational', $details);

        // Test with warning status
        $component->set('metrics', ['performance' => ['test_successful' => false]]);
        $details = $component->call('getStatusDetails');
        $this->assertEquals('Some systems may not be working properly', $details);

        // Test with error status
        $component->set('metrics', ['performance' => ['error' => 'Test error']]);
        $details = $component->call('getStatusDetails');
        $this->assertEquals('Test error', $details);

        // Test with unknown status
        $component->set('metrics', []);
        $details = $component->call('getStatusDetails');
        $this->assertEquals('Unable to determine status', $details);
    }

    /** @test */
    public function it_can_handle_component_lifecycle()
    {
        $this->actingAs($this->adminUser);

        $component = Livewire::test(ApplicationMonitoring::class);

        $component->assertSet('metrics', []);
        $component->assertSet('loading', false);
        $component->assertSet('autoRefresh', false);
        $component->assertSet('refreshInterval', 30);

        $component->call('refreshApplicationMetrics');

        $component->assertSet('loading', false);

        $component->call('toggleAutoRefresh');

        $component->assertSet('autoRefresh', true);

        $component->call('toggleAutoRefresh');

        $component->assertSet('autoRefresh', false);
    }
}
