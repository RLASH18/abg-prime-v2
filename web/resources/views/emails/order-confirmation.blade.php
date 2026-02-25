@extends('emails.layout')

@section('preheader', 'Your order has been confirmed - Order #' . $order->id)

@section('content')
    {{-- Hero Icon --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center; padding-bottom: 32px;">
                <div
                    style="background-color: rgba(129, 83, 49, 0.1); border-radius: 50%; width: 80px; height: 80px; display: inline-block; vertical-align: middle; line-height: 80px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" width="40" height="40"
                        alt="Order" style="vertical-align: middle; border: 0; opacity: 0.8;">
                </div>
            </td>
        </tr>
    </table>

    {{-- Headline --}}
    <h1 class="headline"
        style="margin: 0 0 16px 0; font-family: 'Outfit', sans-serif; font-size: 24px; line-height: 32px; color: #1a1a1a; font-weight: 700; text-align: center;">
        Order Confirmed!
    </h1>

    {{-- Welcome Text --}}
    <p class="body-text"
        style="margin: 0 0 24px 0; font-family: 'Outfit', sans-serif; font-size: 16px; line-height: 26px; color: #4a4a4a; text-align: center;">
        Hi <strong>{{ $user->name }}</strong>,<br>
        Thank you for your order! We've received your order and it's being processed.
    </p>

    {{-- Order Summary Box --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 8px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <h2
                    style="margin: 0 0 16px 0; font-family: 'Outfit', sans-serif; font-size: 18px; color: #1a1a1a; font-weight: 600;">
                    Order Details
                </h2>

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #8c8c8c; padding: 8px 0;">
                            Order Number:
                        </td>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #1a1a1a; font-weight: 600; text-align: right; padding: 8px 0;">
                            #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #8c8c8c; padding: 8px 0;">
                            Order Date:
                        </td>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #1a1a1a; font-weight: 600; text-align: right; padding: 8px 0;">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #8c8c8c; padding: 8px 0;">
                            Status:
                        </td>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #815331; font-weight: 600; text-align: right; padding: 8px 0;">
                            {{ ucfirst($order->status) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #8c8c8c; padding: 8px 0;">
                            Payment Method:
                        </td>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #1a1a1a; font-weight: 600; text-align: right; padding: 8px 0;">
                            {{ ucfirst($order->payment_method) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Order Items --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 8px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 24px;">
                <h2
                    style="margin: 0 0 16px 0; font-family: 'Outfit', sans-serif; font-size: 18px; color: #1a1a1a; font-weight: 600;">
                    Items Ordered
                </h2>

                @foreach ($order->orderItems as $item)
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                        style="margin-bottom: 12px;">
                        <tr>
                            <td style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #1a1a1a; padding: 8px 0;">
                                {{ $item->item->item_name ?? 'Product' }}
                                <br>
                                <span style="font-size: 12px; color: #8c8c8c;">Qty: {{ $item->quantity }}</span>
                            </td>
                            <td
                                style="font-family: 'Outfit', sans-serif; font-size: 14px; color: #1a1a1a; font-weight: 600; text-align: right; padding: 8px 0;">
                                ₱{{ number_format($item->unit_price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    </table>
                @endforeach

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                    style="border-top: 1px solid #f0f0f0; margin-top: 16px; padding-top: 16px;">
                    <tr>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 16px; color: #1a1a1a; font-weight: 700; padding: 8px 0;">
                            Total Amount:
                        </td>
                        <td
                            style="font-family: 'Outfit', sans-serif; font-size: 16px; color: #815331; font-weight: 700; text-align: right; padding: 8px 0;">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Delivery Information --}}
    @if ($order->delivery_method === 'delivery' && $order->delivery_address)
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
            style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 8px; margin-bottom: 24px;">
            <tr>
                <td style="padding: 24px;">
                    <h2
                        style="margin: 0 0 16px 0; font-family: 'Outfit', sans-serif; font-size: 18px; color: #1a1a1a; font-weight: 600;">
                        Delivery Address
                    </h2>
                    <p
                        style="margin: 0; font-family: 'Outfit', sans-serif; font-size: 14px; line-height: 22px; color: #4a4a4a;">
                        {{ $order->delivery_address }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

    {{-- View Order Button --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="text-align: center; padding: 12px 0 32px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: auto;">
                    <tr>
                        <td style="border-radius: 50px; background-color: #815331; text-align: center;">
                            <a href="{{ url('/orders/' . $order->id) }}" target="_blank"
                                style="background-color: #815331; border: 1px solid #815331; font-family: 'Outfit', sans-serif; font-size: 16px; line-height: 1.5; font-weight: 600; color: #ffffff; text-decoration: none; padding: 12px 32px; display: inline-block; border-radius: 50px; min-width: 180px;">
                                View Order Details
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Help Notice --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 8px;">
        <tr>
            <td style="padding: 16px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="width: 24px; vertical-align: top; padding-right: 12px;">
                            <span style="font-size: 18px;">💬</span>
                        </td>
                        <td style="font-family: 'Outfit', sans-serif; font-size: 14px; line-height: 22px; color: #595959;">
                            <strong style="color: #333333;">Need help?</strong><br>
                            If you have any questions about your order, feel free to contact our support team.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
