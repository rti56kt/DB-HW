<?php
    session_start();
                
    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW3";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";
    $order_id_update = $_POST["update_order_id"];
    $house_id = $_POST["house_id"];
    $today = date("Y/m/d");
    //echo "$today";
    
    if(isset($_POST["submit"])){
        $check_in = $_POST["check_in"];
        $check_out = $_POST["check_out"];
        $today = date("Y/m/d");
        $user_id = $_SESSION['UserID'];
        $house_id = $_POST["house_id"];
        $order_id_update = $_POST["update_order_id"];
        
        if(empty($check_in))
            echo "<script>alert('請輸入入住日期！！'); window.location.replace('Order_Page.php');</script>";
        elseif(empty($check_out))
            echo "<script>alert('請輸入退房日期！！'); window.location.replace('Order_Page.php');</script>";
        elseif(strtotime($check_in) >= strtotime($check_out))
            echo "<script>alert('退房日期必須比入住日期還晚！！'); window.location.replace('Order_Page.php');</script>";
        elseif(strtotime($check_in) <= strtotime($today))
            echo "<script>alert('入住日期必須比晚於今天！！'); window.location.replace('Order_Page.php');</script>";
        else{
            
            try {
            
                $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $order_house_stmt = $db->prepare("SELECT id, check_in, check_out
                FROM Book WHERE house_id = $house_id AND visitor_id != $user_id");
                $order_house_stmt->execute();

                $overlap_dates = array();

                while($order_house_row = $order_house_stmt->fetch()){
                    $start = $order_house_row[1];
                    $end = $order_house_row[2];
                    if(strtotime($check_in) <=  strtotime($start) && strtotime($check_out) > strtotime($start) && strtotime($check_out) < strtotime($end)){
                        $msg = "From $start to $check_out, \\n";
                        array_push($overlap_dates, $msg); 
                    }elseif(strtotime($check_in) >= strtotime($start) && strtotime($check_in) < strtotime($end) && strtotime($check_out) > strtotime($end)){
                        $msg = "From $check_in to $end, \\n";
                        array_push($overlap_dates, $msg); 
                    }elseif(strtotime($check_in) >= strtotime($start) && strtotime($check_in) <= strtotime($end) && strtotime($check_out) >= strtotime($start) && strtotime($check_out) <= strtotime($end)){
                        $msg = "From $check_in to $check_out, \\n";
                        array_push($overlap_dates, $msg); 
                    }elseif(strtotime($check_in) < strtotime($start) && strtotime($check_out) > strtotime($end)){
                        $msg = "From $start to $end, \\n";
                        array_push($overlap_dates, $msg); 
                    }else{                     // no collision
                        //do nothing.
                    }
                }
                if(empty($overlap_dates)){
                    
                    $update_stmt = $db->prepare("UPDATE Book
                    SET house_id = :house_id, check_in = :check_in, check_out = :check_out, visitor_id = :visitor_id
                    WHERE id='$order_id_update'");
    
                    $update_stmt->bindParam(':house_id', $house_id);
                    $update_stmt->bindParam(':check_in', $check_in);
                    $update_stmt->bindParam(':check_out', $check_out);
                    $update_stmt->bindParam(':visitor_id', $user_id);
                    $update_stmt->execute();
                    
                }
                else{
                    $final_msg = "";
                    foreach($overlap_dates as $i){
                        $final_msg = $final_msg.$i;
                    }
                    $tmp = "this room is  reserved by other visitors.\\nPlease choose other date to check-in!";
                    $final_msg = $final_msg.$tmp;
                    echo "<script>alert('$final_msg');</script>";
                }
                   
                echo "<script>window.location.replace('Order_Page.php');</script>";
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
    <title>DB_HW3 Edit Order Date</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="signup">
        <form action="Order_update.php" method="post">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){  ?>
                    <h1 style="font-size:28px;color:white">Edit Order Date</h1>
            
            <?php } ?>
            <label for="check_in">Check-in &nbsp;&nbsp;date: </label>
            <input type="date" name="check_in"><br><br>
            <label for="check_out">Check-out date:</label>
            <input type="date" name="check_out"><br><br>

            <input type="hidden" name = "update_order_id" value = <?php echo $order_id_update ?>>
            <input type="hidden" name = "house_id" value = <?php echo $house_id ?>>

            <input type="submit" value = "submit" name = "submit">
            <input type="reset" value = "reset">
            <?php
                if (isset($_SESSION['Authenticated']) && $_SESSION['Authenticated']==true){ ?>
                    <input type="button" value = "Cancel" onClick = "window.location.replace('Order_Page.php')">
            <?php } ?>
            <br><br>
        </form>
    </div>
</body>
</html>