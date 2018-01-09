<?php
//master config
$masterclientid='656341090161-r9bq8v786uvegpj9v2k1gfk5u0ofdoav.apps.googleusercontent.com';
$masterclientsecret='j0p8fh6fH-tuwRowHsHpNS3y';
//end of master config

$email='';
$cookie_name='Firmwares4u-Auth';

$host='suriaraj.ipagemysql.com';
$user='firmwares4uadmin';
$pass='firmwares4u_pass';
$db='firmwares4u_unlock';
$con=mysql_connect($host,$user,$pass);
if(!$con)
{
die('cannot connect to database host');
}
$dblink=mysql_select_db($db,$con);
if(!$dblink)
{
die('cannot connect to database host');
}
?>
<?php
if(isset($_GET['logout'])&&$_GET['logout']==1)
{
setcookie($cookie_name, "", time() - 3600, "/");
header('Location: index.php');
exit;
}
?>
<?php
//get code from redirect url
if(isset($_GET['code'])&&$_GET['code']!='')
{
$code=$_GET['code'];

//exchange for access token using this code
/*
POST /o/oauth2/token HTTP/1.1
Host: accounts.google.com
Content-length: 265
content-type: application/x-www-form-urlencoded
user-agent: google-oauth-playground
code=4%2FaqailpUbweCcNtm8CG_VAZx33vjhvuiDc9IDSlq5dBE.IgFaLi3fkOEWcp7tdiljKKblx-EAlQI&redirect_uri=https%3A%2F%2Fdevelopers.google.com%2Foauthplayground&client_id=407408718192.apps.googleusercontent.com&scope=&client_secret=************&grant_type=authorization_code
*/

$redirecturl="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
$postfields = array('code'=>$code, 'redirect_uri'=>$redirecturl, 'client_id'=>$masterclientid, 'client_secret'=>$masterclientsecret, 'grant_type'=>'authorization_code');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);
// Edit: prior variable $postFields should be $postfields;
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // On dev server only!
$result = curl_exec($ch);
curl_close($ch);
$data=json_decode($result);
$accesstoken=$data->access_token;
/*
echo $accesstoken;
exit;
*/

//now get the email address and do our operations
/*
GET /plus/v1/people/me HTTP/1.1
Host: www.googleapis.com
Content-length: 0
Authorization: Bearer ya29.5wB6QyshaZ-6ibZDciwOXrs4pQ6SSrOu16y3SD1Nt8qmTdnEnucdNS1tn1oIEgVORJpz-LGaOF-Hfw
*/

$header = array();
$header[] = 'Content-length: 0';
$header[] = "Authorization: Bearer ".$accesstoken;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/plus/v1/people/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // On dev server only!
$result = curl_exec($ch);
curl_close($ch);
$data1=json_decode($result);
$emails=$data1->emails;
$email=$emails[0]->value;
if($email!='')
{
	setcookie($cookie_name, md5(trim($email)), time() + (86400 * 30), "/");
	//insert into db
	$query="INSERT IGNORE INTO `email` SET `emailhash` = '".md5(trim($email))."', `emailid` = '".$email."';";
	$result=mysql_query($query);
	header('Location: index.php');
	exit;
}
}
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>
    Firmwares4u | Unlock Codes
    </title>
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://raw.githubusercontent.com/noizwaves/bootstrap-social-buttons/v1.0.0/social-buttons-3.css" rel="stylesheet">
  <style class="custom-css">
#jumbo {
  background-color: #333;
  color: #eee;
}
#jumbo p {
  font-size: 16px;
}
#try-header {
  margin: 30px 0px;
}
#try-more {
  margin: 30px 0px;
  font-style: italic;
}
</style>
<script src="jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="social-locker.js"></script>
<script type="text/javascript">
    jQuery.noConflict();                    
    jQuery(document).ready(function ($) {
        $("#lock-my-div").sociallocker({
            text: {
                header: "The content is locked!", // replace content with this heading
                message: "Please support us, click like button below to view the unlock codes." // hidden content message
            },

            theme: "starter", // Theme

            locker: {
                close: false,
                timer: 0
            },

            buttons: {   // Buttons you want to show on box
                order: ["facebook-like", "twitter-tweet", "twitter-follow", "google-plus", "linkedin-share"] 
            },

            facebook: {  
                appId: "2068418453432508",
                like: {
                    title: "Like us",
                    url: "https://www.facebook.com/firmwares4u" // link to like in Facebook button
                }
            },

            twitter: {
                tweet: {
                    title: "Tweet", 
                    text: "All in one free unlock solution.", // tweet text
                    url: "http://www.firmwares4u.in/" //tweet link
                },
                follow: {
                    title: "Follow us", 
                    url: "http://twitter.com/firmwares4u" // Twitter profile to follow 
                }
            },

            google: {                                
                plus: {
                    title: "Plus +1",
                    url: "http://www.firmwares4u.in/" // Google plus link for +1
                }
            },

            linkedin: {
                url: "http://www.firmwares4u.in/",      // LinkedIn url to share 
                share: {
                    title: "Share"
                }
            }
        });
    });
