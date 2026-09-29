@php($rtl = $inquiry['locale'] === 'ar')
<!DOCTYPE html>
<html lang="{{ $inquiry['locale'] }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Thanks for your message, :name', ['name' => $inquiry['name']]) }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f6fb;font-family:{{ $rtl ? 'Tahoma,Arial' : 'Arial,Helvetica' }},sans-serif;color:#16202e;" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" dir="{{ $rtl ? 'rtl' : 'ltr' }}" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;text-align:{{ $rtl ? 'right' : 'left' }};">
        <tr>
            <td style="background:linear-gradient(135deg,#0379a8,#2563eb);background-color:#0379a8;padding:26px 28px;color:#ffffff;">
                <div style="font-size:22px;font-weight:bold;">{{ __('Thanks for your message, :name', ['name' => $inquiry['name']]) }}</div>
                <div style="font-size:14px;opacity:.9;margin-top:6px;">{{ __('I received your inquiry and will reply within 1–2 days.') }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 28px;font-size:15px;line-height:1.75;">
                <p style="margin:0 0 14px;">{{ __('What happens next?') }}</p>
                <ol style="margin:0 0 18px;padding-{{ $rtl ? 'right' : 'left' }}:20px;">
                    <li>{{ __('I read your message and review your project.') }}</li>
                    <li>{{ __('I reply with questions or a first recommendation.') }}</li>
                    <li>{{ __('You get a clear plan with scope, timeline, and price.') }}</li>
                </ol>
                <div style="font-size:13px;color:#5c6a7e;margin-bottom:6px;">{{ __('Your message') }}</div>
                <div style="padding:14px 16px;background:#f4f6fb;border-radius:12px;white-space:pre-line;" dir="auto">{{ $inquiry['message'] }}</div>
                @if($owner['whatsapp'])
                    <p style="margin:22px 0 0;">
                        <a href="https://wa.me/{{ $owner['whatsapp'] }}" style="display:inline-block;background:#25d366;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">{{ __('Need a faster answer? Chat on WhatsApp') }}</a>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:16px 28px;border-top:1px solid #e2e8f0;font-size:13px;color:#5c6a7e;">
                <strong style="color:#16202e;">{{ $owner['name'] }}</strong><br>
                <a href="{{ $owner['site_url'] }}" style="color:#0379a8;text-decoration:none;">{{ $owner['site_url'] }}</a>
            </td>
        </tr>
    </table>
</body>
</html>
