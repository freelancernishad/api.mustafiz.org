<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP for Mustafiz Foundation Assistance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            color: #333333;
            margin: 0 0 10px;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
            display: inline-block;
            letter-spacing: 2px;
        }
        .footer {
            background: #0072ff;
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>Thank you for registering with the Mustafiz Foundation. To complete your registration and receive assistance, please use the OTP below. It is valid for 10 minutes.</p>
            <p>Your OTP code is:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>If you did not request this, please ignore this email or contact us immediately.</p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>Mustafiz Foundation</p>
            <p>Contact Us:</p>
            <p>Email: <a href="mailto:info@mustafiz.org">info@mustafiz.org</a> <br> Website: <a href="http://mustafiz.org">mustafiz.org</a></p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
