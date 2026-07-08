<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titleText }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f4f4f4;padding:30px 0;">
    <tr>
        <td align="center">

            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="background:#ffffff;border-radius:8px;overflow:hidden;">

                <!-- Hero Image -->
                <tr>
                    <td>
                        <img src="{{ asset($heroImageUrl) }}"
                             alt="NFL Pool Picks"
                             width="600"
                             style="display:block;width:100%;height:auto;border:0;">
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:30px;">

                        <h1 style="margin:0 0 20px;font-size:30px;color:#333333;">
                            {{ $titleText }}
                        </h1>

                        <table role="presentation" width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom:35px;">
                            @foreach ($picks as $pick)
                            <tr>
                                <td style="font-size:18px;color:#333;">{{ $pick['team'] }}</td>
                                <td align="right" style="font-size:18px;font-weight:bold;color:#333;">{{ $pick['points'] }}</td>
                            </tr>
                            @endforeach
                        </table>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>