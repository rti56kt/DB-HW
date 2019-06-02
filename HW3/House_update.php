<?php
    session_start();
                
    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW3";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";
    $house_id_update = $_POST["update_house_id"];

    if(isset($_POST["submit"])){
        $name = $_POST["name"];
        $price = $_POST["price"];
        $location_id = $_POST["location_id"];  //地點下拉選單選到的地點id
        $date = date("Y/m/d");
        $user_id = $_SESSION['UserID'];
        $house_id_update = $_POST["update_house_id"];

        if(empty($name))
            echo "<script>alert('請輸入房屋名稱！！'); window.location.replace('House_m.php');</script>";
        elseif(empty($price))
            echo "<script>alert('請輸入房屋價錢！！'); window.location.replace('House_m.php');</script>";
        else{

            try {
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                $location_id; 
                //echo "$location_id";

                $update_stmt = $db->prepare("UPDATE House
                SET name = :name, price = :price, time = :date, owner_id = :user_id, location_id = :location_id
                WHERE id='$house_id_update'");

                $update_stmt->bindParam(':name', $name);
                $update_stmt->bindParam(':price', $price);
                $update_stmt->bindParam(':date', $date);
                $update_stmt->bindParam(':user_id', $user_id);
                $update_stmt->bindParam(':location_id', $location_id);
                $update_stmt->execute();
                    

                $delete_info_stmt = $db->prepare("DELETE FROM House_Info_mapping WHERE house_id = :house_id");
                $delete_info_stmt->bindParam(':house_id', $house_id_update);
                $delete_info_stmt->execute();

                if(isset($_POST["info"])){
                    $info_id_array = $_POST["info"];
                }else{
                    header("Location: House_m.php");
                    exit();
                }
                if(isset($info_id_array)){
                    foreach($info_id_array as $i){
                        $info_stmt = $db->prepare("INSERT INTO House_Info_mapping(info_id, house_id)
                        VALUES(:info_id, :house_id)");
                        $info_stmt->bindParam(':info_id', $i);
                        $info_stmt->bindParam(':house_id', $house_id_update);
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
    <title>DB_HW3 Edit House</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="signup">
        <form action="House_update.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){  ?>
                    <h1 style="font-size:28px;color:white">Edit House</h1>
            
            <?php } ?>
            
            <input type="text" placeholder = "House name" name = "name"><br><br>
            <input type="number" placeholder = "Price" name="price"><br><br>
            <input type="hidden" name = "update_house_id" value = <?php echo $house_id_update ?>>
            
            <select name="location_id" id="l_select">
                <?php
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

                $location_stmt = $db->prepare("SELECT id, location FROM Locations");
                $location_stmt ->execute();
                while($location_row = $location_stmt->fetch()){
                    echo "<option value=$location_row[0]>"; 
                    echo $location_row[1]; 
                    echo "</option>"; 
                }

                ?>
            </select><br><br>

            <?php
                $cnt = 0;
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

                $info_stmt = $db->prepare("SELECT id, information FROM Information");
                $info_stmt ->execute();
                while($info_row = $info_stmt->fetch()){
                    //echo "<input type='hidden' name='info_id[]' value = >";
                    echo "<input type='checkbox'  name='info[]' value = $info_row[0]>";
                    echo " $info_row[1]";
                    $cnt++;
                    if($cnt % 2 == 0){
                        echo "<br>";
                    } 
                }
            ?><br>

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