<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Monitoring de l'Application
                </h1>
                <p class="text-gray-600 mt-1">
                    Surveillance en temps réel des performances et de la santé de l'application
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshApplicationMetrics()" class="btn btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
                <a href="{{ route('monitoring.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Application Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card bg-blue-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold" id="app-name">-</h4>
                            <p class="text-blue-100">Nom de l'application</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cube text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-green-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="app-version">-</h4>
                            <p class="text-green-100">Version</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tag text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-purple-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="app-uptime">-</h4>
                            <p class="text-purple-100">Temps de fonctionnement</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-orange-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="app-status">-</h4>
                            <p class="text-orange-100">Statut</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Response Time -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-stopwatch text-blue-600 mr-2"></i>
                        Temps de réponse
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Moyenne</span>
                            <span class="text-lg font-bold text-gray-900" id="avg-response-time">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Minimum</span>
                            <span class="text-lg font-bold text-gray-900" id="min-response-time">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Maximum</span>
                            <span class="text-lg font-bold text-gray-900" id="max-response-time">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-memory text-green-600 mr-2"></i>
                        Utilisation mémoire
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Utilisée</span>
                            <span class="text-lg font-bold text-gray-900" id="memory-used">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Disponible</span>
                            <span class="text-lg font-bold text-gray-900" id="memory-available">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pourcentage</span>
                            <span class="text-lg font-bold text-gray-900" id="memory-percentage">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Requests Chart -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Requêtes par minute
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="requestsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Error Rate Chart -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Taux d'erreur
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="errorRateChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bell text-yellow-600 mr-2"></i>
                    Alertes récentes
                </h3>
                <div id="alerts-container">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Chargement des alertes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let requestsChart, errorRateChart;

        // Initialize charts
        function initCharts() {
            // Requests Chart
            const requestsCtx = document.getElementById('requestsChart').getContext('2d');
            requestsChart = new Chart(requestsCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Requêtes/min',
                        data: [],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Error Rate Chart
            const errorRateCtx = document.getElementById('errorRateChart').getContext('2d');
            errorRateChart = new Chart(errorRateCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Taux d\'erreur (%)',
                        data: [],
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        // Refresh application metrics
        function refreshApplicationMetrics() {
            fetch('/api/v1/application-monitoring/')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMetrics(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching metrics:', error);
                    showAlert('Erreur lors du chargement des métriques', 'error');
                });
        }

        // Update metrics display
        function updateMetrics(data) {
            document.getElementById('app-name').textContent = data.app_name || 'N/A';
            document.getElementById('app-version').textContent = data.app_version || 'N/A';
            document.getElementById('app-uptime').textContent = data.uptime || 'N/A';
            document.getElementById('app-status').textContent = data.status || 'N/A';

            document.getElementById('avg-response-time').textContent = data.avg_response_time || 'N/A';
            document.getElementById('min-response-time').textContent = data.min_response_time || 'N/A';
            document.getElementById('max-response-time').textContent = data.max_response_time || 'N/A';

            document.getElementById('memory-used').textContent = data.memory_used || 'N/A';
            document.getElementById('memory-available').textContent = data.memory_available || 'N/A';
            document.getElementById('memory-percentage').textContent = data.memory_percentage || 'N/A';
        }

        // Show alert
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alerts-container');
            const alertClass = {
                'success': 'bg-green-50 border-green-200 text-green-800',
                'error': 'bg-red-50 border-red-200 text-red-800',
                'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
                'info': 'bg-blue-50 border-blue-200 text-blue-800'
            };

            alertContainer.innerHTML = `
                <div class="alert ${alertClass[type]} mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                        ${message}
                    </div>
                </div>
            `;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            refreshApplicationMetrics();

            // Auto-refresh every 30 seconds
            setInterval(refreshApplicationMetrics, 30000);
        });
    </script>
</x-app-layout>
