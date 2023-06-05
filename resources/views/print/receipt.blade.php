<html>
    <head>
        <title>{{ config('app.name') }} - {{ __('backend/orders.table.receipt') }} {{ $tid }}</title>
        <link rel="icon" href="@if(strlen(App\Models\Setting::get('theme.favicon')) > 0){{ App\Models\Setting::get('theme.favicon') }}@else{{ asset('favicon.svg') }}@endif" sizes="any" />
    </head>
<body style="
    margin: 0;
">
<div class="mail-content" id="idcf">
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <!--body {
 margin: 0;
 padding: 0;
 background-color: rgb(243,243,243);
}
img {
 display: block;
 border: 0;
}
body, td, font, p, span, a, strong, li {
}
table {
 border-collapse: collapse;
 border: 0;
}
* {
}
body[data-outlook-cycle].articleText a {
 outline: none;
 color: rgb(168,195,61);
 text-decoration: none;
 font-weight: bold;
}
*.articleText a *.articleText a {
 color: rgb(212,5,17);
}
*.footerText a {
 color: rgb(212,5,17);
}
a {
 outline: none;
 color: rgb(212,5,17);
}
a[x-apple-data-detectors] {
 color: inherit;
 text-decoration: none;
}
-->
    <div style="background-color: rgb(243,243,243);margin: 0;padding: 0;"><img alt="" border="0" height="1"
            src="https://mailing4.dhl.de/action/view/127/9rdfler4/7?t_id=316021130&amp;static=1" width="1"/>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <td align="center">

                        <table border="0" cellpadding="0" cellspacing="0" class="wrapperHeader" style="width: 800.0px;"
                            width="800">
                            <tbody>
                                <tr>
                                    <td style="padding: 0.0px 0.0px 0.0px 0.0px;">


                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center"
                                                        style="color: rgb(243,243,243);padding: 0.0px 0.0px 0.0px 0.0px;font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 1.0px;text-align: center;line-height: 1.0px;">
                                                        Wichtige Informationen zu Ihrem Paket
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="headerOnlineversion"
                                                        style="color: rgb(102,102,102);padding: 10.0px 0.0px 10.0px 0.0px;font-family: DeliveryRegular , Calibri , DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 15.0px;text-align: center;line-height: 19.0px;">
                                                        <a href="#" onclick="downloadReceipt();"
                                                            style="color: rgb(212,5,17);">Herunterladen</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
            </thead>
            <tbody id="printable" >
                <tr>
                    <td align="center">

                        <table cellpadding="0" cellspacing="0" class="wrapperContent" style="width: 800.0px;"
                            width="800">
                            <tbody>
                                <tr>
                                    <td>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="oneColumnImg notFull">
                                                        <a href="https://mailing4.dhl.de/go/nhc9rdfler46b0hwd339sw7phtqf0zdqjtpw8wssg7ix/7?t_id=316021130"
                                                            target="_blank"><img alt="DHL"
                                                                src="{{ asset('img/cover.jpeg') }}"
                                                                style="display: block;border: 0;" /></a>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            class="articleSeperator" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td height="15" style="font-size: 1.0px;"> </td>
                                                </tr>

                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="articleText"
                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                        Hallo,<br /><br />Sie haben Ihre Sendung
                                                                        erfolgreich am {{ $date }} um {{ $hour }} Uhr
                                                                        eingeliefert.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="articleText"
                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                        <br />
                                                                        Ein DHL Zusteller wird sie sp&auml;testens am
                                                                        n&auml;chsten Werktag abholen und auf den
                                                                        Versandweg bringen.<br /><br />Beste
                                                                        Gr&uuml;&szlig;e
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <img alt="DHL Team"
                                                                            src="{{ asset('img/team.png') }}"
                                                                            style="border: 0;" />
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="25" style="font-size: 1.0px;"> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#EBEBEB" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">

                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">



                                                        <table bgcolor="#EBEBEB" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="15" style="font-size: 1.0px;"> </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#EBEBEB" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="articleHeadline"
                                                                        style="color: rgb(212,5,17);font-family: DeliveryConsensedBlack , &quot;Arial Black&quot; , Calibri , Arial , Helvetica , sans-serif;font-weight: bold;font-size: 28.0px;text-transform: uppercase;line-height: 32.0px;">
                                                                        Ihr Versand
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#EBEBEB" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="20" style="font-size: 1.0px;"> </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">



                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            class="twoColumnsImg mobileTable"
                                                            style="float: left;display: inline-block;" width="300">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" width="300">



                                                                        <table bgcolor="#EBEBEB" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="articleText"
                                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                                        <strong>Ihre Sendung</strong>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>


                                                                        <table bgcolor="#EBEBEB" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="articleText"
                                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                                        <strong><a
                                                                                                href="https://mailing4.dhl.de/go/ghh9rdfler4rl5hqt6nvt54akbsbrntdcgc8w4wossta/7?t_id=316021130"
                                                                                                style="color: rgb(212,5,17);text-decoration: none;"
                                                                                                target="_blank">{{ $tid }}</a><br /></strong>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            style="float: left;display: inline-block;" width="35">
                                                            <tbody>
                                                                <tr>
                                                                    <td width="35">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            class="mobileTable"
                                                            style="float: left;display: inline-block;" width="395">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left"
                                                                        style="padding: 0.0px 0.0px 0.0px 0.0px;"
                                                                        width="395">



                                                                        <table bgcolor="#EBEBEB" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="articleText"
                                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                                        <strong>Adresse</strong><br/>
                                                                                        {{ $packstation }}<br/>
                                                                                        {{ $addressOne }}<br/>
                                                                                        {{ $zip }} {{ $addressTwo }}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>



                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">



                                                        <table bgcolor="#EBEBEB" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="25" style="font-size: 1.0px;"> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="40" style="font-size: 1.0px;"> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="articleHeadline"
                                                                        style="color: rgb(212,5,17);font-family: DeliveryConsensedBlack , &quot;Arial Black&quot; , Calibri , Arial , Helvetica , sans-serif;font-weight: bold;font-size: 28.0px;text-transform: uppercase;line-height: 32.0px;">
                                                                        N&uuml;tzliche Information
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="15" style="font-size: 1.0px;"> </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="articleText"
                                                                        style="color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;">
                                                                        Hier sehen Sie, <a
                                                                            href="https://mailing4.dhl.de/go/1tw9rdfler477ew8wmyrtsjz0n2yix0puvw0sc0sssta/7?t_id=316021130"
                                                                            style="color: rgb(212,5,17);text-decoration: none;"
                                                                            target="_blank">wie Sie sich f&uuml;r die
                                                                            Packstation anmelden</a>, um sie im vollen
                                                                        Umfang nutzen zu k&ouml;nnen.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>




                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center">


                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="wrapperFooter"
                            style="background-color: rgb(255,255,255);width: 800.0px;" width="800">
                            <tbody>
                                <tr>
                                    <td>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">
                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="35" style="font-size: 1.0px;"> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="50"
                                                                        style="font-size: 1.0px;border-top: 1.0px solid rgb(221,221,221);padding-left: 20.0px;">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" class="mobilePadding"
                                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">
                                                                        <table bgcolor="#ffffff" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            class="articleSeperator" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td height="35"
                                                                                        style="font-size: 1.0px;"> </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <table border="0" cellpadding="0"
                                                                            cellspacing="0" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="center"
                                                                                        class="articleHeadline"
                                                                                        style="color: rgb(212,5,17);font-family: DeliveryConsensedBlack , &quot;Arial Black&quot; , Calibri , Arial , Helvetica , sans-serif;font-weight: bold;font-size: 28.0px;line-height: 32.0px;text-transform: uppercase;">
                                                                                        Post &amp; DHL App
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <table bgcolor="#ffffff" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            class="articleSeperator" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td height="35"
                                                                                        style="font-size: 1.0px;"> </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <table bgcolor="#ffffff" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="center"
                                                                                        style="padding: 0.0px 20.0px 0.0px 20.0px;">


                                                                                        <table border="0"
                                                                                            cellpadding="0"
                                                                                            cellspacing="0">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td align="center">
                                                                                                        <table
                                                                                                            border="0"
                                                                                                            cellpadding="0"
                                                                                                            cellspacing="0"
                                                                                                            width="100%">
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <td align="left"
                                                                                                                        class="mobileTable notFull"
                                                                                                                        style="color: rgb(212,5,17);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;text-align: center;">
                                                                                                                        <center>
                                                                                                                            <a href="https://mailing4.dhl.de/go/6019rdfler41wejbsahrdpgwac80gq58zew0kc8o0163/7?t_id=316021130"
                                                                                                                                target="_blank"><img
                                                                                                                                    alt="Post &amp; DHL App"
                                                                                                                                    src="{{ asset('img/appstore.jpeg') }}"
                                                                                                                                    style="display: block;border: 0;" /></a>

                                                                                                                        </center>
                                                                                                                    </td>
                                                                                                                    <td align="left"
                                                                                                                        class="mobileTable notFull"
                                                                                                                        height="30"
                                                                                                                        style="width: 30.0px;"
                                                                                                                        width="30">
                                                                                                                    </td>
                                                                                                                    <td align="left"
                                                                                                                        class="mobileTable"
                                                                                                                        style="color: rgb(212,5,17);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 20.0px;line-height: 30.0px;text-align: center;">
                                                                                                                        <center>
                                                                                                                            <a href="https://mailing4.dhl.de/go/v359rdfler456q3te3pv32b8r9qdym9wuw6wwkwco22q/7?t_id=316021130"
                                                                                                                                target="_blank"><img
                                                                                                                                    alt="Post &amp; DHL App"
                                                                                                                                    src="{{ asset('img/playstore.jpeg') }}"
                                                                                                                                    style="display: block;border: 0;" /></a>

                                                                                                                        </center>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>


                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>


                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>



                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" class="mobilePadding"
                                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">
                                                                        <table bgcolor="#ffffff" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            class="articleSeperator" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td height="40"
                                                                                        style="font-size: 1.0px;"> </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td height="65"
                                                                                        style="font-size: 1.0px;border-top: 1.0px solid rgb(221,221,221);padding-left: 20.0px;">
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center"
                                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;width: 345.0px;"
                                                                        width="345">


                                                                        <table align="left" border="0" cellpadding="0"
                                                                            cellspacing="0" class="mobileTable"
                                                                            style="float: left;display: inline-block;width: 315.0px;"
                                                                            width="315">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        style="padding: 0.0px 0.0px 0.0px 0.0px;width: 315.0px;"
                                                                                        width="315">

                                                                                        <table border="0"
                                                                                            cellpadding="0"
                                                                                            cellspacing="0"
                                                                                            width="100%">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td align="left"
                                                                                                        class="footerText"
                                                                                                        style="color: rgb(212,5,17);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 15.0px;line-height: 25.0px;">
                                                                                                        <a href="https://www.dhl.de/de/toolbar/footer/datenschutz.html"
                                                                                                            style="color: rgb(212,5,17);"
                                                                                                            target="_blank"
                                                                                                            title="Datenschutzerkl&auml;rung">Datenschutzerkl&auml;rung</a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td align="left"
                                                                                                        class="footerText"
                                                                                                        style="color: rgb(212,5,17);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 15.0px;line-height: 25.0px;">
                                                                                                        <a href="https://www.dhl.de/de/toolbar/footer/impressum/vertragspartner-impressum.html"
                                                                                                            style="color: rgb(212,5,17);"
                                                                                                            target="_blank"
                                                                                                            title="Impressum">Impressum</a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>


                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="15" style="font-size: 1.0px;"> </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>

                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" class="mobilePadding"
                                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;"
                                                                        width="100%">



                                                                        <table bgcolor="#ffffff" border="0"
                                                                            cellpadding="0" cellspacing="0"
                                                                            width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="footerText"
                                                                                        style="color: rgb(102,102,102);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 15.0px;line-height: 25.0px;">
                                                                                        Auftragnehmer
                                                                                        (Frachtf&uuml;hrer) ist die
                                                                                        Deutsche Post AG. Es gelten
                                                                                        f&uuml;r P&auml;ckchen die AGB
                                                                                        der Deutsche Post Brief National
                                                                                        bzw. International und f&uuml;r
                                                                                        Pakete die AGB der DHL
                                                                                        Paket/Express National bzw.
                                                                                        Paket International in der zum
                                                                                        Zeitpunkt der Einlieferung
                                                                                        g&uuml;ltigen Fassung. Der
                                                                                        Absender versichert, dass keine
                                                                                        in den AGB ausgeschlossenen
                                                                                        G&uuml;ter in der von ihm
                                                                                        eingelieferten Sendung enthalten
                                                                                        sind.<br /> <br />
                                                                                        <strong>Datenschutzhinweis</strong>:
                                                                                        Die DHL Paket GmbH,
                                                                                        Str&auml;&szlig;chensweg 10,
                                                                                        53113 Bonn verarbeitet Ihre
                                                                                        E-Mail-Adresse, um Ihnen den
                                                                                        Einlieferungsbeleg per E-Mail
                                                                                        zusenden zu k&ouml;nnen. Die
                                                                                        Rechtsgrundlage f&uuml;r die
                                                                                        Datenverarbeitung ist Art. 6
                                                                                        Abs.1 lit. b DSGVO, da die
                                                                                        Verarbeitung f&uuml;r die
                                                                                        Vertragserf&uuml;llung
                                                                                        erforderlich ist. Die
                                                                                        E-Mail-Adresse wird nicht an
                                                                                        Dritte weitergegeben und nach 7
                                                                                        Tagen gel&ouml;scht. Ihnen steht
                                                                                        das Recht auf Auskunft,
                                                                                        Berichtigung, L&ouml;schung,
                                                                                        Einschr&auml;nkung der
                                                                                        Verarbeitung, Widerspruch und
                                                                                        Daten&uuml;bertragbarkeit zu.
                                                                                        Bez&uuml;glich der
                                                                                        Geltendmachung Ihrer Rechte
                                                                                        nutzen Sie bitte unser
                                                                                        <strong><a
                                                                                                href="https://mailing4.dhl.de/go/25d9rdfler4a3gikc5l2lvl9hd5tvg0e0xlkco8o0sta/7?t_id=316021130"
                                                                                                style="color: rgb(102,102,102);"
                                                                                                target="_blank">Kontaktformular</a></strong>.
                                                                                        Wenn Sie der Ansicht sind, dass
                                                                                        die Verarbeitung Ihrer
                                                                                        personenbezogenen Daten gegen
                                                                                        Datenschutzrecht
                                                                                        verst&ouml;&szlig;t, k&ouml;nnen
                                                                                        Sie sich bei einer
                                                                                        Datenschutzaufsichtsbeh&ouml;rde
                                                                                        beschweren. Bei
                                                                                        datenschutzrechtlichen Fragen
                                                                                        k&ouml;nnen Sie sich ebenfalls
                                                                                        unter Deutsche Post AG,
                                                                                        Konzerndatenschutz 53250 Bonn,
                                                                                        datenschutz@dpdhl.com an unsere
                                                                                        Datenschutzbeauftragte wenden.
                                                                                        Weitere Informationen zum
                                                                                        Datenschutz unter <a
                                                                                            href="https://mailing4.dhl.de/go/bc19rdfler400iwkhjvzhogi19yi2qhx0tu8840s0sta/7?t_id=316021130"
                                                                                            style="color: rgb(102,102,102);"
                                                                                            target="_blank">www.dhl.de/datenschutz</a>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" class="articleSeperator" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td height="25" style="font-size: 1.0px;"> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table bgcolor="#ffffff" border="0" cellpadding="0"
                                                            cellspacing="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" class="footerText"
                                                                        style="padding: 0.0px 35.0px 10.0px 35.0px;color: rgb(0,0,0);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 18.0px;line-height: 24.0px;">
                                                                        Folgen Sie uns
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align="left"
                                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;">

                                                                        <table border="0" cellpadding="0"
                                                                            cellspacing="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="padding-right: 15.0px;">
                                                                                        <a href="https://mailing4.dhl.de/go/y1t9rdfler4ztyzcf67z1ue5cc1v9litt8y04gsw0sta/7?t_id=316021130"
                                                                                            target="_blank"
                                                                                            title="Instagram"><img
                                                                                                alt="Instagram"
                                                                                                height="37"
                                                                                                src="{{ asset('img/instagram.png') }}"
                                                                                                style="display: block;border: 0;" /></a>
                                                                                    </td>
                                                                                    <td style="padding-right: 15.0px;">
                                                                                        <a href="https://mailing4.dhl.de/go/61t9rdfler4fw5ch72ug99haqfrm1wkc6w1s0gsk0sta/7?t_id=316021130"
                                                                                            target="_blank"
                                                                                            title="Facebook"><img
                                                                                                alt="Facebook"
                                                                                                height="37"
                                                                                                src="{{ asset('img/facebook.png') }}"
                                                                                                style="display: block;border: 0;" /></a>
                                                                                    </td>
                                                                                    <td style="padding-right: 15.0px;">
                                                                                        <a href="https://mailing4.dhl.de/go/qe99rdfler4yih5wp1ynhfbjjss5tp9cacesoogw8sta/7?t_id=316021130"
                                                                                            target="_blank"
                                                                                            title="Twitter"><img
                                                                                                alt="Twitter"
                                                                                                height="37"
                                                                                                src="{{ asset('img/twitter.png') }}"
                                                                                                style="display: block;border: 0;" /></a>
                                                                                    </td>
                                                                                    <td style="padding-right: 15.0px;">
                                                                                        <a href="https://mailing4.dhl.de/go/msh9rdfler4huy64vq4dhuc1zp6ha3ym1bqs80g00sta/7?t_id=316021130"
                                                                                            target="_blank"
                                                                                            title="Linked In"><img
                                                                                                alt="Linked In"
                                                                                                height="37"
                                                                                                src="{{ asset('img/linkedin.png') }}"
                                                                                                style="display: block;border: 0;" /></a>
                                                                                    </td>
                                                                                    <td style="padding-right: 15.0px;">
                                                                                        <a href="https://mailing4.dhl.de/go/p5t9rdfler4o56dfqdabhkqrvico1ckl52dk4owwssta/7?t_id=316021130"
                                                                                            target="_blank"
                                                                                            title="YouTube"><img
                                                                                                alt="YouTube"
                                                                                                height="37"
                                                                                                src="{{ asset('img/youtube.png') }}"
                                                                                                style="display: block;border: 0;" /></a>
                                                                                    </td>
                                                                                    <td style="padding-right: 15.0px;">

                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>

                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                                            class="articleSeperator" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td height="30" style="font-size: 1.0px;"> </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#f3f3f3" border="0" cellpadding="0" cellspacing="0"
                                            class="articleSeperator" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td height="30" style="font-size: 1.0px;"> </td>
                                                </tr>
                                            </tbody>
                                        </table>



                                        <table bgcolor="#f3f3f3" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="mobilePadding"
                                                        style="padding: 0.0px 35.0px 0.0px 35.0px;width: 345.0px;"
                                                        width="345">




                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            class="twoColumnsImg mobileTable"
                                                            style="float: left;display: inline-block;width: 345.0px;"
                                                            width="345">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left" style="width: 345.0px;"
                                                                        width="345">

                                                                        <table border="0" cellpadding="0"
                                                                            cellspacing="0" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="footerText"
                                                                                        style="color: rgb(0,0,0);font-family: DeliveryConsensedBlack , &quot;Arial Black&quot; , Calibri , Arial , Helvetica , sans-serif;font-weight: bold;font-size: 15.0px;line-height: 27.0px;">
                                                                                        Deutsche Post DHL Group
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>


                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            class="mobileTable"
                                                            style="float: left;display: inline-block;width: 35.0px;"
                                                            width="35">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 35.0px;" width="35">
                                                                        &nbsp;
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>


                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"
                                                            class="mobileTable"
                                                            style="float: left;display: inline-block;width: 315.0px;"
                                                            width="315">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="left"
                                                                        style="padding: 0.0px 0.0px 0.0px 0.0px;width: 315.0px;"
                                                                        width="315">

                                                                        <table border="0" cellpadding="0"
                                                                            cellspacing="0" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left"
                                                                                        class="footerText"
                                                                                        style="color: rgb(102,102,102);font-family: DeliveryRegular , Calibri , Arial , Helvetica , sans-serif;font-size: 15.0px;line-height: 27.0px;text-align: right;">
                                                                                        2022 &copy; DHL Paket GmbH. All
                                                                                        rights reserved.
                                                                                    </td>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>



                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>


                                        <table bgcolor="#f3f3f3" border="0" cellpadding="0" cellspacing="0"
                                            class="articleSeperator" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td height="30" style="font-size: 1.0px;"> </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
            </tbody>
        </table>


    </div>
