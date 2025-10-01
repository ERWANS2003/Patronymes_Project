<template>
    <div class="application-monitoring">
        <div class="row">
            <div class="col-12">
                <div
                    class="d-flex justify-content-between align-items-center mb-4"
                >
                    <h3 class="mb-0">Application Monitoring</h3>
                    <div>
                        <button
                            class="btn btn-sm btn-primary"
                            @click="refreshMetrics"
                            :disabled="loading"
                        >
                            <i
                                class="fas fa-sync-alt"
                                :class="{ 'fa-spin': loading }"
                            ></i>
                            Refresh
                        </button>
                        <button
                            class="btn btn-sm btn-success"
                            @click="startAutoRefresh"
                            v-if="!autoRefresh"
                        >
                            <i class="fas fa-play"></i>
                            Auto Refresh
                        </button>
                        <button
                            class="btn btn-sm btn-warning"
                            @click="stopAutoRefresh"
                            v-if="autoRefresh"
                        >
                            <i class="fas fa-pause"></i>
                            Stop Auto Refresh
                        </button>
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
                                <h4 class="card-title">
                                    {{ metrics.summary?.app_name || "-" }}
                                </h4>
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
                                <h4 class="card-title">
                                    {{ metrics.summary?.app_version || "-" }}
                                </h4>
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
                                <h4 class="card-title">
                                    {{
                                        metrics.summary?.app_environment || "-"
                                    }}
                                </h4>
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
                                <h4 class="card-title">
                                    {{
                                        metrics.summary?.app_debug
                                            ? "Yes"
                                            : "No"
                                    }}
                                </h4>
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
                                    <h3 class="text-primary">
                                        {{
                                            metrics.performance
                                                ?.database_time_ms || "-"
                                        }}
                                    </h3>
                                    <p class="text-muted">Database Time (ms)</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-info">
                                        {{
                                            metrics.performance
                                                ?.cache_time_ms || "-"
                                        }}
                                    </h3>
                                    <p class="text-muted">Cache Time (ms)</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-success">
                                        {{
                                            metrics.performance
                                                ?.session_time_ms || "-"
                                        }}
                                    </h3>
                                    <p class="text-muted">Session Time (ms)</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h3 class="text-warning">
                                        {{
                                            metrics.performance
                                                ?.total_time_ms || "-"
                                        }}
                                    </h3>
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
                                <i
                                    class="fas fa-check-circle fa-3x text-success"
                                    v-if="metrics.performance?.test_successful"
                                ></i>
                                <i
                                    class="fas fa-exclamation-triangle fa-3x text-warning"
                                    v-else-if="
                                        metrics.performance &&
                                        !metrics.performance.error
                                    "
                                ></i>
                                <i
                                    class="fas fa-times-circle fa-3x text-danger"
                                    v-else
                                ></i>
                            </div>
                            <h4 v-if="metrics.performance?.test_successful">
                                Application is running
                            </h4>
                            <h4
                                v-else-if="
                                    metrics.performance &&
                                    !metrics.performance.error
                                "
                            >
                                Application issues detected
                            </h4>
                            <h4 v-else>Application error</h4>
                            <p
                                class="text-muted"
                                v-if="metrics.performance?.test_successful"
                            >
                                All systems operational
                            </p>
                            <p
                                class="text-muted"
                                v-else-if="
                                    metrics.performance &&
                                    !metrics.performance.error
                                "
                            >
                                Some systems may not be working properly
                            </p>
                            <p class="text-muted" v-else>
                                {{
                                    metrics.performance?.error ||
                                    "Unknown error"
                                }}
                            </p>
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
                        <canvas ref="trendsChart" height="100"></canvas>
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
                        <div v-if="metrics.alerts && metrics.alerts.length > 0">
                            <div
                                v-for="alert in metrics.alerts"
                                :key="alert.id"
                                class="alert"
                                :class="getAlertClass(alert.level)"
                                role="alert"
                            >
                                <strong
                                    >{{ alert.level.toUpperCase() }}:</strong
                                >
                                {{ alert.message }}
                                <button
                                    type="button"
                                    class="btn-close"
                                    @click="dismissAlert(alert.id)"
                                ></button>
                            </div>
                        </div>
                        <div v-else class="text-center text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No alerts at this time</p>
                        </div>
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
                        <div
                            v-if="
                                metrics.recommendations &&
                                metrics.recommendations.length > 0
                            "
                        >
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="recommendation in metrics.recommendations"
                                    :key="recommendation"
                                    class="list-group-item"
                                >
                                    <i
                                        class="fas fa-arrow-right text-primary me-2"
                                    ></i>
                                    {{ recommendation }}
                                </li>
                            </ul>
                        </div>
                        <div v-else class="text-center text-muted">
                            <i class="fas fa-lightbulb fa-2x mb-2"></i>
                            <p>No recommendations at this time</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Chart from "chart.js/auto";

