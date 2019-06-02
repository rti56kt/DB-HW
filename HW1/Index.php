<?php
    session_start();
    session_unset(); # remove all session var
    session_destroy(); # destroy the session
    $_SESSION['Authenticted']=false;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>DB_HW1 Login</title>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="Design.css">
        <script>
            function validateForm() {
                var accErr="";
                var UserAccount = document.forms["login_form"]["UserAccount"].value;
                var UserPassword = document.forms["login_form"]["UserPassword"].value;
                if ((UserAccount == null || UserAccount == "") && (UserPassword == null || UserPassword == "")) {
                    alert("帳號呢？\n密碼呢？\n什麼都沒有就想登入？");
                    return false;
                }else if (UserAccount == null || UserAccount == "") {
                    alert("只給我密碼不給我帳號你是想要我做什麼？");
                    return false;
                }else if (UserPassword == null || UserPassword == "") {
                    alert("生活小知識：\n你知道嗎？\n只填帳號不填密碼是不能登入的");
                    return false;
                }
            }
        </script>
    </head>

    <body>
        <div class="login_board">
            <form name="login_form"action="Login.php" onsubmit= "return validateForm()" method="POST">
                <h1 style="font-size:28px;color:white">Login</h1>
                <input type="text" placeholder="  Account" name="UserAccount"><br><br>
                <input type="password" placeholder="  Password" name="UserPassword"><br><br>
                <input type="submit" value="Login"><br>
                <hr> <!--一條分隔線-->
                <p style="font-size:15px;color:white"><b>Not a member?</b>
                    <a href="Signup.php" style="font-size:15px;color:rgb(52, 152, 219)"><b>Sign Up</b></a>
                </p>
            </form>
        </div>
    </body>
</html>