<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your ER Medics booking</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f8fb; padding: 24px; color: #1a1a1a;">
<div style="max-width: 560px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,.06);">
    <div style="background: #c1272d; color: #fff; padding: 22px 26px;">
        <h1 style="margin: 0; font-size: 20px;">ER Medics</h1>
        <div style="font-size: 12px; opacity: .9;">Emergency Medical Training</div>
    </div>
    <div style="padding: 26px;">
        <h2 style="margin: 0 0 10px; font-size: 18px;">Hi {{ $booking->first_name }},</h2>
        <p style="margin: 0 0 14px;">Thank you for booking with ER Medics. We've received your request and a team member will contact you soon.</p>

        <p style="margin: 0 0 6px;">Your booking reference is:</p>
        <div style="background: #f5f7fb; border: 1px solid #e6e9ef; padding: 14px 16px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: bold; letter-spacing: 1px; color: #1d3557;">
            {{ $booking->booking_code }}
        </div>

        <p style="margin: 18px 0 6px;">You can track the status of your booking at any time:</p>
        <p style="margin: 0;">
            <a href="{{ $trackUrl }}" style="display: inline-block; background: #c1272d; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600;">Track my booking</a>
        </p>

        <h3 style="margin: 24px 0 8px; font-size: 14px; color: #6b7280; text-transform: uppercase; letter-spacing: .05em;">Courses requested</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f7fb;">
                    <th align="left"  style="padding: 8px 10px; font-size: 12px; color: #6b7280;">Course</th>
                    <th align="right" style="padding: 8px 10px; font-size: 12px; color: #6b7280;">Qty</th>
                    <th align="right" style="padding: 8px 10px; font-size: 12px; color: #6b7280;">Fee</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($booking->items as $i)
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #e6e9ef;">
                            <strong>{{ $i->course?->title }}</strong>
                            <div style="color: #6b7280; font-size: 12px;">{{ $i->course?->code }}@if ($i->schedule) · {{ $i->schedule->start_date->format('d M Y') }}@endif</div>
                        </td>
                        <td align="right" style="padding: 10px; border-bottom: 1px solid #e6e9ef;">{{ $i->quantity }}</td>
                        <td align="right" style="padding: 10px; border-bottom: 1px solid #e6e9ef;">{{ format_money($i->unit_price * $i->quantity) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" align="right" style="padding: 10px; font-weight: bold;">Total</td>
                    <td align="right" style="padding: 10px; font-weight: bold;">{{ format_money($booking->total_amount) }}</td>
                </tr>
            </tbody>
        </table>

        <p style="margin: 22px 0 0; color: #6b7280; font-size: 13px;">If you have questions, reply to this email or call us on <strong>{{ site_setting('phone_primary', '') }}</strong> / <strong>{{ site_setting('phone_secondary', '') }}</strong>.</p>
    </div>
    <div style="background: #f5f7fb; padding: 14px 26px; color: #6b7280; font-size: 12px; text-align: center;">
        &copy; {{ date('Y') }} ER Medics
    </div>
</div>
</body>
</html>
