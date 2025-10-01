<template>
    <div class="real-time-monitoring">
        <div class="row">
            <!-- Métriques de Performance -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tachometer-alt"></i> Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{
                                            metrics.performance.memory
                                                .current_mb
                                        }}MB
                                    </div>
                                    <div class="metric-label">Mémoire</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{
                                            metrics.performance.cpu
                                                .usage_percentage
                                        }}%
                                    </div>
                                    <div class="metric-label">CPU</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{
                                            metrics.performance.cache_hit_rate
                                        }}%
                                    </div>
                                    <div class="metric-label">Cache Hit</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{
                                            metrics.performance.disk_io
                                                .free_space_gb
                                        }}GB
                                    </div>
                                    <div class="metric-label">Espace Libre</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métriques d'Activité -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users"></i> Activité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{ metrics.activity.online_users }}
                                    </div>
                                    <div class="metric-label">Utilisateurs</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{ metrics.activity.active_sessions }}
                                    </div>
                                    <div class="metric-label">Sessions</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{ metrics.activity.current_requests }}
                                    </div>
                                    <div class="metric-label">Requêtes</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-item">
                                    <div class="metric-value">
                                        {{
                                            metrics.activity
                                                .database_connections
                                        }}
                                    </div>
                                    <div class="metric-label">
                                        Connexions DB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- État de Santé -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heartbeat"></i> État de Santé
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div
                                        class="h2 mb-0"
                                        :class="healthStatusClass"
                                    >
                                        <i :class="healthStatusIcon"></i>
                                    </div>
                                    <p class="mb-0">
                                        Statut:
                                        <strong>{{ healthStatusText }}</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div
                                        v-for="(check, name) in health.checks"
                                        :key="name"
                                        class="col-md-3"
                                    >
                                        <div class="text-center">
                                            <div
                                                class="h4 mb-0"
                                                :class="
                                                    getCheckStatusClass(
                                                        check.status
                                                    )
                                                "
                                            >
                                                <i
                                                    :class="
                                                        getCheckStatusIcon(
                                                            check.status
                                                        )
                                                    "
                                                ></i>
                                            </div>
                                            <p class="mb-0">
                                                {{ formatCheckName(name) }}
                                            </p>
                                            <small
                                                v-if="check.response_time_ms"
                                                class="text-muted"
                                            >
                                                {{ check.response_time_ms }}ms
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques en Temps Réel -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Graphiques en
                            Temps Réel
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas
                            ref="performanceChart"
                            width="400"
                            height="200"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicateur de Mise à Jour -->
        <div class="update-indicator" :class="{ updating: isUpdating }">
            <i class="fas fa-sync-alt" :class="{ 'fa-spin': isUpdating }"></i>
            <span>{{
                isUpdating
                    ? "Mise à jour..."
                    : "Dernière mise à jour: " + lastUpdate
            }}</span>
        </div>
    </div>
</template>

<script>
import Chart from "chart.js/auto";

export default {
    name: "RealTimeMonitoring",
    data() {
        return {
            metrics: {
                performance: {
                    memory: { current_mb: 0 },
                    cpu: { usage_percentage: 0 },
                    cache_hit_rate: 0,
                    disk_io: { free_space_gb: 0 },
                },
                activity: {
                    online_users: 0,
                    active_sessions: 0,
                    current_requests: 0,
                    database_connections: 0,
                },
            },
            health: {
                status: "unknown",
                checks: {},
            },
            isUpdating: false,
            lastUpdate: "",
            performanceChart: null,
            chartData: {
                labels: [],
                datasets: [
                    {
                        label: "Mémoire (MB)",
                        data: [],
                        borderColor: "rgb(75, 192, 192)",
                        tension: 0.1,
                    },
                    {
                        label: "CPU (%)",
                        data: [],
                        borderColor: "rgb(255, 99, 132)",
                        tension: 0.1,
                    },
                ],
            },
        };
    },
    computed: {
        healthStatusClass() {
            return {
                "text-success": this.health.status === "healthy",
                "text-warning": this.health.status === "warning",
                "text-danger": this.health.status === "unhealthy",
            };
        },
        healthStatusIcon() {
            return {
                "fas fa-check-circle": this.health.status === "healthy",
                "fas fa-exclamation-triangle": this.health.status === "warning",
                "fas fa-times-circle": this.health.status === "unhealthy",
            };
        },
        healthStatusText() {
            return this.health.status === "healthy"
                ? "Sain"
                : this.health.status === "warning"
                ? "Attention"
                : "Problème";
        },
    },
    mounted() {
        this.initializeChart();
        this.startRealTimeUpdates();
    },
    beforeUnmount() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    },
    methods: {
        async fetchMetrics() {
            try {
                this.isUpdating = true;

                const [performanceResponse, activityResponse, healthResponse] =
                    await Promise.all([
                        fetch("/api/v1/metrics/performance"),
                        fetch("/api/v1/metrics/activity"),
                        fetch("/api/v1/metrics/health"),
                    ]);

                if (performanceResponse.ok) {
                    const performanceData = await performanceResponse.json();
                    this.metrics.performance = performanceData.data;
                }

                if (activityResponse.ok) {
                    const activityData = await activityResponse.json();
                    this.metrics.activity = activityData.data;
                }

                if (healthResponse.ok) {
                    this.health = await healthResponse.json();
                }

                this.updateChart();
                this.lastUpdate = new Date().toLocaleTimeString();
            } catch (error) {
                console.error(
                    "Erreur lors de la récupération des métriques:",
                    error
                );
            } finally {
                this.isUpdating = false;
            }
        },
        startRealTimeUpdates() {
            this.fetchMetrics(); // Première récupération
            this.updateInterval = setInterval(this.fetchMetrics, 5000); // Mise à jour toutes les 5 secondes
        },
        initializeChart() {
            const ctx = this.$refs.performanceChart.getContext("2d");
            this.performanceChart = new Chart(ctx, {
                type: "line",
                data: this.chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                    plugins: {
                        legend: {
                            position: "top",
                        },
                    },
                },
            });
        },
        updateChart() {
            const now = new Date().toLocaleTimeString();

            // Ajouter de nouvelles données
            this.chartData.labels.push(now);
            this.chartData.datasets[0].data.push(
                this.metrics.performance.memory.current_mb
            );
            this.chartData.datasets[1].data.push(
                this.metrics.performance.cpu.usage_percentage
            );

            // Limiter à 20 points de données
            if (this.chartData.labels.length > 20) {
                this.chartData.labels.shift();
                this.chartData.datasets[0].data.shift();
                this.chartData.datasets[1].data.shift();
            }

            this.performanceChart.update();
        },
        getCheckStatusClass(status) {
            return {
                "text-success": status === "ok",
                "text-warning": status === "warning",
                "text-danger": status === "error",
            };
        },
        getCheckStatusIcon(status) {
            return {
                "fas fa-check": status === "ok",
                "fas fa-exclamation-triangle": status === "warning",
                "fas fa-times": status === "error",
            };
        },
        formatCheckName(name) {
            return name.charAt(0).toUpperCase() + name.slice(1);
        },
    },
};
</script>

<style scoped>
.real-time-monitoring {
    position: relative;
}

.metric-item {
    text-align: center;
    padding: 10px;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.metric-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 5px;
}

.update-indicator {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 0.9rem;
    z-index: 1000;
}

.update-indicator.updating {
    background: rgba(0, 123, 255, 0.8);
}

.update-indicator i {
    margin-right: 5px;
}
</style>
