<div class="application-monitoring">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Application Monitoring</h3>
                <div>
                    <button class="btn btn-sm btn-primary" wire:click="refreshApplicationMetrics" wire:loading.attr="disabled">
                        <i class="fas fa-sync-alt" wire:loading.class="fa-spin"></i>
                        Refresh
                    </button>
                    <button class="btn btn-sm {{ $autoRefresh ? 'btn-warning' : 'btn-success' }}" wire:click="toggleAutoRefresh">
                        <i class="fas {{ $autoRefresh ? 'fa-pause' : 'fa-play' }}"></i>
                        {{ $autoRefresh ? 'Stop Auto Refresh' : 'Auto Refresh' }}
                    </button>
                    <button class="btn btn-sm btn-info" wire:click="exportMetrics">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    @if(!empty($metrics['alerts']))
                        <button class="btn btn-sm btn-danger" wire:click="clearAlerts">
                            <i class="fas fa-trash"></i>
                            Clear Alerts
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Application Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $metrics['summary']['app_name'] ?? '-' }}</h4>
                            <p class="card-text">Application Name</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cube fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $metrics['summary']['app_version'] ?? '-' }}</h4>
                            <p class="card-text">Version</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $metrics['summary']['app_environment'] ?? '-' }}</h4>
                            <p class="card-text">Environment</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-server fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ ($metrics['summary']['app_debug'] ?? false) ? 'Yes' : 'No' }}</h4>
                            <p class="card-text">Debug Mode</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bug fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Performance -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $metrics['performance']['database_time_ms'] ?? '-' }}</h3>
                                <p class="text-muted">Database Time (ms)</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-info">{{ $metrics['performance']['cache_time_ms'] ?? '-' }}</h3>
                                <p class="text-muted">Cache Time (ms)</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-success">{{ $metrics['performance']['session_time_ms'] ?? '-' }}</h3>
                                <p class="text-muted">Session Time (ms)</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $metrics['performance']['total_time_ms'] ?? '-' }}</h3>
                                <p class="text-muted">Total Time (ms)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Status</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="{{ $statusIcon }} fa-3x text-{{ $statusColor }}"></i>
                        </div>
                        <h4>{{ $statusText }}</h4>
                        <p class="text-muted">{{ $statusDetails }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Trends</h5>
                </div>
                <div class="card-body">
                    @if(!empty($metrics['trends']['hourly']))
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Hour</th>
                                        <th>Requests</th>
                                        <th>Response Time (ms)</th>
                                        <th>Memory Usage (MB)</th>
                                        <th>CPU Usage (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($metrics['trends']['hourly'] as $hour => $performance)
                                        <tr>
                                            <td>{{ $hour }}</td>
                                            <td>{{ $performance['requests'] ?? 0 }}</td>
                                            <td>{{ $performance['response_time_ms'] ?? 0 }}</td>
                                            <td>{{ $performance['memory_usage_mb'] ?? 0 }}</td>
                                            <td>{{ $performance['cpu_usage_percentage'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p>No trend data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Application Alerts -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Alerts</h5>
                </div>
                <div class="card-body">
                    @if(!empty($metrics['alerts']))
                        @foreach($metrics['alerts'] as $alert)
                            <div class="alert alert-{{ $alert['level'] === 'critical' ? 'danger' : ($alert['level'] === 'warning' ? 'warning' : 'info') }} alert-dismissible fade show" role="alert">
                                <strong>{{ strtoupper($alert['level']) }}:</strong> {{ $alert['message'] }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No alerts at this time</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Application Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recommendations</h5>
                </div>
                <div class="card-body">
                    @if(!empty($metrics['recommendations']))
                        <ul class="list-group list-group-flush">
                            @foreach($metrics['recommendations'] as $recommendation)
                                <li class="list-group-item">
                                    <i class="fas fa-arrow-right text-primary me-2"></i>
                                    {{ $recommendation }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-lightbulb fa-2x mb-2"></i>
                            <p>No recommendations at this time</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    @if($loading)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('startAutoRefresh', (event) => {
        const interval = event[0].interval || 30000;
        window.applicationMonitoringInterval = setInterval(() => {
            Livewire.dispatch('refreshApplicationMetrics');
        }, interval);
    });

    Livewire.on('stopAutoRefresh', () => {
        if (window.applicationMonitoringInterval) {
            clearInterval(window.applicationMonitoringInterval);
            window.applicationMonitoringInterval = null;
        }
    });
});
</script>
