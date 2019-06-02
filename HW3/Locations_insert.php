<?php
    session_start();

    if(isset($_POST["submit"])){
        $Location = $_POST["Location"];

        if(empty($Location))
            echo "<script>alert('請輸入要新增的Location！！'); window.location.replace('Locations_insert.php');</script>";
        else {
            $db_host = "dbhome.cs.nctu.edu.tw";
            $db_name = "yyli0911_cs_HW3";
            $db_user = "yyli0911_cs";
            $db_password = "IloveNCTUDB";
            
            try {
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $ifexist = $db->prepare("SELECT location FROM Locations WHERE location='$Location'");
                $ifexist->execute();
                if($ifexist->rowCount()!=0){
                    echo "<script>alert('location is already exists.'); window.location.replace('Locations_insert.php');</script>";
                }else{
                    $house_sql = "INSERT INTO Locations(location) VALUES(:location)";

                    $house_stmt = $db->prepare($house_sql);
                    $house_stmt->bindParam(':location', $Location);
                    $house_stmt->execute();

                    echo "<script>window.location.replace('Location: Locations_m.php');</script>";
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
    <title>DB_HW3 Location Insert</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="signup">
        <form action="Locations_insert.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){  ?>
                    <h1 style="font-size:28px;color:white">New Location</h1>
            <?php } ?>
            
            <input type="text" placeholder = "New Location" name = "Location"><br><br>

            <input type="submit" value = "submit" name = "submit">
            <input type="reset" value = "reset">
            
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){ ?>
                    <input type="button" value = "Cancel" onClick = "window.location.replace('Locations_m.php')">
            <?php } ?>
            <br><br>
        </form>
    </div>
</body>
</html>