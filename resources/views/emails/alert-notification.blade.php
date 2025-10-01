<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ $app_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: {{ $level === 'critical' ? '#dc3545' : ($level === 'warning' ? '#ffc107' : '#17a2b8') }};
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .alert-level {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: {{ $level === 'critical' ? '#dc3545' : ($level === 'warning' ? '#ffc107' : '#17a2b8') }};
            color: white;
        }
        .context {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .context pre {
            margin: 0;
            font-size: 12px;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <span class="alert-level">{{ $level }}</span>
    </div>

    <div class="content">
        <p><strong>Message:</strong></p>
        <p>{{ $message }}</p>

        <p><strong>Timestamp:</strong> {{ $timestamp }}</p>

        @if(!empty($context))
        <div class="context">
            <p><strong>Contexte:</strong></p>
            <pre>{{ json_encode($context, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ $app_url }}/admin/monitoring" class="button">
                Voir le Dashboard de Monitoring
            </a>
        </div>
    </div>

    <div class="footer">
        <p>Cette alerte a été générée automatiquement par {{ $app_name }}</p>
        <p>Si vous ne souhaitez plus recevoir ces alertes, contactez l'administrateur système.</p>
    </div>
</body>
</html>