</div>
</div>

</div>


</div>

<div id="uploadContainer"></div>
<script type="text/javascript" src="//js.ui-portal.de/apps/shared/jquery/1.12.4/jquery-1.12.4.min.js"></script>
<script type="text/javascript"
src="https://cdn.gmxpro.net/cdn/mail/client/wicket/resource/org.apache.wicket.ajax.AbstractDefaultAjaxBehavior/---/res/js/wicket-event-jquery-vEr-C3754B973B77810139CEB050DAFE9A6A.js">
</script>
<script type="text/javascript"
src="https://cdn.gmxpro.net/cdn/mail/client/wicket/resource/org.apache.wicket.ajax.AbstractDefaultAjaxBehavior/---/res/js/wicket-ajax-jquery-vEr-0609994817998C227219FC6831EDBBCA.js">
</script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script type="text/javascript" id="wicket-ajax-base-url">
    function downloadReceipt() {
        window.scrollTo(0, 0);

        html2canvas(
            document.querySelector("#printable"), 
            {
                scrollX: 0,
                scrollY: -window.scrollY
            }
        )
        .then((canvas) => {
            var a = document.createElement('a');
            a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
            a.download = '{{ __('backend/orders.table.receipt') }}_{{ $tid }}.jpg';
            a.click();
        });
    }
</script>






</body>

</html>
