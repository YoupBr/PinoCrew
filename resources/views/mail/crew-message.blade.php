<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; padding:30px 15px;">
    <tr>
        <td align="center">

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="
                    max-width:600px;
                    background:#ffffff;
                    border-radius:16px;
                    overflow:hidden;">
                <tr>
                    <td style="background:#2563eb; padding:25px 30px; color:white;">
                        <strong style="font-size:22px;">
                            PinoCrew
                        </strong>
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px; color:#334155; font-size:15px; line-height:1.7;">
                        {!! nl2br(e($mailBody)) !!}
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 30px; background:#f8fafc; color:#64748b; font-size:12px;">
                        Dit bericht is verzonden via PinoCrew.
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>