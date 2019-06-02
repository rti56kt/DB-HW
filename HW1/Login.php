<?php
    session_start();
    $_SESSION['Authenticated']=false;

    $UserAccount = $_POST['UserAccount'];
    $UserPassword = $_POST['UserPassword'];
    
    $HashPassword = hash('sha512', $UserPassword);

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW1";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";
    
    try{
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # set the PDO error mode to exception
        $stmt = $conn->prepare("SELECT id, account, password, user_or_admin FROM users WHERE account=? && password=?");
        $stmt->execute(array($UserAccount, $HashPassword));

        if($stmt->rowCount()==1){
            $row = $stmt->fetch();
            $_SESSION['Authenticated']=true;
            $_SESSION['UserID']=$row[0];
            if($row[3] == "User"){
                header("Location: List_user.php");
            }elseif($row[3] == "Admin"){
                header("Location: List_admin.php");
            }
            exit();
        }else{
            session_unset();
            session_destroy();
echo <<<EOT
            <!DOCTYPE html>
            <html>
                <body>
                <script>
                    alert("Your Account or Password is Incorrect.");
                    window.location.replace("Index.php");
                </script>
                </body>
            </html>
EOT;
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
                window.location.replace("Index.php");
            </script>
            </body>
        </html>
EOT;
    }
?>