</script>
<style type="text/css">

    .jo-sociallocker.jo-sociallocker-msie {
        background-color: hsl(200, 65%, 91%);
        border-color: hsl(190, 65%, 84%);
        color: hsl(200, 50%, 45%);
    }
    .jo-sociallocker {
        background-color: hsl(50, 81%, 94%);
        border: 1px solid hsl(39, 83%, 91%);
        -moz-border-radius: 4px 4px 4px 4px;
        -webkit-border-radius: 4px 4px 4px 4px;
        border-radius: 4px 4px 4px 4px;
        margin-bottom: 20px;
        padding: 8px 35px 8px 14px;
        -moz-text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        -webkit-text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        text-shadow: 0 1px 0 hsla(0, 100%, 100%, 0.5);
        margin-left: auto;
        margin-right: auto;
    }
    .jo-sociallocker-button{
        float: left;
        margin-left: 10px;
    }
    .jo-sociallocker-after-text{
        margin-bottom: 20px;
    }
    .jo-sociallocker-buttons{
        height:35px;
    }
    .jo-sociallocker-strong{
        font-size: 30px;
        color: hsl(0, 0%, 0%);
    }
</style>
<meta name="google-site-verification" content="M80j8KCsmu98p8M67EnxHCj_q1PzXBLvekIflwShOaE" />
</head>
  
  <body>
    <div id="jumbo" class="jumbotron">
      <div class="container">
        <h1>
          Welcome to Firmwares4u
        </h1>
        <p>
          Here you can generate unlock codes for most dongles made from huawei.
        </p>
        <p>
          Have fun unlocking your device. Drop your feedback so that we can add
          more to this.
        </p>
      </div>
    </div>
    <div class="container">
    
    
      <div class="row">
        <div class="col-md3 col-md-3" style="display: block;">
        <div class="well well-sm">
          <h3 class="text-center">
            3 Easy Steps
          </h3>
          <p>
            <ol>
            	<li>Login using <i class="fa fa-google-plus"></i> account</li>
            	<li>Enter your dongle's IMEI</li>
            	<li>Click<b> <em>Calculate Unlock Codes</em> button to get your codes.<b/></li>
            </ol>
          </p>
          <hr/>
          <h3 class="text-center">Donate us!!</h3>
          <br/>
          <div class="text-center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TZD6QS86W3WR4">
<table>
<tr><td><input type="hidden" name="on0" value="Huawei Free Unlock Code">Huawei Free Unlock Code</td></tr><tr><td><select name="os0">
	<option value="Very Small Help">Very Small Help $10.00 USD</option>
	<option value="Small Help">Small Help $50.00 USD</option>
	<option value="Big Help">Big Help $100.00 USD</option>
	<option value="Very Big Help">Very Big Help $500.00 USD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="http://suriaraj.ipage.com/unlockcodes/donate.png" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="http://suriaraj.ipage.com/unlockcodes/donate.png" width="1" height="1">
</form>
        </div>
<!-- Place this tag in your head or just before your close body tag. -->
<script src="https://apis.google.com/js/platform.js" async defer></script>

<!-- Place this tag where you want the widget to render. -->
<div class="g-page" data-width="239" data-href="//plus.google.com/u/0/101541099370130968543" data-rel="publisher"></div>
        </div><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- unlockcodes -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-8203584604754752"
     data-ad-slot="1359831020"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
        </div>
      
        <div class="col-md3 col-md5 col-md-5" style="display: block;">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- unlockcodes -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-8203584604754752"
     data-ad-slot="1359831020"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
          <h3 class="text-center">
            Huawei Modem<br/>Free<br/>Old Algo / New Algo / V3(v201)<br/> Unlock Code Calculator
          </h3>
<?php
$redirecturl="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
if(!isset($_COOKIE[$cookie_name]))
{
echo '          <div class="text-center"><br/>
          <a href="https://accounts.google.com/o/oauth2/auth?redirect_uri='.$redirecturl.'&response_type=code&client_id='.$masterclientid.'&scope=email+profile+https://www.googleapis.com/auth/plus.me" class="btn btn-lg btn-google-plus"><i class="fa fa-google-plus"></i> | Connect with Google+</a>
          </div>';
}
else
{
$limit=10;
//get the current count for this email
$cookievalue=$_COOKIE[$cookie_name];

error_reporting(0);
if(isset($_POST["imei"]))
{
	$output= '			<hr/>';
	$imei = $_POST["imei"];    
        $py=("python calc.py ".$imei);
	$output = shell_exec($py);	
         //increase the counts in the db for this email
        $query="UPDATE `email` SET `count` = `count`+1 where `emailhash` = '".$cookievalue."';";
	$result=mysql_query($query);
	$query="INSERT INTO `imei` (`imeino`) VALUES ('".$imei."');";
	$result=mysql_query($query);
}

$query="SELECT `count` FROM `email` WHERE `emailhash`='".$cookievalue."' LIMIT 1;";
$result=mysql_query($query);
while($row=mysql_fetch_array($result))
{
$count=$row['count'];
}
echo '<div class="alert alert-success text-center" role="alert">Available Credits:'.($limit-$count).' | <a href="'.$redirecturl.'?logout=1">Logout</a></div>';
if(($limit-$count)>0)
{
echo '			<form action="index.php" method="post">
				<div class="form-group">
				<label>
				*IMEI: (e.g : 3xxxxx / 8xxxxx )
				</label>
	<input type="text" class="form-control" pattern="[0-9]{15}" name="imei" maxlength="15" placeholder="Enter your 15 digit IMEI here" title="imei">
				</div>
				<div class="form-group">
				<button type="submit" class="btn btn-primary form-control">
				Calculate Unlock Codes
				</button>
				</div>

			</form>';
}
else
{
	echo '<p class="text-center">All your credits are exhausted, if you need more, please buy CREDITS, COMING SOON!!!</p>';
}
if($output!='')
{
echo '<div id="lock-my-div" style="display: none;">';
echo $output;
echo '</div>';
}
}
?>
        </div>
        <div class="col-md4 col-md-4" style="display: block;">
        <div class="well well-sm text-center">


	        <h3>Last 10 IMEIs unlocked</h3>
	        <hr/>
