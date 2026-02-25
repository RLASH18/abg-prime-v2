<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ config('app.name') }}</title>
    <!--[if mso]>
    <style>
        * { font-family: sans-serif !important; }
    </style>
    <![endif]-->
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!--<![endif]-->
    <style>
        /* Base Resets */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            font-family: 'Outfit', sans-serif;
            background-color: #f6f6f6;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        a {
            text-decoration: none;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .content-padding {
                padding: 24px 20px !important;
            }

            .header-padding {
                padding: 30px 20px !important;
            }

            .mobile-hidden {
                display: none !important;
            }

            .mobile-block {
                display: block !important;
                width: 100% !important;
            }

            .brand-name {
                font-size: 18px !important;
                line-height: 36px !important;
            }

            .headline {
                font-size: 20px !important;
                line-height: 28px !important;
            }

            .body-text {
                font-size: 14px !important;
                line-height: 22px !important;
            }
        }
    </style>
</head>

<body width="100%"
    style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f6f6f6;">
    <center style="width: 100%; background-color: #f6f6f6;">
        <!-- Visually Hidden Preheader -->
        <div
            style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
            @yield('preheader', 'Notification from ' . config('app.name'))
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>

        <!-- Main Email Container -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
            class="email-container"
            style="margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); margin-top: 40px; margin-bottom: 40px;">

            <!-- Header Section -->
            <tr>
                <td class="header-padding"
                    style="padding: 40px 40px 32px 40px; text-align: center; background-color: #ffffff;">
                    <a href="{{ url('/') }}" style="display: inline-block; text-decoration: none;">
                        <!-- Brand Logo -->
                        <div style="display: inline-flex; align-items: center; justify-content: center;">
                            {{-- Production Logo
                            <img src="{{ config('app.url') }}/img/abg-logo.png"
                                alt="ABG Logo"
                                style="width: 48px; height: 48px; object-fit: contain; margin-right: 12px; vertical-align: middle;" /> --}}

                            {{-- Development Logo --}}
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/abg-logo.png'))) }}"
                                alt="ABG Logo"
                                style="width: 48px; height: 48px; object-fit: contain; margin-right: 12px; vertical-align: middle;" />
                            <span class="brand-name"
                                style="font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; color: #1a1a1a; vertical-align: middle; line-height: 48px; display: inline-block;">
                                {{ config('app.name') }}
                            </span>
                        </div>
                    </a>
                </td>
            </tr>

            <!-- Divider Line -->
            <tr>
                <td style="padding: 0 40px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="border-bottom: 1px solid #f0f0f0;"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Content Section -->
            <tr>
                <td class="content-padding" style="padding: 40px 40px 40px 40px; background-color: #ffffff;">
                    @yield('content')
                </td>
            </tr>

            <!-- Footer Section -->
            <tr>
                <td
                    style="background-color: #fbfbfb; padding: 32px 40px; text-align: center; border-top: 1px solid #f0f0f0;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="text-align: center; padding-bottom: 24px;">
                                <a href="#" style="margin: 0 8px;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24"
                                        alt="Facebook" style="border:0; opacity: 0.6;">
                                </a>
                                <a href="#" style="margin: 0 8px;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="24"
                                        alt="Twitter" style="border:0; opacity: 0.6;">
                                </a>
                                <a href="#" style="margin: 0 8px;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" width="24"
                                        alt="Instagram" style="border:0; opacity: 0.6;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="font-family: 'Outfit', sans-serif; font-size: 14px; line-height: 22px; color: #8c8c8c;">
                                <p style="margin: 0 0 12px 0;">
                                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                </p>
                                <p style="margin: 0;">
                                    You received this email because you signed up for an account on our platform.<br>
                                    <a href="#" style="color: #815331; text-decoration: underline;">Privacy
                                        Policy</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>

        <!-- Bottom Spacer -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td height="40" style="height: 40px;">&nbsp;</td>
            </tr>
        </table>
    </center>
</body>

</html>
