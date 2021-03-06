<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'OpenSans','Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 15px; margin: 0;">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Document</title>

<style type="text/css">
img {
width: 250px;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage" style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; margin: 0;"><td style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0;" valign="top"></td>
        <td class="container" width="600" style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
            <div class="content" style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                <table width="100%" style="border:1px solid #000"  cellspacing="0">
            <tbody>
                <tr>
                    <td style="background-color:#9c8c59;text-align:center;height:60px;">
                        <img style="display:inline-block;padding: 5px;" src="{{ env('APP_URL_WEBSITE') }}/assets/images/m-f-logo-header.png" />
                        <span style="display:inline-block;
                        font-family: OpenSans;
                        font-size: 11px;
                        font-style: italic;
                        line-height:42px;
                        text-align: left;
                        color: #ffffff;
                        vertical-align:top;padding-left:5px;">for Business</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;padding-right:20px;
                    font-family: OpenSans;
                    font-size: 15px;
                    line-height: 1.91;
                    text-align: left;
                    color: #000000;
                    padding-top:40px;">
                        <div>Dear {{ $userName }},</div>
                        <div>You have received a new review! Kindly login to <a href="{{ env('APP_URL_WEBSITE') }}/vendor/reviews">Mummyfique for Business</a> site</div>
                        <div>to view the details under REVIEWS tab.</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;padding-right:20px;padding-top:20px;
                    font-family: OpenSans;
                    font-size: 15px;
                    font-style: italic;
                    line-height: 1.91;
                    text-align: left;
                    color: rgba(0, 0, 0, 0.54);">
                        This is an automated message. Please do not reply to this email.
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:20px;padding-right:20px;padding-top:40px;padding-bottom:40px;
                    font-family: OpenSans;
                    font-size: 15px;
                    line-height: 1.91;
                    text-align: left;
                    color: #000000;">
                    Thank you.
                </td>
                </tr>
            </tbody>
        </table>
        </td>
        <td style="font-family: 'OpenSans','Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0;" valign="top"></td>
    </tr></table></body>
</html>