<p>
<?php
$query="SELECT `imeino` FROM `imei`  ORDER BY `id`  DESC LIMIT 10";
$result=mysql_query($query);
while($row=mysql_fetch_array($result))
{
$tempimei=$row['imeino'];
if(strlen($tempimei)>=10)
{
$tempimei=substr($tempimei,0,-5).'*****';
}
else
{
$tempimei.='*****';
}
echo '<em>'.$tempimei.'</em><br/><br/>';
}
?>
</p>	      
          </div>
        </div>
        
      </div>

<div class="row">
<hr/>
<h3 class="text-center">Supported Models</h3>
        <div class="col-md4 col-md-4" style="display: block;">
        <div class="well well-sm text-center">
<p>
Huawei B115<br/>
Huawei B153<br/>
Huawei B183<br/>
Huawei B200<br/>
Huawei B220<br/>
Huawei B260<br/>
Huawei B560 3G router<br/>
Huawei B593<br/>
Huawei B660<br/>
Huawei B681<br/>
Huawei B683<br/>
Huawei B686<br/>
Huawei B932<br/>
Huawei B933<br/>
Huawei E1152<br/>
Huawei E122<br/>
Huawei E150<br/>
Huawei E150 (beeline)<br/>
Huawei E153<br/>
Huawei E153 Airtel Kenya<br/>
Huawei E153 Dialog Sri Lanka<br/>
Huawei E153 Globe<br/>
Huawei E153 India IDEA<br/>
Huawei E153 Mobinil<br/>
Huawei E153 MTN Sudan<br/>
Huawei E153 MTS Uzbekistan<br/>
Huawei E153 Philippines SmartBro<br/>
Huawei E153 Qcell Gambia<br/>
Huawei E153 SUN<br/>
Huawei E153 Telcel Mexico<br/>
Huawei E153 Tigo Tanzania<br/>
Huawei E153Eu-1<br/>
Huawei E153u1 Egypt Etisalat<br/>
Huawei E1550<br/>
Huawei E1550 India IDEA<br/>
Huawei E1550 Kyivstar<br/>
Huawei E1550 Meditel<br/>
Huawei E1550 MTS (MTC)<br/>
Huawei E1550 Viettel<br/>
Huawei E1552<br/>
Huawei E1553<br/>
Huawei E1556<br/>
Huawei E1556 Mexico Telcom<br/>
Huawei E156 / E156B / E156C / E156G<br/>
Huawei E156B Telcel Mexico<br/>
Huawei E157<br/>
Huawei E158<br/>
Huawei E160 / E160G / E160X<br/>
Huawei E160 Beeline Russia<br/>
Huawei E161<br/>
Huawei E1612<br/>
Huawei E1630<br/>
Huawei E166 / E166G<br/>
Huawei E169 / E169G<br/>
Huawei E170<br/>
Huawei E171<br/>
Huawei E171 Beeline<br/>
Huawei E171 MTS Russia<br/>
Huawei E172<br/>
Huawei E173<br/>
Huawei E173 Airtel Nigeria<br/>
Huawei E173 Airtel Uganda<br/>
Huawei E173 Beeline Uzbekistan<br/>
Huawei E173 Dialog Sri Lanka<br/>
Huawei E173 Idea India<br/>
Huawei E173 Megafon Russia<br/>
Huawei E173 Metfone Combodia<br/>
Huawei E173 Mobiphone<br/>
Huawei E173 MTS Uzbekistan<br/>
Huawei E173 Safaricom<br/>
Huawei E173 SmartBro Philippines<br/>
Huawei E173 Sudan MTN<br/>
Huawei E173 Tanzania Airtel<br/>
Huawei E173 Tigo Tanzania<br/>
Huawei E173 Unitel Laos<br/>
Huawei E173 Vietnamobile<br/>
Huawei E1731<br/>
Huawei E1732<br/>
Huawei E1732 India IDEA<br/>
Huawei E173Bu-1/E1731 India Airtel<br/>
Huawei E173Eu-1<br/>
Huawei E1750<br/>
Huawei E1750 Safaricom<br/>
Huawei E1750 Viettel<br/>
Huawei E1750c Aircel India<br/>
</p>
</div>
</div>
        <div class="col-md4 col-md-4" style="display: block;">
        <div class="well well-sm text-center">
