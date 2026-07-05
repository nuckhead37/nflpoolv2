<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NFL Pool Result</title>
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
                             alt="NFL Pool Champion"
                             width="600"
                             style="display:block;width:100%;height:auto;border:0;">
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:30px;">

                        <h1 style="margin:0 0 20px;font-size:30px;color:#333333;">
                            {{ $championText }}
                        </h1>

                        <h1 style="margin:0 0 20px;font-size:30px;color:#333333;">
                            {{ $titleText }}
                        </h1>

                        <table role="presentation" width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom:35px;">
                            @foreach ($users as $user)
                            <tr>
                                <td style="font-size:18px;color:#333;">{{ $user['name'] }}</td>
                                <td align="right" style="font-size:18px;font-weight:bold;color:#333;">{{ $user['points'] }}</td>
                            </tr>
                            @endforeach
                        </table>

                        <h2 style="margin:0 0 15px;font-size:24px;color:#333;">
                            Season Totals
                        </h2>

                        <table role="presentation"
                               width="100%"
                               cellpadding="8"
                               cellspacing="0"
                               border="0"
                               style="border-collapse:collapse;">

                            <thead>
                                <tr style="background:#f3f3f3;">
                                    <th align="left" style="font-size:16px;color:#333;">Player</th>
                                    <th align="right" style="font-size:16px;color:#333;">Total</th>
                                    <th align="right" style="font-size:16px;color:#333;">Wins</th>
                                    <th align="right" style="font-size:16px;color:#333;">Tied</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($totals as $player)
                                <tr>
                                    <td style="font-size:16px;padding:10px 8px;">
                                        {{ $player->name }}
                                    </td>
                                    <td align="right">
                                        {{ $player->total }}
                                    </td>
                                    <td align="right">
                                        {{ $player->wins }}
                                    </td>
                                    <td align="right">
                                        {{ $player->tied }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>