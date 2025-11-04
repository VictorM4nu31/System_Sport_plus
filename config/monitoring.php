<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the system monitoring
    | and alerting functionality.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Alert Settings
    |--------------------------------------------------------------------------
    |
    | Configure alert thresholds and notification settings.
    |
    */

    'alerts' => [

        // Database monitoring thresholds
        'database' => [
            'response_time_threshold' => env('DB_RESPONSE_TIME_THRESHOLD', 1000), // milliseconds
            'long_query_threshold' => env('DB_LONG_QUERY_THRESHOLD', 30), // seconds
        ],

        // Payment monitoring thresholds
        'payments' => [
            'failure_rate_threshold' => env('PAYMENT_FAILURE_THRESHOLD', 5), // failures per hour
            'processing_time_threshold' => env('PAYMENT_PROCESSING_THRESHOLD', 30), // seconds
        ],

        // Stock monitoring thresholds
        'inventory' => [
            'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5), // units
            'out_of_stock_threshold' => env('OUT_OF_STOCK_THRESHOLD', 10), // number of products
        ],

        // Queue monitoring thresholds
        'queue' => [
            'failed_jobs_threshold' => env('QUEUE_FAILED_THRESHOLD', 10), // failures per hour
            'queue_size_threshold' => env('QUEUE_SIZE_THRESHOLD', 1000), // number of jobs
        ],

        // Storage monitoring thresholds
        'storage' => [
            'usage_threshold' => env('STORAGE_USAGE_THRESHOLD', 85), // percentage
        ],

        // Application monitoring thresholds
        'application' => [
            'error_rate_threshold' => env('ERROR_RATE_THRESHOLD', 50), // errors per hour
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure how and when notifications are sent.
    |
    */

    'notifications' => [

        // Email notifications
        'email' => [
            'enabled' => env('MONITORING_EMAIL_ENABLED', true),
            'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'critical_only' => env('MONITORING_EMAIL_CRITICAL_ONLY', true),
        ],

        // Slack notifications (optional)
        'slack' => [
            'enabled' => env('MONITORING_SLACK_ENABLED', false),
            'webhook_url' => env('MONITORING_SLACK_WEBHOOK'),
            'channel' => env('MONITORING_SLACK_CHANNEL', '#alerts'),
        ],

        // SMS notifications (optional)
        'sms' => [
            'enabled' => env('MONITORING_SMS_ENABLED', false),
            'service' => env('SMS_SERVICE', 'twilio'), // twilio, nexmo, etc.
            'admin_phone' => env('ADMIN_PHONE'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Settings
    |--------------------------------------------------------------------------
    |
    | Configure health check intervals and settings.
    |
    */

    'health_check' => [

        // How often to run health checks (in minutes)
        'interval' => env('HEALTH_CHECK_INTERVAL', 5),

        // Cache health check results for this many minutes
        'cache_duration' => env('HEALTH_CHECK_CACHE', 2),

        // Enable/disable specific health checks
        'checks' => [
            'database' => env('HEALTH_CHECK_DATABASE', true),
            'payments' => env('HEALTH_CHECK_PAYMENTS', true),
            'inventory' => env('HEALTH_CHECK_INVENTORY', true),
            'queue' => env('HEALTH_CHECK_QUEUE', true),
            'storage' => env('HEALTH_CHECK_STORAGE', true),
            'application' => env('HEALTH_CHECK_APPLICATION', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Collection
    |--------------------------------------------------------------------------
    |
    | Configure metrics collection and retention.
    |
    */

    'metrics' => [

        // How long to keep metrics data (in days)
        'retention_days' => env('METRICS_RETENTION_DAYS', 30),

        // Metrics collection interval (in minutes)
        'collection_interval' => env('METRICS_INTERVAL', 15),

        // Enable/disable specific metrics
        'collect' => [
            'orders' => env('COLLECT_ORDER_METRICS', true),
            'payments' => env('COLLECT_PAYMENT_METRICS', true),
            'users' => env('COLLECT_USER_METRICS', true),
            'inventory' => env('COLLECT_INVENTORY_METRICS', true),
            'performance' => env('COLLECT_PERFORMANCE_METRICS', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Log Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure log file monitoring and analysis.
    |
    */

    'log_monitoring' => [

        // Enable log file monitoring
        'enabled' => env('LOG_MONITORING_ENABLED', true),

        // Log files to monitor
        'files' => [
            'laravel' => storage_path('logs/laravel.log'),
            'payments' => storage_path('logs/payments.log'),
            'security' => storage_path('logs/security.log'),
            'orders' => storage_path('logs/orders.log'),
        ],

        // Error patterns to look for
        'error_patterns' => [
            'ERROR:',
            'CRITICAL:',
            'EMERGENCY:',
            'Fatal error',
            'Exception',
        ],

        // How far back to analyze logs (in hours)
        'analysis_window' => env('LOG_ANALYSIS_WINDOW', 1),

    ],

    /*
    |--------------------------------------------------------------------------
    | Security Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure security event monitoring and alerting.
    |
    */

    'security' => [

        // Enable security monitoring
        'enabled' => env('SECURITY_MONITORING_ENABLED', true),

        // Failed login attempt threshold
        'failed_login_threshold' => env('FAILED_LOGIN_THRESHOLD', 5),

        // Suspicious activity patterns
        'suspicious_patterns' => [
            'multiple_failed_logins',
            'unusual_payment_patterns',
            'admin_access_attempts',
            'sql_injection_attempts',
            'xss_attempts',
        ],

        // IP whitelist for admin access
        'admin_ip_whitelist' => env('ADMIN_IP_WHITELIST', ''),

        // Enable rate limiting monitoring
        'rate_limit_monitoring' => env('RATE_LIMIT_MONITORING', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure performance monitoring and optimization alerts.
    |
    */

    'performance' => [

        // Enable performance monitoring
        'enabled' => env('PERFORMANCE_MONITORING_ENABLED', true),

        // Response time thresholds (in milliseconds)
        'response_time_thresholds' => [
            'web' => env('WEB_RESPONSE_THRESHOLD', 2000),
            'api' => env('API_RESPONSE_THRESHOLD', 1000),
            'admin' => env('ADMIN_RESPONSE_THRESHOLD', 3000),
        ],

        // Memory usage threshold (in MB)
        'memory_threshold' => env('MEMORY_THRESHOLD', 512),

        // CPU usage threshold (percentage)
        'cpu_threshold' => env('CPU_THRESHOLD', 80),

        // Enable slow query logging
        'slow_query_logging' => env('SLOW_QUERY_LOGGING', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of monitoring data.
    |
    */

    'cleanup' => [

        // Enable automatic cleanup
        'enabled' => env('MONITORING_CLEANUP_ENABLED', true),

        // How often to run cleanup (cron expression)
        'schedule' => env('MONITORING_CLEANUP_SCHEDULE', '0 2 * * *'), // Daily at 2 AM

        // Data retention periods (in days)
        'retention' => [
            'alerts' => env('ALERT_RETENTION_DAYS', 30),
            'metrics' => env('METRICS_RETENTION_DAYS', 90),
            'logs' => env('LOG_RETENTION_DAYS', 14),
            'failed_jobs' => env('FAILED_JOBS_RETENTION_DAYS', 7),
            'sessions' => env('SESSION_RETENTION_DAYS', 1),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    |
    | Configure the monitoring dashboard.
    |
    */

    'dashboard' => [

        // Enable monitoring dashboard
        'enabled' => env('MONITORING_DASHBOARD_ENABLED', true),

        // Dashboard refresh interval (in seconds)
        'refresh_interval' => env('DASHBOARD_REFRESH_INTERVAL', 30),

        // Number of recent alerts to show
        'recent_alerts_count' => env('DASHBOARD_ALERTS_COUNT', 10),

        // Number of recent metrics to show
        'recent_metrics_count' => env('DASHBOARD_METRICS_COUNT', 20),

        // Enable real-time updates
        'real_time_updates' => env('DASHBOARD_REAL_TIME', true),

    ],

];