<p>
Huawei E1752<br/>
Huawei E1756<br/>
Huawei E176<br/>
Huawei E1762<br/>
Huawei E177<br/>
Huawei E177 Sudan Zain<br/>
Huawei E1780<br/>
Huawei E1786<br/>
Huawei E180<br/>
Huawei E181<br/>
Huawei E1820<br/>
Huawei E182E<br/>
Huawei E1831<br/>
Huawei E188<br/>
Huawei E188 Optus Australia<br/>
Huawei E192<br/>
Huawei E2010 football<br/>
Huawei E219<br/>
Huawei E220<br/>
Huawei E226<br/>
Huawei E226 Telcel<br/>
Huawei E230<br/>
Huawei E261<br/>
Huawei E270<br/>
Huawei E271<br/>
Huawei E272<br/>
Huawei E303 Sudani Sudan<br/>
Huawei E303/E303 Hilink<br/>
Huawei E303H-1h Tigo<br/>
Huawei E3121<br/>
Huawei E3131<br/>
Huawei E3231 HiLink<br/>
Huawei E3251 Hilink<br/>
Huawei E3256 HiLink<br/>
Huawei E3272<br/>
Huawei E3272 Airtel<br/>
Huawei E3276<br/>
Huawei E3276 Airtel<br/>
Huawei E3331s<br/>
Huawei E3351<br/>
Huawei E3372<br/>
Huawei E352<br/>
Huawei E353/E353 Hilink<br/>
Huawei E3531<br/>
Huawei E3533<br/>
Huawei E355<br/>
Huawei E357<br/>
Huawei E359<br/>
Huawei E366<br/>
Huawei E367<br/>
Huawei E368<br/>
Huawei E372<br/>
Huawei E392<br/>
Huawei E397<br/>
Huawei E398<br/>
Huawei E510 TV<br/>
Huawei E5151s<br/>
Huawei E5170<br/>
Huawei E5172<br/>
Huawei E5220<br/>
Huawei E5221<br/>
Huawei E5251<br/>
Huawei E5330<br/>
Huawei E5331<br/>
Huawei E5332<br/>
Huawei E5336<br/>
Huawei E5356<br/>
Huawei E5372<br/>
Huawei E5372 Bolt<br/>
Huawei E5372 Mobily<br/>
Huawei E5372 STC<br/>
Huawei E5372 Zain<br/>
Huawei E5373<br/>
Huawei E5375<br/>
Huawei E560<br/>
Huawei E5756<br/>
Huawei E5776s<br/>
Huawei E5830/E583x/E5836/E5837 and others<br/>
Huawei E585<br/>
Huawei E585U-82<br/>
Huawei E586<br/>
Huawei E587 <br/>
Huawei E5878<br/>
Huawei E589<br/>
Huawei E618<br/>
</p>
</div>
</div>
        <div class="col-md4 col-md-4" style="display: block;">
        <div class="well well-sm text-center">
<p>
Huawei E620<br/>
Huawei E630<br/>
Huawei E630+<br/>
Huawei E660<br/>
Huawei E800<br/>
Huawei E8278<br/>
Huawei E870<br/>
Huawei E880<br/>
Huawei E881e<br/>
Huawei E960 / E968<br/>
Huawei E970/B970 router<br/>
Huawei EC122<br/>
Huawei EC1260<br/>
Huawei EC156<br/>
Huawei EC167<br/>
Huawei EC168<br/>
Huawei EC169<br/>
Huawei EC226<br/>
Huawei EC367<br/>
Huawei EG162 / E162G<br/>
Huawei EG602 / EG602G<br/>
Huawei EM770 Module<br/>
Huawei EM775<br/>
Huawei EM820u<br/>
Huawei EM920<br/>
Huawei EM930<br/>
Huawei Emobile AP02HW<br/>
Huawei Emobile D01HW<br/>
Huawei Emobile D02HW<br/>
Huawei Emobile D03HW<br/>
Huawei Emobile D12HW<br/>
Huawei Emobile D21HW<br/>
Huawei Emobile D22HW<br/>
Huawei Emobile D23HW<br/>
Huawei Emobile D24HW<br/>
Huawei Emobile D26HW<br/>
Huawei Emobile D31HW<br/>
Huawei Emobile D32HW<br/>
Huawei HiMini E369<br/>
Huawei HW-01C<br/>
Huawei Megafon M100-4<br/>
Huawei Megafon M21-1<br/>
Huawei MomoDesign MD-@ HSUPA<br/>
Huawei MTS 320D<br/>
Huawei MTS 320S<br/>
Huawei MTS 321S<br/>
Huawei MTS 420D<br/>
Huawei MTS 420S<br/>
Huawei MTS 424D<br/>
Huawei MTS 823F<br/>
Huawei SoftBank 005HW<br/>
Huawei SoftBank AP 01HW<br/>
Huawei Speedport HSPA<br/>
Huawei T353<br/>
Huawei UMG1691<br/>
Huawei UMG181<br/>
Huawei UMG1831<br/>
Huawei UMG366 (T-Mobile JetTM 2.0)<br/>
Huawei UML397<br/>
Huawei Vodafone E3735<br/>
Huawei Vodafone K2540<br/>
Huawei Vodafone K3520<br/>
Huawei Vodafone K3565<br/>
Huawei Vodafone K3715<br/>
Huawei Vodafone K3765<br/>
Huawei Vodafone K3765 Vodafone Egypt<br/>
Huawei Vodafone K3770<br/>
Huawei Vodafone K3771<br/>
Huawei Vodafone K3772<br/>
Huawei Vodafone K3773<br/>
Huawei Vodafone K3806<br/>
Huawei Vodafone K4201<br/>
Huawei Vodafone K4203<br/>
Huawei Vodafone K4505<br/>
Huawei Vodafone K4510<br/>
Huawei Vodafone K4511<br/>
Huawei Vodafone K4605<br/>
Huawei Vodafone K5005<br/>
Huawei Vodafone K5150<br/>
Huawei Vodafone R201<br/>
Huawei Vodafone R205<br/>
Huawei Vodafone R206<br/>
Huawei Vodafone R207<br/>
Huawei Vodafone R208<br/>
Huawei Vodafone R210<br/>
</p>
</div>
</div>
</div>
    </div>
  <script type="text/javascript">
