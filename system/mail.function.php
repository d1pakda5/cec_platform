<?php
require 'class.phpmailer.php';
function phpMailerFunction($to, $subject, $message) {
	$mail =	new PHPMailer(true); //New instance, with exceptions enabled
	$body	=	$message;
	$body	=	preg_replace('/\\\\/','', $body); //Strip backslashes
	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 25;                    // set the SMTP server port
	$mail->Host       = "mail.99-604-99-605.com"; // SMTP server
	$mail->Username   = "info@99-604-99-605.com";     // SMTP server username
	$mail->Password   = "ankit@2017";            // SMTP server password
	$mail->IsSendmail();  // tell the class to use Sendmail
	$mail->AddReplyTo("info@99-604-99-605.com","Support Team");
	$mail->From       = "info@99-604-99-605.com";
	$mail->FromName   = "Support Team";
	$mail->AddAddress($to);
	$mail->Subject  = $subject;
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($body);
	$mail->IsHTML(true); // send as HTML
	$mail->Send();
}

function mailNewClient($fullname, $company_name, $mobile, $email, $username, $password, $sitename = '') {
	$subject = $sitename." new account created successfully";
	$message = "<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Dear ".$fullname.", <br><br>
						Your account has been created successfully, below are the account and login details for your account!<br><br>
						Full Name : ".$fullname."<br>
						Company Name : ".$company_name."<br>
						Registered Mobile : ".$mobile."<br>
						Email : ".$email."<br><br>
						Username : ".$username."<br>
						Password : ".$password."<br><br></td>
					</tr>
					<tr>
						<td>Please do not share your account details to anybody</td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/>".$sitename."</td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

function mailNewAdmin($email, $fullname, $mobile, $username, $password, $pin, $sitename = '') {
	$subject = $sitename." new account created successfully";
	$message = "<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Dear ".$fullname.", <br><br>
						Your account has been created successfully, below are the account and login details for your account!<br><br>
						Full Name : ".$fullname."<br>
						Mobile : ".$mobile."<br>
						Email : ".$email."<br><br>
						Username : ".$username."<br>
						Password : ".$password."<br>
						Pin : ".$pin."<br><br></td>
					</tr>
					<tr>
						<td>Please do not share your account details to anybody</td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/>".$sitename."</td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

function mailChangePin($email, $username, $pin) {
	$subject = "PIN reset details";
	$message = "<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Hi ".$username.", <br><br>
						Your PIN has been changed successfully, your new PIN is: <b>".$pin."</b><br><br>
						If you made this change, you don't need to do anything more.<br>If you didn't change your pin, your account might have been hijacked. To get back into your account, you'll need to reset your pin.<br>Please do not share your pin details to anybody<br><br></td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/></td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

function mailForgetPassword($email, $username, $password) {
	$subject = " password reset details";
	$message = "<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Hi ".$username.", <br><br>
						Your Password has been successfully reset, your new password is: <b>".$password."</b><br></td>
					</tr>
					<tr>
						<td>Please do not share your password details to anybody<br><br></td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/></td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {	
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

function mailChangePassword($email, $username, $password) {
	$subject =	"Password chanage confirmation";
	$message =	"<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Hi ".$username.", <br><br>
						Your Password has been recently change, your new password is: <b>".$password."<b><br><br>
						If you made this change, you don't need to do anything more.<br>If you didn't change your password, your account might have been hijacked. To get back into your account, you'll need to reset your password.<br>Please do not share your pin details to anybody<br><br></td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/></td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

function mailFundTransfer($email, $fullname, $amount, $closing_balance, $date) {
	$subject = "Fund transfer confirmation";
	$message = "<html>
				<body>
				<table border='0' cellpadding='5' cellspacing='5' width='100%'>
					<tr>
						<td>Dear ".$fullname.", <br><br>
							Balance has been credited to your account, below are the details<br><br>
							<hr>
							Added Date : ".$date."<br>
							<hr>
							Amount : Rs ".$amount."<br>
							<hr>
							Closing Balance : Rs ".$closing_balance."<br>
							<hr><br><br>
						</td>
					</tr>
					<tr>
						<td>Thanks,<br />Support Team<br/></td>
					</tr>
				</table>
				</body>
				</html>";
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}
function sendbulkemail($email, $msg) {
	$subject = "Click-E-Charge: Multirecharge Services";
	$message = '<html>
<head>
<title>A Responsive Email Template</title>


<style type="text/css">
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a{-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
    table, td{mso-table-lspace: 0pt; mso-table-rspace: 0pt;} /* Remove spacing between tables in Outlook 2007 and up */
    img{-ms-interpolation-mode: bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

    /* RESET STYLES */
    img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;}
    table{border-collapse: collapse !important;}
    body{height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;}

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* MOBILE STYLES */
    @media screen and (max-width: 525px) {

        /* ALLOWS FOR FLUID TABLES */
        .wrapper {
          width: 100% !important;
        	max-width: 100% !important;
        }

        /* ADJUSTS LAYOUT OF LOGO IMAGE */
        .logo img {
          margin: 0 auto !important;
        }

        /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */
        .mobile-hide {
          display: none !important;
        }

        .img-max {
          max-width: 100% !important;
          width: 100% !important;
          height: auto !important;
        }

        /* FULL-WIDTH TABLES */
        .responsive-table {
          width: 100% !important;
        }

        /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */
        .padding {
          padding: 10px 5% 15px 5% !important;
        }

        .padding-meta {
          padding: 30px 5% 0px 5% !important;
          text-align: center;
        }

        .padding-copy {
     		padding: 10px 5% 10px 5% !important;
          text-align: center;
        }

        .no-padding {
          padding: 0 !important;
        }

        .section-padding {
          padding: 50px 15px 50px 15px !important;
        }

        /* ADJUST BUTTONS ON MOBILE */
        .mobile-button-container {
            margin: 0 auto;
            width: 100% !important;
        }

        .mobile-button {
            padding: 15px !important;
            border: 0 !important;
            font-size: 16px !important;
            display: block !important;
        } 
        
    }

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
<!--[if gte mso 12]>
<style type="text/css">
.mso-right {
	padding-left: 20px;
}
</style>
<![endif]-->
</head>
<body style="margin: 0 !important; padding: 0 !important;">

<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    Entice the open with some amazing preheader text. Use a little mystery and get those subscribers to read through...
</div>

<!-- HEADER -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#333333" align="center">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
            <tr>
            <td align="center" valign="top" width="500">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="wrapper">
                <tr>
                    <td align="center" valign="top" style="padding: 15px 0;" class="logo">
                        <a href="http://clickecharge.com" target="_blank">
                            <img alt="Logo" src="/home/recharge/public_html/images/logo-1.png"  style="display: block; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-size: 16px;" border="0">
                        </a>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#fbeca8" align="center" style="padding: 70px 15px 70px 15px;" class="section-padding">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
            <tr>
            <td align="center" valign="top" width="500">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td>
                        <!-- HERO IMAGE -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              	<td align="center" class="padding">
                                  <a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-1.png" width="500" height="300" border="0" alt="Insert alt text here" style="display: block; padding: 0; color: #266e9c; text-decoration: none; font-family: Helvetica, arial, sans-serif; font-size: 16px;" class="img-max"></a>
                              </td>
                            </tr>
                            <tr>
                                <td>
                                    <!-- COPY -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #000000; padding-top: 15px;opacity: 1" class="padding-copy">India\'s Leading MultiRecharge Service Provider </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 70px 15px 25px 15px;" class="section-padding">
            <table border="0" cellpadding="0" cellspacing="0" width="500" style="padding:0 0 20px 0;" class="responsive-table">
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding-bottom: 35px;">
                        <!--[if (gte mso 9)|(IE)]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                        <tr>
                        <td align="center" valign="top" width="500">
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:500;">
                            <tr>
                                <td align="center" valign="top" style="font-size:0;">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                    <td align="left" valign="top" width="150">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:150px; vertical-align:top; width:100%;">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                            <tr>
                                                <td valign="top"><a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-2.png" alt="alt text here" width="150" height="200" border="0" style="display: block; font-family: Arial; color: #266e9c; font-size: 14px;"></a></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    <td align="left" valign="top" width="350">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:350px; vertical-align:top; width:100%;" class="wrapper">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 325px; float: right;" class="wrapper">
                                            <tr>

                                                <td style="padding: 0px 0 0 0;" class="no-padding">
                                                    <!-- ARTICLE -->
                                                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                        <tr>
                                                            <td align="left" style="padding: 0 0 5px 0; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">MultiRecharge Software (B2B)</td>
                                                        </tr>
                                                        <tr>
                                                             <td align="left" style="padding: 10px 0 15px 0; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">Mobile Recharge Software by ClickECharge is most trusted platform in Multi Recharge Industry. Our premium service platform always produces best customer experience and ensure profit of software admin.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
                </tr>
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding-bottom: 35px;">
                        <!--[if (gte mso 9)|(IE)]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                        <tr>
                        <td align="center" valign="top" width="500">
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:500;">
                            <tr>
                                <td align="center" valign="top" style="font-size:0;" dir="rtl">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                    <td align="left" valign="top" width="150">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:150px; vertical-align:top; width:100%;" dir="ltr">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                            <tr>
                                                <td valign="top"><a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-3.png" alt="alt text here" width="150" height="200" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px;"></a></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    <td align="left" valign="top" width="350">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:350px; vertical-align:top; width:100%;" dir="ltr">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 325px;">
                                            <tr>

                                                <td style="padding: 0px 0 0 0;" class="no-padding">
                                                    <!-- ARTICLE -->
                                                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                        <tr>
                                                            <td align="left" style="padding: 0 0 5px 0; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">API Services</td>
                                                        </tr>
                                                        <tr>
                                                             <td align="left" style="padding: 10px 0 15px 0; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">ClickECharge offers Mobile recharge API with all Mobile, DTH, Data Card, Post Paid Bill Payments API and also Money Transfer API.
                                                             Call Us Now +91-9860647921</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
                </tr>
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding-bottom: 25px;">
                        <!--[if (gte mso 9)|(IE)]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                        <tr>
                        <td align="center" valign="top" width="500">
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:500;">
                            <tr>
                                <td align="center" valign="top" style="font-size:0;">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                    <td align="left" valign="top" width="150">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:150px; vertical-align:top; width:100%;">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                            <tr>
                                                <td valign="top"><a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-4.png" alt="alt text here" width="150" height="200" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px;"></a></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    <td align="left" valign="top" width="350">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:350px; vertical-align:top; width:100%;" class="wrapper">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 325px; float: right;" class="wrapper">
                                            <tr>

                                                <td style="padding: 0px 0 0 0;" class="no-padding">
                                                    <!-- ARTICLE -->
                                                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                        <tr>
                                                            <td align="left" style="padding: 0 0 5px 0; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">White Label Recharge Software</td>
                                                        </tr>
                                                        <tr>
                                                             <td align="left" style="padding: 10px 0 15px 0; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">We offer white label recharge Software under the domain name that you desire. We are coveted developer who have crafted out of the box online white label recharge Software that run on web based application.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding-bottom: 35px;">
                        <!--[if (gte mso 9)|(IE)]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                        <tr>
                        <td align="center" valign="top" width="500">
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:500;">
                            <tbody><tr>
                                <td align="center" valign="top" style="font-size:0;" dir="rtl">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                    <td align="left" valign="top" width="150">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:150px; vertical-align:top; width:100%;" dir="ltr">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                            <tbody><tr>
                                                <td valign="top"><a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-5.png" alt="alt text here" width="150" height="200" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px;"></a></td>
                                            </tr>
                                        </tbody></table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    <td align="left" valign="top" width="350">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:350px; vertical-align:top; width:100%;" dir="ltr">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 325px;">
                                            <tbody><tr>

                                                <td style="padding: 0px 0 0 0;" class="no-padding">
                                                    <!-- ARTICLE -->
                                                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                        <tbody><tr>
                                                            <td align="left" style="padding: 0 0 5px 0; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">Mobile,DTH,Bill Payment Panel</td>
                                                        </tr>
                                                        <tr>
                                                             <td align="left" style="padding: 10px 0 15px 0; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">Recharge through online websbase| Sms Base |Anroid Application base 
                                                             Create unlimited distributers and retailers.
                                                             99.99 % Service Accuracy
                                                             Best margin in entire Industry.    </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                </td>
                            </tr>
                        </tbody></table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 70px 15px 25px 15px;" class="section-padding">
            <table border="0" cellpadding="0" cellspacing="0" width="500" style="padding:0 0 20px 0;" class="responsive-table">
               
               
                <tr>
                    <td align="center" height="100%" valign="top" width="100%" style="padding-bottom: 25px;">
                        <!--[if (gte mso 9)|(IE)]>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                        <tr>
                        <td align="center" valign="top" width="500">
                        <![endif]-->
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:500;">
                            <tr>
                                <td align="center" valign="top" style="font-size:0;">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                                    <tr>
                                    <td align="left" valign="top" width="150">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:150px; vertical-align:top; width:100%;">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                            <tr>
                                                <td valign="top"><a href="http://clickecharge.com" target="_blank"><img src="/home/recharge/public_html/images/product-6.png" alt="alt text here" width="150" height="200" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px;"></a></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    <td align="left" valign="top" width="350">
                                    <![endif]-->
                                    <div style="display:inline-block; margin: 0 -2px; max-width:350px; vertical-align:top; width:100%;" class="wrapper">

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 325px; float: right;" class="wrapper">
                                            <tr>

                                                <td style="padding: 0px 0 0 0;" class="no-padding">
                                                    <!-- ARTICLE -->
                                                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                        <tr>
                                                            <td align="left" style="padding: 0 0 5px 0; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">Money Transfer Service</td>
                                                        </tr>
                                                        <tr>
                                                             <td align="left" style="padding: 10px 0 15px 0; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">We offer Money Transfer  directly to a bank account with low fees using ClickECharge\'s online services. Send the money directly where you need it today.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                    </tr>
                                    </table>
                                    <![endif]-->
                                </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 25px 15px 70px 15px;" class="section-padding">
            <table border="0" cellpadding="0" cellspacing="0" width="500" class="responsive-table">
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <!-- COPY -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding-copy">Get In Touch</td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                                                +91 98 60 647 921 <br>
                                            +91 95 18 553 443
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <!-- BULLETPROOF BUTTON -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td align="center" style="padding-top: 25px;" class="padding">
                                                <table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
                                                    <tr>
                                                        <td align="center" style="border-radius: 3px;" bgcolor="#256F9C"><a href="https://clickecharge.com" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; border-radius: 3px; padding: 15px 25px; border: 1px solid #256F9C; display: inline-block;" class="mobile-button">Learn More &rarr;</a></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px 0px;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
            <tr>
            <td align="center" valign="top" width="500">
            <![endif]-->
            <!-- UNSUBSCRIBE COPY -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="max-width: 500px;" class="responsive-table">
                <tr>
                    <td align="center" style="font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                        6, Ganesham Commercial A,
                        Pimple Saudagar,
                        Pune, Maharashtra, India
                        411027
                        info@clickecharge.com
                        <br>
                        
                        <a href="http://clickecharge.com" target="_blank" style="color: #666666; text-decoration: none;">View this email in your browser</a>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>

</table>
</body>
</html>';
	if($email!='') {
		if(phpMailerFunction($email, $subject, $message)) {
			return true;
		} else {
			return false;
		}
	}
}

?>
