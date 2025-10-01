<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ApplicationMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class ApplicationMonitoringServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $applicationMonitoringService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->applicationMonitoringService = new ApplicationMonitoringService();
    }

    /** @test */
    public function it_can_get_application_statistics()
    {
        $statistics = $this->applicationMonitoringService->getApplicationStatistics();

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('summary', $statistics);
        $this->assertArrayHasKey('performance', $statistics);
        $this->assertArrayHasKey('trends', $statistics);
        $this->assertArrayHasKey('alerts', $statistics);
        $this->assertArrayHasKey('recommendations', $statistics);
    }

    /** @test */
    public function it_can_get_application_summary()
    {
        $summary = $this->applicationMonitoringService->getApplicationSummary();

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('app_name', $summary);
        $this->assertArrayHasKey('app_version', $summary);
        $this->assertArrayHasKey('app_environment', $summary);
        $this->assertArrayHasKey('app_debug', $summary);
        $this->assertArrayHasKey('app_timezone', $summary);
        $this->assertArrayHasKey('app_locale', $summary);
        $this->assertArrayHasKey('app_url', $summary);
        $this->assertArrayHasKey('app_key', $summary);
        $this->assertArrayHasKey('app_cipher', $summary);
        $this->assertArrayHasKey('app_providers', $summary);
        $this->assertArrayHasKey('app_aliases', $summary);
        $this->assertArrayHasKey('app_middleware', $summary);
        $this->assertArrayHasKey('app_guards', $summary);
        $this->assertArrayHasKey('app_providers_loaded', $summary);
        $this->assertArrayHasKey('app_services_registered', $summary);
    }

    /** @test */
    public function it_can_get_application_performance()
    {
        $performance = $this->applicationMonitoringService->getApplicationPerformance();

        $this->assertIsArray($performance);
        $this->assertArrayHasKey('database_time_ms', $performance);
        $this->assertArrayHasKey('cache_time_ms', $performance);
        $this->assertArrayHasKey('session_time_ms', $performance);
        $this->assertArrayHasKey('total_time_ms', $performance);
        $this->assertArrayHasKey('test_successful', $performance);
    }

    /** @test */
    public function it_can_get_application_trends()
    {
        $trends = $this->applicationMonitoringService->getApplicationTrends();

        $this->assertIsArray($trends);
        $this->assertArrayHasKey('trend', $trends);
        $this->assertArrayHasKey('hourly', $trends);
        $this->assertArrayHasKey('daily', $trends);
    }

    /** @test */
    public function it_can_get_application_alerts()
    {
        $alerts = $this->applicationMonitoringService->getApplicationAlerts();

        $this->assertIsArray($alerts);
    }

    /** @test */
    public function it_can_get_application_recommendations()
    {
        $recommendations = $this->applicationMonitoringService->getApplicationRecommendations();

        $this->assertIsArray($recommendations);
    }

    /** @test */
    public function it_can_generate_application_report()
    {
        $report = $this->applicationMonitoringService->generateApplicationReport(24);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('period_hours', $report);
        $this->assertArrayHasKey('generated_at', $report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('performance', $report);
        $this->assertArrayHasKey('trends', $report);
        $this->assertArrayHasKey('alerts', $report);
        $this->assertArrayHasKey('recommendations', $report);
    }

    /** @test */
    public function it_can_clear_application_alerts()
    {
        $result = $this->applicationMonitoringService->clearApplicationAlerts();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_export_application_metrics()
    {
        $export = $this->applicationMonitoringService->exportApplicationMetrics();

        $this->assertIsArray($export);
        $this->assertArrayHasKey('summary', $export);
        $this->assertArrayHasKey('performance', $export);
        $this->assertArrayHasKey('trends', $export);
        $this->assertArrayHasKey('alerts', $export);
        $this->assertArrayHasKey('recommendations', $export);
    }

    /** @test */
    public function it_can_cleanup_application_data()
    {
        $result = $this->applicationMonitoringService->cleanupApplicationData();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_application_summary_with_config_values()
    {
        Config::set('app.name', 'Test App');
        Config::set('app.env', 'testing');
        Config::set('app.debug', true);

        $summary = $this->applicationMonitoringService->getApplicationSummary();

        $this->assertEquals('Test App', $summary['app_name']);
        $this->assertEquals('testing', $summary['app_environment']);
        $this->assertTrue($summary['app_debug']);
    }

    /** @test */
    public function it_can_get_application_performance_with_timing()
    {
        $performance = $this->applicationMonitoringService->getApplicationPerformance();

        $this->assertIsNumeric($performance['database_time_ms']);
        $this->assertIsNumeric($performance['cache_time_ms']);
        $this->assertIsNumeric($performance['session_time_ms']);
        $this->assertIsNumeric($performance['total_time_ms']);
        $this->assertIsBool($performance['test_successful']);
    }

    /** @test */
    public function it_can_get_application_trends_with_data()
    {
        $trends = $this->applicationMonitoringService->getApplicationTrends();

        $this->assertIsString($trends['trend']);
        $this->assertIsArray($trends['hourly']);
        $this->assertIsArray($trends['daily']);
    }

    /** @test */
    public function it_can_get_application_alerts_with_levels()
    {
        $alerts = $this->applicationMonitoringService->getApplicationAlerts();

        foreach ($alerts as $alert) {
            $this->assertArrayHasKey('level', $alert);
            $this->assertArrayHasKey('message', $alert);
            $this->assertArrayHasKey('timestamp', $alert);
            $this->assertContains($alert['level'], ['info', 'warning', 'critical']);
        }
    }

    /** @test */
    public function it_can_get_application_recommendations_with_suggestions()
    {
        $recommendations = $this->applicationMonitoringService->getApplicationRecommendations();

        foreach ($recommendations as $recommendation) {
            $this->assertIsString($recommendation);
            $this->assertNotEmpty($recommendation);
        }
    }

    /** @test */
    public function it_can_generate_application_report_with_correct_period()
    {
        $report = $this->applicationMonitoringService->generateApplicationReport(48);

        $this->assertEquals(48, $report['period_hours']);
        $this->assertIsString($report['generated_at']);
        $this->assertNotEmpty($report['generated_at']);
    }

    /** @test */
    public function it_can_clear_application_alerts_and_return_true()
    {
        $result = $this->applicationMonitoringService->clearApplicationAlerts();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_export_application_metrics_with_all_sections()
    {
        $export = $this->applicationMonitoringService->exportApplicationMetrics();

        $this->assertIsArray($export['summary']);
        $this->assertIsArray($export['performance']);
        $this->assertIsArray($export['trends']);
        $this->assertIsArray($export['alerts']);
        $this->assertIsArray($export['recommendations']);
    }

    /** @test */
    public function it_can_cleanup_application_data_and_return_true()
    {
        $result = $this->applicationMonitoringService->cleanupApplicationData();

        $this->assertTrue($result);
    }
}