var infolinks_pid = 1572605;
var infolinks_wsid = 3;
</script>
<script type="text/javascript" src="//resources.infolinks.com/js/infolinks_main.js"></script>
/**Adbloock detect start */
<script type="text/javascript"  charset="utf-8">
// Place this code snippet near the footer of your page before the close of the /body tag
// LEGAL NOTICE: The content of this website and all associated program code are protected under the Digital Millennium Copyright Act. Intentionally circumventing this code may constitute a violation of the DMCA.
                            
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}(';q N=\'\',29=\'1U\';1R(q i=0;i<12;i++)N+=29.X(B.M(B.J()*29.F));q 2e=31,2A=4e,2D=4f,2C=4g,2s=D(t){q o=!1,i=D(){z(k.1i){k.2K(\'2R\',e);E.2K(\'1T\',e)}O{k.2M(\'2U\',e);E.2M(\'1Z\',e)}},e=D(){z(!o&&(k.1i||4h.2E===\'1T\'||k.2P===\'2Q\')){o=!0;i();t()}};z(k.2P===\'2Q\'){t()}O z(k.1i){k.1i(\'2R\',e);E.1i(\'1T\',e)}O{k.2T(\'2U\',e);E.2T(\'1Z\',e);q n=!1;2H{n=E.4j==4d&&k.26}2W(a){};z(n&&n.36){(D d(){z(o)H;2H{n.36(\'16\')}2W(e){H 4k(d,50)};o=!0;i();t()})()}}};E[\'\'+N+\'\']=(D(){q t={t$:\'1U+/=\',4m:D(e){q d=\'\',r,n,o,c,s,l,i,a=0;e=t.e$(e);1e(a<e.F){r=e.14(a++);n=e.14(a++);o=e.14(a++);c=r>>2;s=(r&3)<<4|n>>4;l=(n&15)<<2|o>>6;i=o&63;z(34(n)){l=i=64}O z(34(o)){i=64};d=d+U.t$.X(c)+U.t$.X(s)+U.t$.X(l)+U.t$.X(i)};H d},13:D(e){q n=\'\',r,l,c,s,a,i,d,o=0;e=e.1s(/[^A-4n-4o-9\\+\\/\\=]/g,\'\');1e(o<e.F){s=U.t$.1L(e.X(o++));a=U.t$.1L(e.X(o++));i=U.t$.1L(e.X(o++));d=U.t$.1L(e.X(o++));r=s<<2|a>>4;l=(a&15)<<4|i>>2;c=(i&3)<<6|d;n=n+P.T(r);z(i!=64){n=n+P.T(l)};z(d!=64){n=n+P.T(c)}};n=t.n$(n);H n},e$:D(t){t=t.1s(/;/g,\';\');q n=\'\';1R(q o=0;o<t.F;o++){q e=t.14(o);z(e<1t){n+=P.T(e)}O z(e>4p&&e<4q){n+=P.T(e>>6|4r);n+=P.T(e&63|1t)}O{n+=P.T(e>>12|2h);n+=P.T(e>>6&63|1t);n+=P.T(e&63|1t)}};H n},n$:D(t){q o=\'\',e=0,n=4l=1w=0;1e(e<t.F){n=t.14(e);z(n<1t){o+=P.T(n);e++}O z(n>4b&&n<2h){1w=t.14(e+1);o+=P.T((n&31)<<6|1w&63);e+=2}O{1w=t.14(e+1);2k=t.14(e+2);o+=P.T((n&15)<<12|(1w&63)<<6|2k&63);e+=3}};H o}};q r=[\'3W==\',\'4a\',\'49=\',\'48\',\'47\',\'46=\',\'45=\',\'44=\',\'43\',\'42\',\'41=\',\'40=\',\'3Z\',\'3Y\',\'3X=\',\'4s\',\'4c=\',\'4t=\',\'4L=\',\'4N=\',\'4O=\',\'4P=\',\'4Q==\',\'4R==\',\'4S==\',\'4M==\',\'4T=\',\'4V\',\'4W\',\'4X\',\'4Y\',\'4Z\',\'51\',\'4U==\',\'4K=\',\'4v=\',\'4J=\',\'3U==\',\'4H=\',\'4G\',\'4F=\',\'4E=\',\'4D==\',\'4C=\',\'4B==\',\'4A==\',\'4z=\',\'4y=\',\'4x\',\'4w==\',\'4u==\',\'3V\',\'3T==\',\'38=\'],f=B.M(B.J()*r.F),Z=t.13(r[f]),g=Z,C=1,v=\'#3n\',a=\'#3g\',w=\'#3e\',W=\'#3m\',Y=\'\',b=\'3h!\',p=\'3d 3k 37 3l\\\'3j 3f 3c 2t 2q. 3b\\\'s 3a.  39 3o\\\'t?\',y=\'3i 3q 3F-3S, 3R 3Q\\\'t 3P 3O U 3N 3M.\',s=\'I 3L, I 3K 3J 3I 2t 2q.  3H 3G 3E!\',o=0,u=0,n=\'3r.3D\',l=0,Q=e()+\'.2n\';D h(t){z(t)t=t.1Q(t.F-15);q o=k.2N(\'3C\');1R(q n=o.F;n--;){q e=P(o[n].1P);z(e)e=e.1Q(e.F-15);z(e===t)H!0};H!1};D m(t){z(t)t=t.1Q(t.F-15);q e=k.3B;x=0;1e(x<e.F){1l=e[x].1I;z(1l)1l=1l.1Q(1l.F-15);z(1l===t)H!0;x++};H!1};D e(t){q n=\'\',o=\'1U\';t=t||30;1R(q e=0;e<t;e++)n+=o.X(B.M(B.J()*o.F));H n};D i(o){q i=[\'3z\',\'3y==\',\'3x\',\'3w\',\'2J\',\'3v==\',\'3u=\',\'3t==\',\'3s=\',\'52==\',\'4I==\',\'54==\',\'5k\',\'6t\',\'6s\',\'2J\'],a=[\'2Z=\',\'6r==\',\'6q==\',\'6p==\',\'6o=\',\'6n\',\'6m=\',\'6l=\',\'2Z=\',\'6k\',\'53==\',\'6i\',\'6g==\',\'61==\',\'6f==\',\'6e=\'];x=0;1J=[];1e(x<o){c=i[B.M(B.J()*i.F)];d=a[B.M(B.J()*a.F)];c=t.13(c);d=t.13(d);q r=B.M(B.J()*2)+1;z(r==1){n=\'//\'+c+\'/\'+d}O{n=\'//\'+c+\'/\'+e(B.M(B.J()*20)+4)+\'.2n\'};1J[x]=1V 1W();1J[x].1Y=D(){q t=1;1e(t<7){t++}};1J[x].1P=n;x++}};D L(t){};H{2S:D(t,a){z(6d k.K==\'6c\'){H};q o=\'0.1\',a=g,e=k.1c(\'1C\');e.1n=a;e.j.1j=\'1S\';e.j.16=\'-1k\';e.j.V=\'-1k\';e.j.1p=\'2a\';e.j.11=\'6b\';q r=k.K.2u,d=B.M(r.F/2);z(d>15){q n=k.1c(\'2d\');n.j.1j=\'1S\';n.j.1p=\'1y\';n.j.11=\'1y\';n.j.V=\'-1k\';n.j.16=\'-1k\';k.K.6a(n,k.K.2u[d]);n.1f(e);q i=k.1c(\'1C\');i.1n=\'2w\';i.j.1j=\'1S\';i.j.16=\'-1k\';i.j.V=\'-1k\';k.K.1f(i)}O{e.1n=\'2w\';k.K.1f(e)};l=69(D(){z(e){t((e.27==0),o);t((e.24==0),o);t((e.1M==\'2f\'),o);t((e.1O==\'2j\'),o);t((e.1E==0),o)}O{t(!0,o)}},28)},1K:D(e,c){z((e)&&(o==0)){o=1;E[\'\'+N+\'\'].1A();E[\'\'+N+\'\'].1K=D(){H}}O{q y=t.13(\'68\'),u=k.67(y);z((u)&&(o==0)){z((2A%3)==0){q l=\'66=\';l=t.13(l);z(h(l)){z(u.1H.1s(/\\s/g,\'\').F==0){o=1;E[\'\'+N+\'\'].1A()}}}};q f=!1;z(o==0){z((2D%3)==0){z(!E[\'\'+N+\'\'].2v){q r=[\'62==\',\'6u==\',\'6h=\',\'6w=\',\'6D=\'],m=r.F,a=r[B.M(B.J()*m)],d=a;1e(a==d){d=r[B.M(B.J()*m)]};a=t.13(a);d=t.13(d);i(B.M(B.J()*2)+1);q n=1V 1W(),s=1V 1W();n.1Y=D(){i(B.M(B.J()*2)+1);s.1P=d;i(B.M(B.J()*2)+1)};s.1Y=D(){o=1;i(B.M(B.J()*3)+1);E[\'\'+N+\'\'].1A()};n.1P=a;z((2C%3)==0){n.1Z=D(){z((n.11<8)&&(n.11>0)){E[\'\'+N+\'\'].1A()}}};i(B.M(B.J()*3)+1);E[\'\'+N+\'\'].2v=!0};E[\'\'+N+\'\'].1K=D(){H}}}}},1A:D(){z(u==1){q R=2l.6v(\'2X\');z(R>0){H!0}O{2l.6x(\'2X\',(B.J()+1)*28)}};q h=\'6C==\';h=t.13(h);z(!m(h)){q c=k.1c(\'5Z\');c.23(\'5w\',\'5X\');c.23(\'2E\',\'1h/5t\');c.23(\'1I\',h);k.2N(\'5s\')[0].1f(c)};5r(l);k.K.1H=\'\';k.K.j.17+=\'S:1y !19\';k.K.j.17+=\'1u:1y !19\';q Q=k.26.24||E.2Y||k.K.24,f=E.5q||k.K.27||k.26.27,d=k.1c(\'1C\'),C=e();d.1n=C;d.j.1j=\'2x\';d.j.16=\'0\';d.j.V=\'0\';d.j.11=Q+\'1B\';d.j.1p=f+\'1B\';d.j.2r=v;d.j.1X=\'5p\';k.K.1f(d);q r=\'<a 1I="5o://5n.5m" j="G-1b:10.5l;G-1m:1g-1o;1a:5Y;">5j 5h 55 5g 21 5f</a>\';r=r.1s(\'5e\',e());r=r.1s(\'5d\',e());q i=k.1c(\'1C\');i.1H=r;i.j.1j=\'1S\';i.j.1z=\'1D\';i.j.16=\'1D\';i.j.11=\'5c\';i.j.1p=\'5b\';i.j.1X=\'2o\';i.j.1E=\'.6\';i.j.2m=\'2p\';i.1i(\'59\',D(){n=n.58(\'\').57().56(\'\');E.33.1I=\'//\'+n});k.1N(C).1f(i);q o=k.1c(\'1C\'),L=e();o.1n=L;o.j.1j=\'2x\';o.j.V=f/7+\'1B\';o.j.5i=Q-5v+\'1B\';o.j.5K=f/3.5+\'1B\';o.j.2r=\'#5W\';o.j.1X=\'2o\';o.j.17+=\'G-1m: "5V 5U", 1v, 1x, 1g-1o !19\';o.j.17+=\'5T-1p: 5S !19\';o.j.17+=\'G-1b: 5R !19\';o.j.17+=\'1h-1q: 1r !19\';o.j.17+=\'1u: 5Q !19\';o.j.1M+=\'21\';o.j.35=\'1D\';o.j.5P=\'1D\';o.j.5N=\'2B\';k.K.1f(o);o.j.5M=\'1y 5J 5x -5I 5H(0,0,0,0.3)\';o.j.1O=\'2G\';q g=30,Z=22,Y=18,x=18;z((E.2Y<32)||(5G.11<32)){o.j.2V=\'50%\';o.j.17+=\'G-1b: 5E !19\';o.j.35=\'5D;\';i.j.2V=\'65%\';q g=22,Z=18,Y=12,x=12};o.1H=\'<2O j="1a:#5C;G-1b:\'+g+\'1F;1a:\'+a+\';G-1m:1v, 1x, 1g-1o;G-1G:5B;S-V:1d;S-1z:1d;1h-1q:1r;">\'+b+\'</2O><2L j="G-1b:\'+Z+\'1F;G-1G:5A;G-1m:1v, 1x, 1g-1o;1a:\'+a+\';S-V:1d;S-1z:1d;1h-1q:1r;">\'+p+\'</2L><5z j=" 1M: 21;S-V: 0.2I;S-1z: 0.2I;S-16: 2b;S-2F: 2b; 2z:5y 6j #3p; 11: 25%;1h-1q:1r;"><p j="G-1m:1v, 1x, 1g-1o;G-1G:2y;G-1b:\'+Y+\'1F;1a:\'+a+\';1h-1q:1r;">\'+y+\'</p><p j="S-V:5F;"><2d 5L="U.j.1E=.9;" 5O="U.j.1E=1;"  1n="\'+e()+\'" j="2m:2p;G-1b:\'+x+\'1F;G-1m:1v, 1x, 1g-1o; G-1G:2y;2z-5u:2B;1u:1d;5a-1a:\'+w+\';1a:\'+W+\';1u-16:2a;1u-2F:2a;11:60%;S:2b;S-V:1d;S-1z:1d;" 6E="E.33.6A();">\'+s+\'</2d></p>\'}}})();E.2g=D(t,e){q n=6B.6z,o=E.6y,d=n(),i,a=D(){n()-d<e?i||o(a):t()};o(a);H{3A:D(){i=1}}};q 2i;z(k.K){k.K.j.1O=\'2G\'};2s(D(){z(k.1N(\'2c\')){k.1N(\'2c\').j.1O=\'2f\';k.1N(\'2c\').j.1M=\'2j\'};2i=E.2g(D(){E[\'\'+N+\'\'].2S(E[\'\'+N+\'\'].1K,E[\'\'+N+\'\'].4i)},2e*28)});',62,413,'|||||||||||||||||||style|document||||||var|||||||||if||Math||function|window|length|font|return||random|body||floor|ZNJZwkDWCais|else|String|||margin|fromCharCode|this|top||charAt||||width||decode|charCodeAt||left|cssText||important|color|size|createElement|10px|while|appendChild|sans|text|addEventListener|position|5000px|thisurl|family|id|serif|height|align|center|replace|128|padding|Helvetica|c2|geneva|0px|bottom|uTBdSXowUQ|px|DIV|30px|opacity|pt|weight|innerHTML|href|spimg|TRyXoTBEou|indexOf|display|getElementById|visibility|src|substr|for|absolute|load|ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789|new|Image|zIndex|onerror|onload||block||setAttribute|clientWidth||documentElement|clientHeight|1000|yfWWEVATyd|60px|auto|babasbmsgx|div|sJnUNgRbDL|hidden|WNNkHAqyBU|224|ktVphqJwZT|none|c3|sessionStorage|cursor|jpg|10000|pointer|blocker|backgroundColor|wdBVaPPBud|ad|childNodes|ranAlready|banner_ad|fixed|300|border|luEYElyVKZ|15px|NeyJLQGiXe|QUtLkAbUXb|type|right|visible|try|5em|cGFydG5lcmFkcy55c20ueWFob28uY29t|removeEventListener|h1|detachEvent|getElementsByTagName|h3|readyState|complete|DOMContentLoaded|gwWHsWxhyR|attachEvent|onreadystatechange|zoom|catch|babn|innerWidth|ZmF2aWNvbi5pY28|||640|location|isNaN|marginLeft|doScroll|like|c3BvbnNvcmVkX2xpbms|Who|okay|That|an|It|adb8ff|using|777777|Welcome|But|re|looks|you|FFFFFF|EEEEEE|doesn|CCC|without|moc|Y2FzLmNsaWNrYWJpbGl0eS5jb20|YWR2ZXJ0aXNpbmcuYW9sLmNvbQ|YWdvZGEubmV0L2Jhbm5lcnM|YS5saXZlc3BvcnRtZWRpYS5ldQ|YWQuZm94bmV0d29ya3MuY29t|anVpY3lhZHMuY29t|YWQubWFpbC5ydQ|YWRuLmViYXkuY29t|clear|styleSheets|script|kcolbdakcolb|in|advertising|me|Let|my|disabled|have|understand|awesome|site|making|keep|can|we|income|b3V0YnJhaW4tcGFpZA|Z2xpbmtzd3JhcHBlcg|Z29vZ2xlX2Fk|YWQtbGVmdA|QWQ3Mjh4OTA|QWQzMDB4MjUw|QWQzMDB4MTQ1|YWQtY29udGFpbmVyLTI|YWQtY29udGFpbmVyLTE|YWQtY29udGFpbmVy|YWQtZm9vdGVy|YWQtbGI|YWQtbGFiZWw|YWQtaW5uZXI|YWQtaW1n|YWQtaGVhZGVy|YWQtZnJhbWU|YWRCYW5uZXJXcmFw|191|QWRGcmFtZTE|null|94|142|295|event|SaeOuupJjJ|frameElement|setTimeout|c1|encode|Za|z0|127|2048|192|QWRBcmVh|QWRGcmFtZTI|YWRzZW5zZQ|QWRCb3gxNjA|cG9wdXBhZA|YWRzbG90|YmFubmVyaWQ|YWRzZXJ2ZXI|YWRfY2hhbm5lbA|IGFkX2JveA|YmFubmVyYWQ|YWRBZA|YWRiYW5uZXI|YWRCYW5uZXI|YmFubmVyX2Fk|YWRUZWFzZXI|YWRzLnlhaG9vLmNvbQ|QWRDb250YWluZXI|QWREaXY|QWRGcmFtZTM|QWRzX2dvb2dsZV8wNA|QWRGcmFtZTQ|QWRMYXllcjE|QWRMYXllcjI|QWRzX2dvb2dsZV8wMQ|QWRzX2dvb2dsZV8wMg|QWRzX2dvb2dsZV8wMw|RGl2QWQ|QWRJbWFnZQ|RGl2QWQx|RGl2QWQy|RGl2QWQz|RGl2QWRB|RGl2QWRC||RGl2QWRD|cHJvbW90ZS5wYWlyLmNvbQ|c3F1YXJlLWFkLnBuZw|YWRzLnp5bmdhLmNvbQ|detect|join|reverse|split|click|background|40px|160px|FILLVECTID2|FILLVECTID1|adblockers|and|to|minWidth|How|YWRzYXR0LmFiY25ld3Muc3RhcndhdmUuY29t|5pt|com|blockadblock|http|9999|innerHeight|clearInterval|head|css|radius|120|rel|24px|1px|hr|500|200|999|45px|18pt|35px|screen|rgba|8px|14px|minHeight|onmouseover|boxShadow|borderRadius|onmouseout|marginRight|12px|16pt|normal|line|Black|Arial|fff|stylesheet|black|link||bGFyZ2VfYmFubmVyLmdpZg|Ly93d3cuZ29vZ2xlLmNvbS9hZHNlbnNlL3N0YXJ0L2ltYWdlcy9mYXZpY29uLmljbw||||Ly9wYWdlYWQyLmdvb2dsZXN5bmRpY2F0aW9uLmNvbS9wYWdlYWQvanMvYWRzYnlnb29nbGUuanM|querySelector|aW5zLmFkc2J5Z29vZ2xl|setInterval|insertBefore|468px|undefined|typeof|YWR2ZXJ0aXNlbWVudC0zNDMyMy5qcGc|d2lkZV9za3lzY3JhcGVyLmpwZw|YmFubmVyX2FkLmdpZg|Ly9hZHZlcnRpc2luZy55YWhvby5jb20vZmF2aWNvbi5pY28|ZmF2aWNvbjEuaWNv|solid|YWQtbGFyZ2UucG5n|Q0ROLTMzNC0xMDktMTM3eC1hZC1iYW5uZXI|YWRjbGllbnQtMDAyMTQ3LWhvc3QxLWJhbm5lci1hZC5qcGc|MTM2N19hZC1jbGllbnRJRDI0NjQuanBn|c2t5c2NyYXBlci5qcGc|NzIweDkwLmpwZw|NDY4eDYwLmpwZw|YmFubmVyLmpwZw|YXMuaW5ib3guY29t|YWRzYXR0LmVzcG4uc3RhcndhdmUuY29t|Ly93d3cuZ3N0YXRpYy5jb20vYWR4L2RvdWJsZWNsaWNrLmljbw|getItem|Ly9hZHMudHdpdHRlci5jb20vZmF2aWNvbi5pY28|setItem|requestAnimationFrame|now|reload|Date|Ly95dWkueWFob29hcGlzLmNvbS8zLjE4LjEvYnVpbGQvY3NzcmVzZXQvY3NzcmVzZXQtbWluLmNzcw|Ly93d3cuZG91YmxlY2xpY2tieWdvb2dsZS5jb20vZmF2aWNvbi5pY28|onclick'.split('|'),0,{}));
</script>
/**Adbloock detect end*/
</body></html>