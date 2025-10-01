<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Services\ApplicationMonitoringService;

class GenerateApplicationReportCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function it_can_generate_application_report_command()
    {
        $this->artisan('monitoring:application-report')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_generate_application_report_with_custom_hours()
    {
        $this->artisan('monitoring:application-report', ['--hours' => 48])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_generate_application_report_with_output_file()
    {
        $outputPath = 'reports/application-test.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        Storage::assertExists($outputPath);
    }

    /** @test */
    public function it_can_generate_application_report_with_default_hours()
    {
        $this->artisan('monitoring:application-report')
            ->expectsOutput('Generating application monitoring report for the last 24 hours...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_generate_application_report_with_custom_hours_and_output()
    {
        $outputPath = 'reports/application-custom.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 72,
            '--output' => $outputPath
        ])
            ->expectsOutput('Generating application monitoring report for the last 72 hours...')
            ->expectsOutput("Report saved to: {$outputPath}")
            ->assertExitCode(0);

        Storage::assertExists($outputPath);
    }

    /** @test */
    public function it_can_generate_application_report_without_output_file()
    {
        $this->artisan('monitoring:application-report', ['--hours' => 12])
            ->expectsOutput('Generating application monitoring report for the last 12 hours...')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_generate_application_report_with_valid_json_output()
    {
        $outputPath = 'reports/application-valid.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertArrayHasKey('period_hours', $data);
        $this->assertArrayHasKey('generated_at', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('performance', $data);
        $this->assertArrayHasKey('trends', $data);
        $this->assertArrayHasKey('alerts', $data);
        $this->assertArrayHasKey('recommendations', $data);
    }

    /** @test */
    public function it_can_generate_application_report_with_correct_period()
    {
        $outputPath = 'reports/application-period.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 48,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertEquals(48, $data['period_hours']);
    }

    /** @test */
    public function it_can_generate_application_report_with_timestamp()
    {
        $outputPath = 'reports/application-timestamp.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('generated_at', $data);
        $this->assertNotEmpty($data['generated_at']);
        $this->assertIsString($data['generated_at']);
    }

    /** @test */
    public function it_can_generate_application_report_with_summary_data()
    {
        $outputPath = 'reports/application-summary.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('summary', $data);
        $this->assertIsArray($data['summary']);
        $this->assertArrayHasKey('app_name', $data['summary']);
        $this->assertArrayHasKey('app_version', $data['summary']);
        $this->assertArrayHasKey('app_environment', $data['summary']);
        $this->assertArrayHasKey('app_debug', $data['summary']);
    }

    /** @test */
    public function it_can_generate_application_report_with_performance_data()
    {
        $outputPath = 'reports/application-performance.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('performance', $data);
        $this->assertIsArray($data['performance']);
        $this->assertArrayHasKey('database_time_ms', $data['performance']);
        $this->assertArrayHasKey('cache_time_ms', $data['performance']);
        $this->assertArrayHasKey('session_time_ms', $data['performance']);
        $this->assertArrayHasKey('total_time_ms', $data['performance']);
        $this->assertArrayHasKey('test_successful', $data['performance']);
    }

    /** @test */
    public function it_can_generate_application_report_with_trends_data()
    {
        $outputPath = 'reports/application-trends.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('trends', $data);
        $this->assertIsArray($data['trends']);
        $this->assertArrayHasKey('trend', $data['trends']);
        $this->assertArrayHasKey('hourly', $data['trends']);
        $this->assertArrayHasKey('daily', $data['trends']);
    }

    /** @test */
    public function it_can_generate_application_report_with_alerts_data()
    {
        $outputPath = 'reports/application-alerts.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('alerts', $data);
        $this->assertIsArray($data['alerts']);
    }

    /** @test */
    public function it_can_generate_application_report_with_recommendations_data()
    {
        $outputPath = 'reports/application-recommendations.json';

        $this->artisan('monitoring:application-report', [
            '--hours' => 24,
            '--output' => $outputPath
        ])->assertExitCode(0);

        $content = Storage::get($outputPath);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('recommendations', $data);
        $this->assertIsArray($data['recommendations']);
    }

    /** @test */
    public function it_can_generate_application_report_with_multiple_hours()
    {
        $hours = [1, 6, 12, 24, 48, 72];

        foreach ($hours as $hour) {
            $outputPath = "reports/application-{$hour}h.json";

            $this->artisan('monitoring:application-report', [
                '--hours' => $hour,
                '--output' => $outputPath
            ])->assertExitCode(0);

            Storage::assertExists($outputPath);

            $content = Storage::get($outputPath);
            $data = json_decode($content, true);

            $this->assertEquals($hour, $data['period_hours']);
        }
    }

    /** @test */
    public function it_can_generate_application_report_with_different_output_paths()
    {
        $outputPaths = [
            'reports/application-1.json',
            'reports/application-2.json',
            'reports/application-3.json'
        ];

        foreach ($outputPaths as $outputPath) {
            $this->artisan('monitoring:application-report', [
                '--hours' => 24,
                '--output' => $outputPath
            ])->assertExitCode(0);

            Storage::assertExists($outputPath);
        }
    }

    /** @test */
    public function it_can_generate_application_report_without_errors()
    {
        $this->artisan('monitoring:application-report', ['--hours' => 24])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_generate_application_report_with_service_integration()
    {
        $this->mock(ApplicationMonitoringService::class, function ($mock) {
            $mock->shouldReceive('generateApplicationReport')
                ->once()
                ->with(24)
                ->andReturn([
                    'period_hours' => 24,
                    'generated_at' => now()->toISOString(),
                    'summary' => [],
                    'performance' => [],
                    'trends' => [],
                    'alerts' => [],
                    'recommendations' => []
                ]);
        });

        $this->artisan('monitoring:application-report', ['--hours' => 24])
            ->assertExitCode(0);
    }
}
