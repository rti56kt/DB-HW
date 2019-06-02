<?php
    session_start();

    if(isset($_POST["submit"])){
        $info = $_POST["Info"];

        if(empty($info))
            echo "<script>alert('請輸入要新增的Infomation！！'); window.location.replace('Information_insert.php');</script>";
        else {
            $db_host = "dbhome.cs.nctu.edu.tw";
            $db_name = "yyli0911_cs_HW3";
            $db_user = "yyli0911_cs";
            $db_password = "IloveNCTUDB";
            
            try {
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $ifexist = $db->prepare("SELECT information FROM Information WHERE information='$info'");
                $ifexist->execute();
                if($ifexist->rowCount()!=0){
                    echo "<script>alert('information is already exists.'); window.location.replace('Information_insert.php');</script>";
                }else{
                    $house_sql = "INSERT INTO Information(information) VALUES(:info)";

                    $house_stmt = $db->prepare($house_sql);
                    $house_stmt->bindParam(':info', $info);
                    $house_stmt->execute();

                    header("Location: Information_m.php");
                    exit();
                }
            }catch(PDOException $e){
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
        }
    }
?>

<!DOCTYPE html>
<head>
    <title>DB_HW3 Information Insert</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="signup">
        <form action="Information_insert.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){  ?>
                    <h1 style="font-size:28px;color:white">New Information</h1>
            <?php } ?>
            
            <input type="text" placeholder = "New Information" name = "Info"><br><br>

            <input type="submit" value = "submit" name = "submit">
            <input type="reset" value = "reset">
            
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){ ?>
                    <input type="button" value = "Cancel" onClick = "window.location.replace('Information_m.php')">
            <?php } ?>
            <br><br>
        </form>
    </div>
</body>
</html>