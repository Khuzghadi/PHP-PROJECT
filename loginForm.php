<?php
require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

$login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

<style>
body{
	background-image:url("imgs/login_bg.jpg");
	background-repeat:no-repeat;
	background-size:cover;
	min-height:100vh;
	font-family:'Source Sans Pro',Arial;
}

.card-box{
	width:380px;
	background:white;
	border-radius:10px;
	padding:20px;
	margin:auto;
	margin-top:40px;
}

.logo-image{
	display:block;
	margin:auto;
	height:150px;
}

.heading-title{
	text-align:center;
	color:#b98027;
	font-size:16px;
	margin:10px 0;
}

.input-group{
	margin-top:15px;
}

.lbl-text{
	font-size:14px;
	color:#0e4653;
	opacity:0.7;
	margin-bottom:5px;
}

.input-control{
	width:100%;
	height:45px;
	border:1px solid #ddd;
	border-radius:8px;
	padding:10px;
}

.eye{
	position:absolute;
	right:15px;
	top:50%;
	transform:translateY(-50%);
	cursor:pointer;
}

.btn-login{
	width:100%;
	height:45px;
	background:#0e4653;
	border:none;
	color:white;
	border-radius:25px;
	margin-top:15px;
	font-size:16px;
}

.or-line{
	margin:10px 0;
	text-align:center;
	position:relative;
}

.or-line span{
	background:white;
	padding:0 10px;
	position:relative;
	z-index:2;
}

.or-line::before{
	content:"";
	width:100%;
	height:1px;
	background:#ccc;
	position:absolute;
	top:50%;
	left:0;
	z-index:1;
}

.google-btn{
	width:100%;
	height:50px;
	border:1px solid #ddd;
	border-radius:8px;
	display:flex;
	align-items:center;
	justify-content:center;
	font-size:16px;
	gap:10px;
	background:white;
	cursor:pointer;
}

</style>

</head>

<body class="d-flex align-items-center justify-content-center">
<div class="card-box">

	<img class="logo-image" src="https://www.its52.com/imgs/1443/ITS_Logo_Golden.png?v1">

	<div class="heading-title"><h3>IDARATUT TA' REEF AL SHAKHSI</h3></div>

	<form method="POST" action="login.php">

		<div class="lbl-text">Username</div>
		<div style="position:relative;">
			<input type="text" name="username" class="input-control" placeholder="Enter Username or Email" required>
		</div>

		<div class="lbl-text">Password</div>
		<div style="position:relative;">
			<input type="password" name="password" id="pass" class="input-control" placeholder="Enter Password" required>
			<span class="eye" id="eye">&#128065;</span>
		</div>

		<button type="submit" name="login" class="btn-login">Login ITS</button>

		<p style="text-align:center;margin-top:10px;">
			<a href="Common/ForgotPassword.aspx" style="color:#777;font-size:14px;">Forgot Password?</a>
		</p>

		<div class="or-line"><span>or</span></div>

		<a class="google-btn" href="<?php echo $login_url; ?>">
			<img src="https://developers.google.com/identity/images/g-logo.png" height="22">
			Login with Google
		</a>

	</form>
</div>

<script>
let p=document.getElementById('pass');
let e=document.getElementById('eye');
e.onclick=function(){
	if(p.type==='password'){ p.type='text'; }
	else{ p.type='password'; }
}
</script>

</body>
</html>
