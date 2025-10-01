<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ApplicationMonitoringService;
use Livewire\WithPagination;

class ApplicationMonitoring extends Component
{
    use WithPagination;

    public $metrics = [];
    public $loading = false;
    public $autoRefresh = false;
    public $refreshInterval = 30; // seconds

    protected $listeners = ['refreshApplicationMetrics'];

    public function mount()
    {
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        $this->loading = true;

        try {
            $this->metrics = ApplicationMonitoringService::getApplicationStatistics();
        } catch (\Exception $e) {
            $this->addError('error', 'Failed to load application metrics: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function refreshApplicationMetrics()
    {
        $this->loadMetrics();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;

        if ($this->autoRefresh) {
            $this->dispatch('startAutoRefresh', ['interval' => $this->refreshInterval * 1000]);
        } else {
            $this->dispatch('stopAutoRefresh');
        }
    }

    public function exportMetrics()
    {
        try {
            $report = ApplicationMonitoringService::generateApplicationReport(24);

            return response()->json($report)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="application-metrics-' . date('Y-m-d-H-i-s') . '.json"');
        } catch (\Exception $e) {
            $this->addError('error', 'Failed to export metrics: ' . $e->getMessage());
        }
    }

    public function clearAlerts()
    {
        try {
            ApplicationMonitoringService::clearApplicationAlerts();
            $this->loadMetrics();
            session()->flash('success', 'Application alerts cleared successfully.');
        } catch (\Exception $e) {
            $this->addError('error', 'Failed to clear alerts: ' . $e->getMessage());
        }
    }

    public function getApplicationStatus()
    {
        if (empty($this->metrics['performance'])) {
            return 'unknown';
        }

        if (isset($this->metrics['performance']['error'])) {
            return 'error';
        }

        if (isset($this->metrics['performance']['test_successful']) && $this->metrics['performance']['test_successful']) {
            return 'healthy';
        }

        return 'warning';
    }

    public function getStatusColor()
    {
        $status = $this->getApplicationStatus();

        switch ($status) {
            case 'healthy':
                return 'success';
            case 'warning':
                return 'warning';
            case 'error':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function getStatusIcon()
    {
        $status = $this->getApplicationStatus();

        switch ($status) {
            case 'healthy':
                return 'fas fa-check-circle';
            case 'warning':
                return 'fas fa-exclamation-triangle';
            case 'error':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-question-circle';
        }
    }

    public function getStatusText()
    {
        $status = $this->getApplicationStatus();

        switch ($status) {
            case 'healthy':
                return 'Application is running';
            case 'warning':
                return 'Application issues detected';
            case 'error':
                return 'Application error';
            default:
                return 'Status unknown';
        }
    }

    public function getStatusDetails()
    {
        $status = $this->getApplicationStatus();

        switch ($status) {
            case 'healthy':
                return 'All systems operational';
            case 'warning':
                return 'Some systems may not be working properly';
            case 'error':
                return $this->metrics['performance']['error'] ?? 'Unknown error';
            default:
                return 'Unable to determine status';
        }
    }

    public function render()
    {
        return view('livewire.application-monitoring', [
            'status' => $this->getApplicationStatus(),
            'statusColor' => $this->getStatusColor(),
            'statusIcon' => $this->getStatusIcon(),
            'statusText' => $this->getStatusText(),
            'statusDetails' => $this->getStatusDetails(),
        ]);
    }
}
