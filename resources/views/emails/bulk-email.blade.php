<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .email-header h1 {
            margin: 0;
            color: #4F46E5;
            font-size: 24px;
        }

        .email-body {
            margin-bottom: 30px;
            white-space: pre-wrap;
        }

        .email-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="email-body">
            {!! nl2br(e($body)) !!}
        </div>

        <div class="email-footer">
            <p>
                This email was sent from {{ config('app.name') }}<br>
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>