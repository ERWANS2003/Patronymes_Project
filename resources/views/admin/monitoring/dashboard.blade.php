<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Monitoring Dashboard
                </h1>
                <p class="text-gray-600 mt-1">
                    Surveillance en temps réel de l'application
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshMetrics()" class="btn btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- System Health Status -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card bg-green-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold">Statut</h4>
                            <p class="text-green-100">Système opérationnel</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-blue-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="uptime">-</h4>
                            <p class="text-blue-100">Temps de fonctionnement</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-purple-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="memory-usage">-</h4>
                            <p class="text-purple-100">Utilisation mémoire</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-memory text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-orange-600 text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-2xl font-bold" id="response-time">-</h4>
                            <p class="text-orange-100">Temps de réponse</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-stopwatch text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- System Metrics -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-server text-blue-600 mr-2"></i>
                        Métriques système
                    </h3>
                    <div class="space-y-4" id="system-metrics">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">CPU Usage</span>
                            <span class="text-lg font-bold text-gray-900" id="cpu-usage">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Memory Usage</span>
                            <span class="text-lg font-bold text-gray-900" id="memory-usage-detail">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Disk Usage</span>
                            <span class="text-lg font-bold text-gray-900" id="disk-usage">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Metrics -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-cube text-green-600 mr-2"></i>
                        Métriques application
                    </h3>
                    <div class="space-y-4" id="app-metrics">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Requêtes/min</span>
                            <span class="text-lg font-bold text-gray-900" id="requests-per-minute">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Erreurs/min</span>
                            <span class="text-lg font-bold text-gray-900" id="errors-per-minute">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Utilisateurs actifs</span>
                            <span class="text-lg font-bold text-gray-900" id="active-users">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-alt text-red-600 mr-2"></i>
                    Logs récents
                </h3>
                <div id="recent-logs" class="space-y-2">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Chargement des logs...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Refresh metrics
        function refreshMetrics() {
            fetch('/admin/monitoring/metrics')
                .then(response => response.json())
                .then(data => {
                    updateMetrics(data);
                })
                .catch(error => {
                    console.error('Error fetching metrics:', error);
                });
        }

        // Update metrics display
        function updateMetrics(data) {
            if (data.uptime) {
                document.getElementById('uptime').textContent = data.uptime;
            }
            if (data.memory_usage) {
                document.getElementById('memory-usage').textContent = data.memory_usage;
                document.getElementById('memory-usage-detail').textContent = data.memory_usage;
            }
            if (data.response_time) {
                document.getElementById('response-time').textContent = data.response_time;
            }
            if (data.cpu_usage) {
                document.getElementById('cpu-usage').textContent = data.cpu_usage;
            }
            if (data.disk_usage) {
                document.getElementById('disk-usage').textContent = data.disk_usage;
            }
            if (data.requests_per_minute) {
                document.getElementById('requests-per-minute').textContent = data.requests_per_minute;
            }
            if (data.errors_per_minute) {
                document.getElementById('errors-per-minute').textContent = data.errors_per_minute;
            }
            if (data.active_users) {
                document.getElementById('active-users').textContent = data.active_users;
            }
        }

        // Load recent logs
        function loadRecentLogs() {
            fetch('/admin/monitoring/logs?lines=10')
                .then(response => response.json())
                .then(data => {
                    const logsContainer = document.getElementById('recent-logs');
                    if (data.logs && data.logs.length > 0) {
                        logsContainer.innerHTML = data.logs.map(log => `
                            <div class="p-3 bg-gray-50 rounded-lg text-sm font-mono">
                                ${log}
                            </div>
                        `).join('');
                    } else {
                        logsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Aucun log récent</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    document.getElementById('recent-logs').innerHTML = '<p class="text-red-500 text-center py-4">Erreur lors du chargement des logs</p>';
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            refreshMetrics();
            loadRecentLogs();

            // Auto-refresh every 30 seconds
            setInterval(refreshMetrics, 30000);
            setInterval(loadRecentLogs, 60000);
        });
    </script>
</x-app-layout>
