<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{{ env('APP_NAME') }} Email Verification Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <style>
        body {
            font-family: 'Poppins', 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #486fe9;
            font-weight: 700;
            font-size: 20px;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .token-container {
            background-color: #e8f0fe;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .token {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            letter-spacing: 2px;
        }

        .content {
            padding: 20px;
            min-height: 300px;
            /* Set minimum height for content */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CB315;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 style="font-size: 20px;">{{ env('APP_NAME') }} Email Verification Code</h1>
        </div>
        <div class="content">
            <h2>Hello, Welcome to {{ env('APP_NAME') }}</h2>
            <p style="font-size: 14px; line-height: 25px; text-align: center; margin: 0 2rem;">Thank you for registering
                with us! Please verify your email address by clicking
                the button below:</p>

            <div class="token-container">
                <span class="token">{{ $code }}</span>
            </div>
            <p style="font-size: 14px">If you did not create an account, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>{{ env('APP_NAME') }} All rights reserved</p>
            <p style="font-size: 12px">This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
