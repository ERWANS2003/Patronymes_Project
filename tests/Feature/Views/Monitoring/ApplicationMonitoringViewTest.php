<?php

namespace Tests\Feature\Views\Monitoring;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationMonitoringViewTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function admin_can_view_application_monitoring_page()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertViewIs('monitoring.application');
    }

    /** @test */
    public function application_monitoring_page_contains_required_elements()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Monitoring');
        $response->assertSee('Refresh');
        $response->assertSee('Back to Dashboard');
        $response->assertSee('Application Statistics');
        $response->assertSee('Application Performance');
        $response->assertSee('Application Status');
        $response->assertSee('Application Trends');
        $response->assertSee('Application Alerts');
        $response->assertSee('Recommendations');
    }

    /** @test */
    public function application_monitoring_page_contains_statistics_cards()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Name');
        $response->assertSee('Version');
        $response->assertSee('Environment');
        $response->assertSee('Debug Mode');
    }

    /** @test */
    public function application_monitoring_page_contains_performance_metrics()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Database Time (ms)');
        $response->assertSee('Cache Time (ms)');
        $response->assertSee('Session Time (ms)');
        $response->assertSee('Total Time (ms)');
    }

    /** @test */
    public function application_monitoring_page_contains_status_section()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Status');
        $response->assertSee('Application is running');
        $response->assertSee('All systems operational');
    }

    /** @test */
    public function application_monitoring_page_contains_trends_section()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Trends');
        $response->assertSee('applicationTrendsChart');
    }

    /** @test */
    public function application_monitoring_page_contains_alerts_section()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Alerts');
        $response->assertSee('No alerts at this time');
    }

    /** @test */
    public function application_monitoring_page_contains_recommendations_section()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Recommendations');
        $response->assertSee('No recommendations at this time');
    }

    /** @test */
    public function application_monitoring_page_contains_javascript_functionality()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('refreshApplicationMetrics');
        $response->assertSee('updateApplicationMetrics');
        $response->assertSee('updateApplicationAlerts');
        $response->assertSee('updateApplicationRecommendations');
        $response->assertSee('updateApplicationTrendsChart');
    }

    /** @test */
    public function application_monitoring_page_contains_chart_js_integration()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Chart.js');
        $response->assertSee('applicationTrendsChart');
        $response->assertSee('new Chart');
    }

    /** @test */
    public function application_monitoring_page_contains_api_endpoints()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('/api/v1/application-monitoring/statistics');
    }

    /** @test */
    public function application_monitoring_page_contains_auto_refresh_functionality()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('setInterval');
        $response->assertSee('30000');
    }

    /** @test */
    public function application_monitoring_page_contains_error_handling()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('catch');
        $response->assertSee('console.error');
    }

    /** @test */
    public function application_monitoring_page_contains_loading_states()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('loading');
        $response->assertSee('fa-spin');
    }

    /** @test */
    public function application_monitoring_page_contains_bootstrap_classes()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('container-fluid');
        $response->assertSee('row');
        $response->assertSee('col-md-3');
        $response->assertSee('col-md-6');
        $response->assertSee('col-12');
        $response->assertSee('card');
        $response->assertSee('card-body');
        $response->assertSee('btn');
        $response->assertSee('btn-primary');
        $response->assertSee('btn-secondary');
    }

    /** @test */
    public function application_monitoring_page_contains_font_awesome_icons()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('fas fa-sync-alt');
        $response->assertSee('fas fa-arrow-left');
        $response->assertSee('fas fa-cube');
        $response->assertSee('fas fa-tag');
        $response->assertSee('fas fa-server');
        $response->assertSee('fas fa-bug');
        $response->assertSee('fas fa-check-circle');
        $response->assertSee('fas fa-exclamation-triangle');
        $response->assertSee('fas fa-times-circle');
        $response->assertSee('fas fa-info-circle');
        $response->assertSee('fas fa-lightbulb');
    }

    /** @test */
    public function application_monitoring_page_contains_responsive_design()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('col-md-3');
        $response->assertSee('col-md-6');
        $response->assertSee('col-12');
        $response->assertSee('d-flex');
        $response->assertSee('justify-content-between');
        $response->assertSee('align-items-center');
    }

    /** @test */
    public function application_monitoring_page_contains_accessible_elements()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('role="alert"');
        $response->assertSee('aria-label');
        $response->assertSee('visually-hidden');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_meta_tags()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Application Monitoring');
    }

    /** @test */
    public function application_monitoring_page_contains_navigation_links()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('Back to Dashboard');
        $response->assertSee('monitoring.dashboard');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_styling()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('text-white');
        $response->assertSee('text-primary');
        $response->assertSee('text-info');
        $response->assertSee('text-success');
        $response->assertSee('text-warning');
        $response->assertSee('text-danger');
        $response->assertSee('text-muted');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_script_tags()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('<script>');
        $response->assertSee('</script>');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_form_elements()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('button');
        $response->assertSee('onclick');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_data_attributes()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('id=');
        $response->assertSee('class=');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_table_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('table');
        $response->assertSee('thead');
        $response->assertSee('tbody');
        $response->assertSee('tr');
        $response->assertSee('th');
        $response->assertSee('td');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_list_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('ul');
        $response->assertSee('li');
        $response->assertSee('list-group');
        $response->assertSee('list-group-item');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_alert_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('alert');
        $response->assertSee('alert-dismissible');
        $response->assertSee('btn-close');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_card_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('card-header');
        $response->assertSee('card-title');
        $response->assertSee('card-text');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_button_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('btn-sm');
        $response->assertSee('btn-primary');
        $response->assertSee('btn-secondary');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_icon_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('fa-2x');
        $response->assertSee('fa-3x');
        $response->assertSee('align-self-center');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_text_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('text-center');
        $response->assertSee('mb-0');
        $response->assertSee('mb-2');
        $response->assertSee('mb-3');
        $response->assertSee('mb-4');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_spacing_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('mt-3');
        $response->assertSee('me-2');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_flexbox_structure()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('d-flex');
        $response->assertSee('justify-content-between');
        $response->assertSee('align-items-center');
    }

    /** @test */
    public function application_monitoring_page_contains_proper_utility_classes()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/monitoring/application');

        $response->assertStatus(200);
        $response->assertSee('w-100');
        $response->assertSee('h-100');
        $response->assertSee('position-fixed');
        $response->assertSee('top-0');
        $response->assertSee('start-0');
    }
}
