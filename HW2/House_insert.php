<?php
    session_start();

    if(isset($_POST["submit"])){
        $name = $_POST["name"];
        $price = $_POST["price"];
        $location = $_POST["location"];
        $date = date("Y/m/d");
        $user_id = $_SESSION['UserID'];

        if(empty($name))
            echo "<script>alert('請輸入房屋名稱！！'); window.location.replace('House_insert.php');</script>";
        elseif(empty($price))
            echo "<script>alert('請輸入房屋價錢！！'); window.location.replace('House_insert.php');</script>";
        elseif(empty($location))
            echo "<script>alert('請輸入房屋地點！！'); window.location.replace('House_insert.php');</script>";
        else {
            $db_host = "dbhome.cs.nctu.edu.tw";
            $db_name = "yyli0911_cs_HW2";
            $db_user = "yyli0911_cs";
            $db_password = "IloveNCTUDB";
            
            try {
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $house_sql = "INSERT INTO House(name, price, location, time, owner_id)
                VALUES(:name, :price, :location, :time, :owner_id)";

                $house_stmt = $db->prepare($house_sql);
                $house_stmt->bindParam(':name', $name);
                $house_stmt->bindParam(':price', $price);
                $house_stmt->bindParam(':location', $location);
                $house_stmt->bindParam(':time', $date);
                $house_stmt->bindParam(':owner_id', $user_id);
                $house_stmt->execute();

                $order_house_stmt = $db->query("SELECT * FROM House ORDER BY id DESC");
                $house_row = $order_house_stmt->fetch();
                $house_id = $house_row[0];

                $info_array = $_POST["info"];

                if(isset($info_array)){
                    foreach($info_array as $i){
                        $info_stmt = $db->prepare("INSERT INTO Information(information, house_id)
                        VALUES(:information, :house_id)
                        ");
                        $info_stmt->bindParam(':information', $i);
                        $info_stmt->bindParam(':house_id', $house_id);
                        $info_stmt->execute();                        
                    }
                }
                header("Location: House_m.php");
                exit();
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
    <title>DB_HW2 House Insert</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="signup">
        <form action="House_insert.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){  ?>
                    <h1 style="font-size:28px;color:white">New House</h1>
            <?php } ?>
            
            <input type="text" placeholder = "House name" name = "name"><br><br>
            <input type="number" placeholder = "Price" name="price"><br><br>
            <input type="text" placeholder = "Location" name="location"><br><br>

            <input type="checkbox" name="info[]" value = "laundry facilities"> laundry facilities
            <input type="checkbox" name="info[]" value = "toiletries provided"> toiletries provided<br>
            <input type="checkbox" name="info[]" value = "shuttle service"> shuttle service
            <input type="checkbox" name="info[]" value = "no smoking"> no smoking<br>
            <input type="checkbox" name="info[]" value = "wifi"> wifi
            <input type="checkbox" name="info[]" value = "kitchen"> kitchen
            <input type="checkbox" name="info[]" value = "breakfast"> breakfast<br>
            <input type="checkbox" name="info[]" value = "television"> television
            <input type="checkbox" name="info[]" value = "lockers"> lockers
            <input type="checkbox" name="info[]" value = "elevator"> elevator<br>

            <input type="submit" value = "submit" name = "submit">
            <input type="reset" value = "reset">
            
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){ ?>
                    <input type="button" value = "Cancel" onClick = "window.location.replace('House_m.php')">
            <?php } ?>
            <br><br>
        </form>
    </div>
</body>
</html>