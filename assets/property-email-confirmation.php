<?php

namespace assets;

$message = '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width">
    <!-- Forcing initial-scale shouldn\'t be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">
    <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title>
    <!-- The title tag shows in email notifications, like Android 4.4. -->


    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i" rel="stylesheet">

    <!-- CSS Reset : BEGIN -->
    <style>
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }
        
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        /* What it does: Uses a better rendering method when resizing images in IE. */
        
        img {
            -ms-interpolation-mode: bicubic;
        }
        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        
        a {
            text-decoration: none;
        }
        /* What it does: A work-around for email clients meddling in triggered links. */
        
        *[x-apple-data-detectors],
        /* iOS */
        
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
        /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        
        .im {
            color: inherit !important;
        }
        /* If the above doesn\'t work, add a .g-img class to any image in question. */
        
        img.g-img+div {
            display: none !important;
        }
        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you\'d like to fix */
        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u~div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u~div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        
        @media only screen and (min-device-width: 414px) {
            u~div .email-container {
                min-width: 414px !important;
            }
        }
    </style>

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>
        .primary {
            background: #f3a333;
        }
        
        .bg_white {
            background: #ffffff;
        }
        
        .bg_light {
            background: #fafafa;
        }
        
        .bg_black {
            background: #000000;
        }
        
        .bg_dark {
            background: rgba(0, 0, 0, .8);
        }
        
        .email-section {
            padding: 2.5em;
        }
        
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: \'Playfair Display\', serif;
 color: #000000;
            margin-top: 0;
        }
        
        a {
            color: #f3a333;
        }
        /*LOGO*/
        
        .logo h1 {
            margin: 0;
        }
        
        .logo h1 a {
            color: #000;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: \'Montserrat\', sans-serif;

        }
        /*HERO*/
        
        .hero {
            position: relative;
        }
        
        .hero .text {
            color: #00acff;
            font-weight: bolder;
        }
        
        .hero .text h2 {
            color: black;
            font-size: 30px;
            margin-bottom: 0;
            background-color: white;
        }
        
        .homey {
            color: #16fc00;
        }
        /*HEADING SECTION*/
        
        .heading-section h2 {
            color: #000000;
            font-size: 28px;
            margin-top: 0;
            line-height: 1.4;
        }
        
        .heading-section .subheading {
            margin-bottom: 20px !important;
            display: inline-block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(0, 0, 0, .4);
            position: relative;
        }
        
        .heading-section .subheading::after {
            position: absolute;
            left: 0;
            right: 0;
            bottom: -10px;
            content: \'\';
 width: 100%;
            height: 2px;
            background: #f3a333;
            margin: 0 auto;
        }
        
        .heading-section-white {
            color: rgba(255, 255, 255, .8);
        }
        
        .heading-section-white h2 {
            font-size: 28px;
            line-height: 1;
            padding-bottom: 0;
        }
        
        .heading-section-white h2 {
            color: #ffffff;
        }
        
        .heading-section-white .subheading {
            margin-bottom: 0;
            display: inline-block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, .4);
        }
        
        .icon {
            text-align: center;
        }
        
        @media screen and (max-width: 500px) {
            .icon {
                text-align: left;
            }
            .text-services {
                padding-left: 0;
                padding-right: 20px;
                text-align: left;
            }
        }
    </style>


</head>

<body width="100%" style="margin: 0; padding: 0 !important;background-color: #222222;">

    <div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; font-family: sans-serif;">
    </div>


    <div style="max-width: 600px; margin: 0 auto;" class="email-container">
        <!-- BEGIN BODY -->
        <table role="presentation" cellspacing="0" cellpadding="0" width="100%" style="margin: auto;">
            <tr>
                <td class="bg_white logo" style="padding: 1em 2.5em; text-align: center">
                    <h1><a href="#">Homey.lk</a></h1>
                </td>
            </tr>
            <!-- end tr -->
            <tr>
                <td valign="middle" class="hero" style="background-image: url(images/bg_5.jpg); background-size: cover; height: 175px;">
                    <table>
                        <tr>
                            <td>
                                <div class="text" style="padding: 0 3em; text-align: center;">
                                    <h2>
                                        Everyone Deserves the Opportunity of a Home. it\'s just a touch away.</h2>
                                    <p><a href="#" class="btn btn-primary">Click Here!</a></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- end tr -->

            <tr>
                <td class="title-txt">
                    <div style="padding-top:20px;padding-bottom:20px;">
                        <h1 style="text-align: center;color: #00acff;">
                            Your Advertisement Is approved
                        </h1>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="property-details" style="display: flex;justify-content: center;color: #0d9b00; 
                font-family: \'Lucida Sans\', \'Lucida Sans Regular\', \'Lucida Grande\', \'Lucida Sans Unicode\', Geneva, Verdana, sans-serif;">
                    <div class="details" style="display: flex;flex-direction: column;justify-content: center;
                    font-size: larger;padding-bottom: 10px;">
                        <div class="column" style="padding-bottom: 1em;">Property Title : Nice House in matara</div>
                        <div class="column" style="padding-bottom: 1em;">Property Id : 12345</div>
                        <div class="column" style="padding-bottom: 1em;">Boosted : Yes</div>
                        <div class="column" style="padding-bottom: 1em;">Shedule Date : dd/mm/yyyy </div>
                        <div class="column" style="padding-bottom: 1em;">Shedule Time : hh.mm</div>
                        <div class="column" style="padding-bottom: 1em;">Sharing : No</div>
                        <div class="column" style="padding-bottom: 1em;">Individuals : No</div>
                        <div class="column" style="padding-bottom: 1em;">Online Paymenys acceptd : Yes</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="button-redirect">
                    <div class="button" style="display: flex; justify-content: center;">
                        <button class="my-btn" style="background-color:#44c767;border-radius:15px;border:3px solid #18ab29;display:inline-block;cursor:pointer;color:#ffffff;
                        font-family:Verdana;font-size:17px;font-weight:bold;padding:16px 20px;text-decoration:none;">
                            <a href="#" style="color: #000000;">Redirect to Property  </a>
                        </button>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="bg_white">
                    <table role="presentation" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td class="bg_dark email-section" style="text-align:center;">
                                <div class="heading-section heading-section-white">
                                    <span class="subheading">Welcome</span>
                                    <h2>Welcome To <span class="homey">Homey.lk<span></h2>
		              	<p>Copyright © | Homey.lk | 2021</p>
                    <p>You received this email because we like to keep Homey.lk Partners (and future ones!) up to speed on what\'s going on with</p>
		            	</div>
		            </td>
		          </tr><!-- end: tr -->
		         
		        </table>

		      </td>
		    </tr><!-- end:tr -->
      <!-- 1 Column Text + Button : END -->
      </table>

    </div>
</body>
</html>';
