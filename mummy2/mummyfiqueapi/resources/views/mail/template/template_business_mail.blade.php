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
                <table width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr style="background-color:#9c8c59;">
                    <td width="20%"></td>
                    <td style="background-color:#9c8c59;text-align:center;">
                        <img style="display:inline-block;width: auto;" src="{{ env('APP_URL_WEBSITE') }}/assets/images/email-logo-business.png" />
                    </td>
                    <td width="20%"></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left:20px;padding-right:20px;padding-top:40px;
                    font-family: Arial, OpenSans;
                    font-size: 15px;
                    line-height: 1.6em;
                    text-align: left;
                    color: #000000;
                    padding-top:20px;">
                        {!! $content !!}                        
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left:20px;padding-right:20px;padding-top:40px;
                    font-family: Arial, OpenSans;
                    font-size: 15px;
                    font-style: italic;
                    line-height: 1.6em;
                    text-align: left;
                    color: rgba(0, 0, 0, 0.54);">
                        This is an automated message. Please do not reply to this email.
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left:20px;padding-right:20px;padding-top:50px;padding-bottom:50px;
                    font-family: Arial, OpenSans;
                    font-size: 15px;
                    line-height: 1.6em;
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