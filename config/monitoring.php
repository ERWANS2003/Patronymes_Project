<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du Monitoring
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration pour le système de monitoring
    | de l'application.
    |
    */

    'enabled' => env('MONITORING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Seuils d'Alerte
    |--------------------------------------------------------------------------
    |
    | Définit les seuils pour déclencher les alertes automatiques.
    |
    */

    'thresholds' => [
        'memory_usage_percentage' => env('MONITORING_MEMORY_THRESHOLD', 90),
        'disk_usage_percentage' => env('MONITORING_DISK_THRESHOLD', 85),
        'cpu_usage_percentage' => env('MONITORING_CPU_THRESHOLD', 80),
        'response_time_ms' => env('MONITORING_RESPONSE_TIME_THRESHOLD', 2000),
        'error_rate_percentage' => env('MONITORING_ERROR_RATE_THRESHOLD', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Logs
    |--------------------------------------------------------------------------
    |
    | Configuration pour les différents canaux de logs.
    |
    */

    'logs' => [
        'performance' => [
            'enabled' => env('MONITORING_PERFORMANCE_LOGS', true),
            'retention_days' => env('MONITORING_PERFORMANCE_RETENTION', 30),
        ],
        'errors' => [
            'enabled' => env('MONITORING_ERROR_LOGS', true),
            'retention_days' => env('MONITORING_ERROR_RETENTION', 30),
        ],
        'activity' => [
            'enabled' => env('MONITORING_ACTIVITY_LOGS', true),
            'retention_days' => env('MONITORING_ACTIVITY_RETENTION', 30),
        ],
        'security' => [
            'enabled' => env('MONITORING_SECURITY_LOGS', true),
            'retention_days' => env('MONITORING_SECURITY_RETENTION', 90),
        ],
        'queries' => [
            'enabled' => env('MONITORING_QUERY_LOGS', true),
            'retention_days' => env('MONITORING_QUERY_RETENTION', 7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Alertes
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'envoi d'alertes par email.
    |
    */

    'alerts' => [
        'enabled' => env('MONITORING_ALERTS_ENABLED', true),
        'email' => [
            'enabled' => env('MONITORING_EMAIL_ALERTS', true),
            'recipients' => explode(',', env('MONITORING_ALERT_RECIPIENTS', '')),
        ],
        'slack' => [
            'enabled' => env('MONITORING_SLACK_ALERTS', false),
            'webhook_url' => env('MONITORING_SLACK_WEBHOOK_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Métriques
    |--------------------------------------------------------------------------
    |
    | Configuration pour la collecte des métriques.
    |
    */

    'metrics' => [
        'collection_interval' => env('MONITORING_COLLECTION_INTERVAL', 60), // secondes
        'retention_days' => env('MONITORING_METRICS_RETENTION', 7),
        'real_time_enabled' => env('MONITORING_REAL_TIME_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration du Cache
    |--------------------------------------------------------------------------
    |
    | Configuration pour le cache des métriques.
    |
    */

    'cache' => [
        'enabled' => env('MONITORING_CACHE_ENABLED', true),
        'ttl' => env('MONITORING_CACHE_TTL', 300), // secondes
        'prefix' => env('MONITORING_CACHE_PREFIX', 'monitoring'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Rapports
    |--------------------------------------------------------------------------
    |
    | Configuration pour la génération de rapports.
    |
    */

    'reports' => [
        'enabled' => env('MONITORING_REPORTS_ENABLED', true),
        'storage_path' => env('MONITORING_REPORTS_PATH', 'reports'),
        'formats' => ['json', 'csv', 'pdf'],
        'schedule' => [
            'daily' => env('MONITORING_DAILY_REPORTS', true),
            'weekly' => env('MONITORING_WEEKLY_REPORTS', true),
            'monthly' => env('MONITORING_MONTHLY_REPORTS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Dashboards
    |--------------------------------------------------------------------------
    |
    | Configuration pour les dashboards de monitoring.
    |
    */

    'dashboards' => [
        'enabled' => env('MONITORING_DASHBOARDS_ENABLED', true),
        'refresh_interval' => env('MONITORING_DASHBOARD_REFRESH', 5000), // millisecondes
        'charts' => [
            'enabled' => env('MONITORING_CHARTS_ENABLED', true),
            'max_data_points' => env('MONITORING_CHART_MAX_POINTS', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de la Base de Données
    |--------------------------------------------------------------------------
    |
    | Configuration pour le monitoring de la base de données.
    |
    */

    'database' => [
        'slow_query_threshold' => env('MONITORING_SLOW_QUERY_THRESHOLD', 1000), // millisecondes
        'connection_monitoring' => env('MONITORING_DB_CONNECTIONS', true),
        'query_logging' => env('MONITORING_DB_QUERY_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des Notifications
    |--------------------------------------------------------------------------
    |
    | Configuration pour les notifications en temps réel.
    |
    */

    'notifications' => [
        'enabled' => env('MONITORING_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'email' => env('MONITORING_EMAIL_NOTIFICATIONS', true),
            'slack' => env('MONITORING_SLACK_NOTIFICATIONS', false),
            'webhook' => env('MONITORING_WEBHOOK_NOTIFICATIONS', false),
        ],
        'webhook_url' => env('MONITORING_WEBHOOK_URL'),
    ],
];