export default {
    name: "ApplicationMonitoring",
    data() {
        return {
            metrics: {
                summary: {},
                performance: {},
                trends: {},
                alerts: [],
                recommendations: [],
            },
            loading: false,
            autoRefresh: false,
            refreshInterval: null,
            trendsChart: null,
        };
    },
    mounted() {
        this.refreshMetrics();
        this.startAutoRefresh();
    },
    beforeUnmount() {
        this.stopAutoRefresh();
        if (this.trendsChart) {
            this.trendsChart.destroy();
        }
    },
    methods: {
        async refreshMetrics() {
            this.loading = true;
            try {
                const response = await fetch(
                    "/api/v1/application-monitoring/statistics"
                );
                const data = await response.json();

                if (data.success) {
                    this.metrics = data.data;
                    this.updateTrendsChart();
                } else {
                    console.error(
                        "Error fetching application metrics:",
                        data.message
                    );
                }
            } catch (error) {
                console.error("Error fetching application metrics:", error);
            } finally {
                this.loading = false;
            }
        },

        startAutoRefresh() {
            this.autoRefresh = true;
            this.refreshInterval = setInterval(() => {
                this.refreshMetrics();
            }, 30000); // Refresh every 30 seconds
        },

        stopAutoRefresh() {
            this.autoRefresh = false;
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
        },

        getAlertClass(level) {
            switch (level) {
                case "critical":
                    return "alert-danger";
                case "warning":
                    return "alert-warning";
                default:
                    return "alert-info";
            }
        },

        dismissAlert(alertId) {
            this.metrics.alerts = this.metrics.alerts.filter(
                (alert) => alert.id !== alertId
            );
        },

        updateTrendsChart() {
            if (!this.metrics.trends || !this.metrics.trends.hourly) {
                return;
            }

            const ctx = this.$refs.trendsChart.getContext("2d");

            if (this.trendsChart) {
                this.trendsChart.destroy();
            }

            const labels = Object.keys(this.metrics.trends.hourly);
            const requestsData = labels.map(
                (hour) => this.metrics.trends.hourly[hour].requests || 0
            );
            const responseTimeData = labels.map(
                (hour) => this.metrics.trends.hourly[hour].response_time_ms || 0
            );
            const memoryUsageData = labels.map(
                (hour) => this.metrics.trends.hourly[hour].memory_usage_mb || 0
            );
            const cpuUsageData = labels.map(
                (hour) =>
                    this.metrics.trends.hourly[hour].cpu_usage_percentage || 0
            );

            this.trendsChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Requests",
                            data: requestsData,
                            borderColor: "rgb(75, 192, 192)",
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            tension: 0.1,
                        },
                        {
                            label: "Response Time (ms)",
                            data: responseTimeData,
                            borderColor: "rgb(255, 99, 132)",
                            backgroundColor: "rgba(255, 99, 132, 0.2)",
                            tension: 0.1,
                        },
                        {
                            label: "Memory Usage (MB)",
                            data: memoryUsageData,
                            borderColor: "rgb(54, 162, 235)",
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            tension: 0.1,
                        },
                        {
                            label: "CPU Usage (%)",
                            data: cpuUsageData,
                            borderColor: "rgb(255, 205, 86)",
                            backgroundColor: "rgba(255, 205, 86, 0.2)",
                            tension: 0.1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                    plugins: {
                        legend: {
                            position: "top",
                        },
                        title: {
                            display: true,
                            text: "Application Performance Trends",
                        },
                    },
                },
            });
        },
    },
};
</script>

<style scoped>
.application-monitoring {
    padding: 20px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-body {
    padding: 1.25rem;
}

.alert {
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
}

.btn-close:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
