@extends('emails.layout')

@section('preheader', 'Verify your email address for ' . config('app.name'))

@section('content')
    <!-- Hero Icon -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center; padding-bottom: 32px;">
                <div style="background-color: rgba(129, 83, 49, 0.1); border-radius: 50%; width: 80px; height: 80px; display: inline-block; vertical-align: middle; line-height: 80px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/646/646094.png" width="40" height="40" alt="Email" style="vertical-align: middle; border: 0; opacity: 0.8;">
                </div>
            </td>
        </tr>
    </table>

    <!-- Headline -->
    <h1 class="headline" style="margin: 0 0 16px 0; font-family: 'Outfit', sans-serif; font-size: 24px; line-height: 32px; color: #1a1a1a; font-weight: 700; text-align: center;">
        Verify Your Email Address
    </h1>

    <!-- Welcome Text -->
    <p class="body-text" style="margin: 0 0 24px 0; font-family: 'Outfit', sans-serif; font-size: 16px; line-height: 26px; color: #4a4a4a; text-align: center;">
        Hi <strong>{{ $user->name }}</strong>,<br>
        Thanks for joining <strong>{{ config('app.name') }}</strong>! To complete your registration and unlock all features, please verify your email address by clicking the button below.
    </p>

    <!-- Main CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center; padding: 12px 0 32px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: auto;">
                    <tr>
                        <td style="border-radius: 50px; background-color: #815331; text-align: center;">
                            <a href="{{ $url }}" target="_blank" style="background-color: #815331; border: 1px solid #815331; font-family: 'Outfit', sans-serif; font-size: 16px; line-height: 1.5; font-weight: 600; color: #ffffff; text-decoration: none; padding: 12px 32px; display: inline-block; border-radius: 50px; min-width: 180px;">
                                Verify Email Now
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Expiry Notice -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
                            <span style="font-size: 18px;">⏰</span>
                        </td>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; line-height: 22px; color: #595959;">
                            <strong style="color: #333333;">Link expires in 5 minutes.</strong><br>
                            If you didn't create an account, you can safely ignore this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Link Fallback -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 32px;">
        <tr>
            <td style="text-align: center;">
                <p style="margin: 0 0 12px 0; font-family: 'Outfit', sans-serif; font-size: 13px; color: #8c8c8c;">
                    Trouble clicking the button? Copy and paste this link into your browser:
                </p>
                <p style="margin: 0; font-family: monospace; font-size: 13px; color: #815331; word-break: break-all;">
                    <a href="{{ $url }}" style="color: #815331; text-decoration: underline;">{{ $url }}</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
