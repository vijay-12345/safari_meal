<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
    </head>
    <body>
        <br> 
        <div style="">
            <p style="font-family: sans-serif;font-size:14px;color:#000;font-weight:500;margin-bottom:0;">
                <span style="font-family: Tahoma,Geneva,sans-serif;">Hello {{ ucfirst($first_name) }},</span>
            </p>
            &nbsp;
            <p style="font-family: sans-serif;text-align:left;color:#000;font-size:14px;font-weight:normal;line-height:19px;">
                Welcome to Taxiye Food!<br />
                <br />
                Your Verification Code is <strong>{{ $otp }}</strong>.
                <br />
            </p>
        </div>
        
        <br> <br>
        Thanks &amp; Regards <br>
        {{ config('app.regards') }}

    </body>
</html>
