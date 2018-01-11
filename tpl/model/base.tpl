<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>{$title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body style="margin: 0; padding: 0;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 20px 0 30px 0;">
            <table style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; border: 1px solid #cccccc;" align="center" border="0" cellpadding="0" cellspacing="0"
                   width="100%" style="border-collapse: collapse;">
                <tr>
                    {$email_header}
                </tr>
                <tr style="padding: 10px 30px 0px 30px">
                    {$email_content}
                </tr>
                <tr>
                    {$email_footer}
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>