<?php
    session_start();
?>

<!DOCTYPE html>
<head>
        <title>DB_HW1 SignUp</title>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="Design.css">
</head>

<body>
    <div class="signup">
        <form action="SignupC.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){
                    if(isset($_SESSION['UserIdentity']) && $_SESSION['UserIdentity']=="Admin"){
            ?>
                <h1 style="font-size:28px;color:white">Add New User</h1>
            <?php
                    }
                }else{
            ?>
                <h1 style="font-size:28px;color:white">Sign Up</h1>
            <?php
                }
            ?>
            
            <input type="text" placeholder = "Account" name = "account">
            <br><br>
            
            <input type="password" placeholder = "Password" name="password">
            <br><br>

            <input type="password" placeholder = "Confirm Password" name="password2">
            <br><br>

            <input type="text" placeholder = "Name" name = "name">
            <br><br>
            
            <input type="email" placeholder = "Email" name = "mail">
            <br><br>

            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){
                    if(isset($_SESSION['UserIdentity']) && $_SESSION['UserIdentity']=="Admin"){
            ?>
                <input type="radio" name="admin" value="User"> User
                <input type="radio" name="admin" value="Admin"> Admin<br><br>
            <?php
                    }
                }
            ?>

            <input type="submit" value = "submit">
            <input type="reset" value = "reset">
            
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){
                    if(isset($_SESSION['UserIdentity']) && $_SESSION['UserIdentity']=="Admin"){
            ?>
                <input type="button" value = "back" onClick = "window.location.replace('List_admin.php')">
            <?php
                    }
                }else{
            ?>
                <input type="button" value = "back" onClick = "window.location.replace('Index.php')">
            <?php
                }
            ?>

            <br><br>
        </form>
    </div>
</body>
</html>