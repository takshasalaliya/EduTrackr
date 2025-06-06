<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject }}</title>
    <style type="text/css">
        /* Reset Styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* General Styles */
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden; /* Ensures border-radius clips content */
        }
        .email-header {
            background-color: #0A2540; /* Your admin panel's dark blue */
            color: #ffffff;
            padding: 20px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .email-header img { /* Optional: If you have a logo */
            max-width: 150px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
            font-size: 16px;
        }
        .email-body p {
            margin: 0 0 15px 0;
        }
        .email-body .label {
            font-weight: bold;
            color: #555555;
        }
        .email-body .value {
            color: #0A2540; /* Dark blue for values */
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .info-table td:first-child {
            width: 40%; /* Adjust as needed */
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .credentials {
            background-color: #eef2f7; /* Light background from your theme */
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dfe6ee;
            margin-top: 20px;
        }
        .credentials p {
            margin: 0 0 10px 0;
        }
        .credentials .password-value {
            font-weight: bold;
            font-size: 18px;
            color: #d9534f; /* A distinct color for password */
            background-color: #fcf8e3; /* Light yellow highlight */
            padding: 5px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .email-footer {
            background-color: #f4f4f4;
            color: #777777;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #dddddd;
        }
        .email-footer a {
            color: #007bff; /* Your primary blue */
            text-decoration: none;
        }

        /* Responsive Styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            .email-body, .email-header, .email-footer {
                padding: 20px !important;
            }
            .email-header h1 {
                font-size: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0 !important; padding: 0 !important; background-color: #f4f4f4;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
        <tr>
            <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
    <![endif]-->
    <div class="email-container" style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px;">
        <div class="email-header" style="background-color: #0A2540; color: #ffffff; padding: 20px 30px; text-align: center;">
            <!-- Optional: Add your logo here -->
            <img src="https://i.ibb.co/yFhzNxBJ/3-removebg-preview.png" alt="Logo" style="max-width: 150px; margin-bottom: 10px;">
            <h1 style="margin: 0; font-size: 24px; font-weight: bold;">{{ $subject }}</h1>
        </div>

        <div class="email-body" style="padding: 30px; color: #333333; line-height: 1.6; font-size: 16px;">
            <p style="margin: 0 0 15px 0;">Hello {{ $name }},</p>
            <p style="margin: 0 0 20px 0;">Here are your account details for the system. Please keep them safe.</p>

            <table class="info-table" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="label" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; font-weight: bold; color: #555555; width: 30%;">Full Name:</td>
                    <td class="value" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; color: #0A2540;">{{ $name }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; font-weight: bold; color: #555555;">Short Name:</td>
                    <td class="value" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; color: #0A2540;">{{ $shortname }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; font-weight: bold; color: #555555;">Phone:</td>
                    <td class="value" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; color: #0A2540;">{{ $phone }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; font-weight: bold; color: #555555;">Counselor Status:</td>
                    <td class="value" style="padding: 8px 0; border-bottom: 1px solid #eeeeee; color: #0A2540;">
                        {{ $counselor == 'yes' || $counselor === true || $counselor == 1 ? 'Yes' : 'No' }}
                    </td>
                </tr>
            </table>

            <div class="credentials" style="background-color: #eef2f7; padding: 15px; border-radius: 6px; border: 1px solid #dfe6ee; margin-top: 20px;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #333333;">Your Login Credentials:</p>
                <p style="margin: 0 0 10px 0;"><span class="label" style="font-weight: bold; color: #555555;">Username/Email:</span> <span class="value" style="color: #0A2540;">(Your system might use email or another username - please specify if different, e.g., {{ $email_if_available ?? 'Your registered email' }})</span></p>
                <p style="margin: 0 0 0 0;"><span class="label" style="font-weight: bold; color: #555555;">Password:</span> <span class="password-value" style="font-weight: bold; font-size: 18px; color: #d9534f; background-color: #fcf8e3; padding: 5px 8px; border-radius: 4px; display: inline-block;">{{ $password }}</span></p>
            </div>

            <p style="margin: 25px 0 15px 0;">
                It is highly recommended to change your password after your first login.
            </p>
            <p style="margin: 0 0 15px 0;">
                You can log in to the system at: <a href="YOUR_LOGIN_URL_HERE" style="color: #007bff; text-decoration: none;">YOUR_LOGIN_URL_HERE</a>
            </p>

            <p style="margin: 0 0 15px 0;">If you have any questions, please contact support.</p>
            <p style="margin: 0;">Sincerely,<br>The {{-- Your System/Organization Name --}} Team</p>
        </div>

    </div>
    <!--[if mso | IE]>
            </td>
        </tr>
    </table>
    <![endif]-->
</body>
</html>