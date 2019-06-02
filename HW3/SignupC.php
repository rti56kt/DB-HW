<?php
session_start();

$account = $_POST["account"];
$password = $_POST["password"];
$password2 = $_POST["password2"];
$name = $_POST["name"];
$email = $_POST["mail"];

if(isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){
    if(isset($_SESSION['UserIdentity']) && $_SESSION['UserIdentity']=="Admin"){
        if(isset($_POST["admin"])) $admin = $_POST["admin"];
    }else{
        $admin = "User";
    }
}else{
    $admin = "User";
}

if(empty($account))
    echo "<script>alert('請輸入帳號！！'); window.location.replace('Signup.php');</script>";
else if(empty($password))
    echo "<script>alert('請輸入密碼！！'); window.location.replace('Signup.php');</script>";
else if(empty($password2))
    echo "<script>alert('請再次確認密碼！！'); window.location.replace('Signup.php');</script>";
else if(empty($name))
    echo "<script>alert('請輸入名字！！'); window.location.replace('Signup.php');</script>";
else if(empty($email))
    echo "<script>alert('請輸入電子信箱！！'); window.location.replace('Signup.php');</script>";
else if (!preg_match("/^[a-zA-Z0-9_]*$/",$account))
    echo "<script>alert('帳號只能有大小寫英文字母跟數字ㄛ'); window.location.replace('Signup.php');</script>";
else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    echo "<script>alert('電子信箱格式錯了喔！！'); window.location.replace('Signup.php');</script>";
else if($password != $password2)
    echo "<script>alert('兩次輸入的密碼不一樣ㄛ！！'); window.location.replace('Signup.php');</script>";
else if(empty($admin))
    echo "<script>alert('請選擇此用戶的身分！！'); window.location.replace('Signup.php');</script>";
else{
    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW3";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";

    /*找帳號有沒有重複，要防sql injection */
    try {
        $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
        # set the PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $data_sql = "SELECT account FROM Users WHERE account=?";
        $statement = $db->prepare("$data_sql");
        $statement->execute(array($account));

        if($statement->rowCount() == 0){
            $HashPassword = hash('sha512', $password);
            $statement = $db->prepare("INSERT INTO `Users` (account, password, name, mail, user_or_admin)
            VALUES(:account, :password, :name, :mail, :user_or_admin) ");

            $statement->bindParam(':account', $account);
            $statement->bindParam(':password', $HashPassword);
            $statement->bindParam(':name', $name);
            $statement->bindParam(':mail', $email);
            $statement->bindParam(':user_or_admin', $admin);
            $statement->execute();
            if(isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){
                if(isset($_SESSION['UserIdentity']) && $_SESSION['UserIdentity']=="Admin"){
                    echo "<script>alert('註冊成功ㄛ！！'); window.location.replace('Member_m.php');</script>";
                }
            }
            echo "<script>alert('註冊成功ㄛ！！'); window.location.replace('Index.php');</script>";
        }
        else{
            echo "<script>alert('註冊失敗ㄛ！！'); window.location.replace('Signup.php');</script>";
        }
    }
    catch(PDOException $e){
        $msg=$e->getMessage();
        session_unset();
        session_destroy();
echo <<<EOT
        <!DOCTYPE html>
        <html>
            <body>
            <script>
            alert("Internal Error.");
            </script>
EOT;
    }
}
?>