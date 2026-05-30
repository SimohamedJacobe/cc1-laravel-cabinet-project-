<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Appointment Confirmed') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        .wrapper {
            background-color: #f4f4f7;
            width: 100%;
            padding: 40px 0;
        }
        .content {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 570px;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid #e8e5ef;
        }
        .header {
            background-color: #4f46e5;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .body {
            padding: 30px;
        }
        .body p {
            font-size: 16px;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table td {
            padding: 12px 0;
            border-bottom: 1px solid #e8e5ef;
            font-size: 15px;
        }
        .table td.label {
            font-weight: bold;
            color: #333333;
            width: 35%;
        }
        .table td.value {
            color: #51545e;
        }
        .footer {
            text-align: center;
            padding: 30px;
            background-color: #f8fafc;
            border-top: 1px solid #e8e5ef;
            font-size: 13px;
            color: #b0adc5;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff !important;
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <h1>{{ __('Appointment Confirmed') }}</h1>
            </div>
            <div class="body">
                <p>{{ __('Hello') }} {{ $appointment->user->name }},</p>
                <p>{{ __('Your appointment has been successfully scheduled and confirmed. Below are the details of your booking:') }}</p>
                
                <table class="table">
                    <tr>
                        <td class="label">{{ __('Patient Name') }}</td>
                        <td class="value">{{ $appointment->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('Medical Service') }}</td>
                        <td class="value">{{ $appointment->service->name }} ({{ number_format($appointment->service->price, 0) }}€)</td>
                    </tr>
                    <tr>
                        <td class="label">{{ __('Date & Time') }}</td>
                        <td class="value">
                            {{ $appointment->appointment_date ? $appointment->appointment_date->format('M d, Y') : '' }}
                            @ {{ $appointment->appointment_date ? $appointment->appointment_date->format('h:i A') : '' }}
                        </td>
                    </tr>
                    @if($appointment->notes)
                        <tr>
                            <td class="label">{{ __('Notes') }}</td>
                            <td class="value">{{ $appointment->notes }}</td>
                        </tr>
                    @endif
                </table>

                <p>{{ __('If you need to make changes or cancel, please log in to your dashboard.') }}</p>
                
                <div style="text-align: center;">
                    <a href="{{ route('appointments.index') }}" class="btn">{{ __('View in Dashboard') }}</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
