<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nz_ims";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("Sorry to Connect to DB ".mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $its = mysqli_real_escape_string($conn, $_POST['txtUserName']);
    $password = mysqli_real_escape_string($conn, $_POST['txtPassword']);

    $query = "SELECT * FROM admin_users WHERE ITS = '$its' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['sr'];
            $_SESSION['ITS'] = $user['ITS'];
            header("Location: frame.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Username not found.";
    }
}
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    body{
        background-image:url("imgs/login_bg.jpg");
        background-repeat:no-repeat;
        background-size: cover;
        background-color: #61a0b1;
        min-height: 100vh;
        font-family: 'Source Sans Pro', Arial;
        font-size: 14px;
        color: #000;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    
    @media screen and (min-width: 768px) {
        .wrapper .content {
            margin-top: 0;
            margin-right: 25px;
            max-width: 370px;
            padding: 20px;
            width: 100%;
        }
    }
    header .logo-image {
        display: block;
        margin-left: auto;
        margin-right: auto;
        padding: 0;
        height: 172px;
        margin-bottom: 10px;
    }
    .heading-title {
        font-family: "Alice", serif;
        color: #b98027;
        text-align: center;
        margin-bottom: 10px;
        font-size: 11px;
    }
    .wrapper #mobile-bg {
        width: 100%;
        height: 200px;
    }
    @media screen and (min-width: 768px) {
        .motif-login {
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding: 0;
            width: 90px;
            margin-bottom: 10px;
        }
    }
    @media screen and (min-width: 768px) {
        .input-group {
            display: flex;
            flex-direction: column;
            padding: 10px;
        }
    }
    @media screen and (min-width: 768px) {
        .select {
            font-size: 12px;
            cursor: pointer;
        }
    }
    /* @media screen and (min-width: 768px) {
        .lbl-text {
            font-size: 13px;
            font-family: "Roboto", sans-serif;
            text-align: left;
            color: #0e4653;
            opacity: 0.7;
            margin-bottom: 3px;
            margin-left: 70px;
            padding-top: 10px;
        }
}    */
#divLogin{text-align:center}
        .btn-login {width: 140px; height: 40px; border-radius: 21px; padding-left: 0px; font-size: 15px; margin: auto; background-color: #0e4653; border: solid 1px #0e4653; border-bottom: solid 5px #0e4653; color: #fff; transition: all .5s ease-in; font-weight: 400; margin-bottom: 10px; cursor: pointer;}
#divCaptcha{padding: 10px;margin-bottom:20px;padding-top:0px}
        .icon-nologin{font-size:80px; color:#c50409}
        .btn-Nologin{padding-top:7px;margin-top: 10px;}
.lbl-text {
    font-size: 13px;
    font-family: "Roboto", sans-serif;
    color: #0e4653;
    opacity: 0.7;
    text-align: left;
    margin-bottom: 3px;
    margin-left: 70px;
    padding-top: 10px;
}
#txtUserName::-webkit-outer-spin-button,
#txtUserName::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
.select {
    font-size: 12px;
    text-align: center;
    vertical-align: middle;
    margin-bottom: 25px;
    font-family: "Roboto", sans-serif;
    color: rgb(160, 157, 157);
    cursor: pointer;
}
section {
    display: block;
    unicode-bidi: isolate;
}
h1{
    font-size: 19px;
    font-weight: 500;
}

</style>

<script>
function TogglePasswordEye() {
    var input = document.getElementById("txtPassword");
    var showEye = document.getElementById("showeye");
    var hideEye = document.getElementById("hideeye");

    if (input.type === "password") {
        input.type = "text";
        showEye.style.display = "none";
        hideEye.style.display = "inline";
    } else {
        input.type = "password";
        showEye.style.display = "inline";
        hideEye.style.display = "none";
    }
}

function ChangeBtnText(btn, newText, disable) {
    btn.value = newText;
    if (disable) {
        btn.disabled = true;
    }
}
</script>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4" style="width: 350px;">

        <form method="post" action="">
            <section class="wrapper">

                <header>
                    <img class="logo-image" src="https://www.its52.com/imgs/1443/ITS_Logo_Golden.png?v1" alt="Idaratut Ta'reef Al Shakhsi" />
                    <div class="heading-title"><h1>IDARATUT TA'REEF AL SHAKHSI</h1></div>
                    <img class="motif-login" src="https://www.its52.com/imgs/1443/Motif_Login.png?v1" alt="" />
                </header>
                
                <div id="divLogin">
                    <div class="input-group">
                        <span class="lbl-text">ITS ID</span>
                        <div class="input-password">
                            <input name="txtUserName" maxlength="8" id="txtUserName" tabindex="1" class="input-control" onblur="javascript:CheckNumeric();" type="number" placeholder="Enter ITS ID" onfocus="this.type='number'" oninput="this.value = this.value.slice(0, this.maxLength);" pattern="[0-9]*" required="">
                        </div>
                        <span class="lbl-text">Password</span>
                        <div class="input-password">
                            <input name="txtPassword" type="password" maxlength="20" id="txtPassword" tabindex="2" class="input-control" placeholder="Enter Password" required="" />
                            <span class="eye" onclick="TogglePasswordEye()">
                                <i id="showeye" class="material-icons" style="cursor:pointer;">show</i>
                                <i id="hideeye" class="material-icons" style="cursor:pointer; display:none;">hide</i>
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <input type="submit" name="btnLogin" value="Login ITS" onclick="ChangeBtnText(this,'Authenticating...',false);"  id="btnLogin" tabindex="4" class="btn-login" />    
                    </div>
                        <label><a title="Forgot Password click here" tabindex="5" href="Common/ForgotPassword.aspx">Forgot Password?</a></label>
                    </div>

                </div>
            </div>
        </section>
        </form>
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
<html>
<head><title>Login</title></head>
<body>
    <a href="<?php echo $login_url; ?>">Login with Google</a>
</body>
</html>

    </div>
</body>
</html>