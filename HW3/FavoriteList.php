<?php
    session_start();

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW3";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";

    if(isset($_SESSION['UserID'])){
        try{
            $cur_userid = $_SESSION['UserID'];
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id, user_or_admin FROM Users WHERE id=?");
            $stmt->execute(array($_SESSION['UserID']));
            
            if($stmt->rowCount()==1){
                $row = $stmt->fetch();
                $_SESSION['Authenticated']=true;
                $_SESSION['UserIdentity'] = $row[1];
            }else{
                session_unset();
                session_destroy();
echo <<<EOT
                <!DOCTYPE html>
                <html>
                    <body>
                    <script>
                        alert("Something went wrong.");
                        window.location.replace("Index.php");
                    </script>
                    </body>
                </html>
EOT;
            }
            if(isset($_POST["submit"])){
                if($_POST["submit"]=="Remove"){
                    $fav_id_remove = $_POST["fav_id_remove"];
                    echo"$fav_id_remove<br>";
                    $del=$conn->prepare("DELETE FROM Favorite WHERE id=?");
                    $del->execute(array($fav_id_remove));
                }
            }
                 
?>

<!DOCTYPE html>
<head>
    <title>DB_HW3 Favorite List</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="Design.css">
</head>
<body>
    <div class="adminpage">
        <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
        <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right"value="Home">

        <br>
        <h1 style="font-size:40px;color:Black">Favorite List</h1><br>

            <?php
            $fav_stmt = $conn->prepare(
                "SELECT House.id, House.name, House.price, House.location_id, House.time, Users.name, Favorite.id
                FROM(House JOIN Favorite ON House.id = Favorite.favorite_id)JOIN
                Users ON House.owner_id = Users.id
                WHERE Favorite.user_id =?
                ORDER BY House.id ASC"
            );
            $fav_stmt->execute(array($_SESSION['UserID']));
            if($fav_stmt->rowCount()!=0){ ?>
                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Location</th>
                        <th>Time</th>
                        <th>Owner</th>
                        <th>Infomation</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while($fav_row = $fav_stmt->fetch()){ ?>
                    <tr>
                        <td><?php echo $fav_row[0]; ?></td>
                        <td><?php echo $fav_row[1]; ?></td>
                        <td><?php echo $fav_row[2]; ?></td>

                        <td valign="middle"><?php
                        if(is_null($fav_row[3])){
                            echo "Unknown <br>";
                        }else{
                            $location_stmt = $conn->prepare(
                                "SELECT location
                                FROM Locations
                                WHERE id = '$fav_row[3]'"
                            );
                            $location_stmt->execute();
                            $location_row = $location_stmt->fetch();
                            echo $location_row[0], "<br>";
                        }
                        ?> </td>

                        <td><?php echo $fav_row[4]; ?></td>
                        <td><?php echo $fav_row[5]; ?></td>
                        


                        <td valign="middle"><?php
                        $info_stmt = $conn->prepare(
                            "SELECT information
                            FROM `House_Info_mapping` JOIN Information
                            ON House_Info_mapping.info_id = Information.id
                            WHERE house_id = '$fav_row[0]'
                            ORDER BY `House_Info_mapping`.`house_id` ASC"
                        );
                        $info_stmt->execute();
                        echo "<br>";
                        while($info_row = $info_stmt->fetch()){
                            echo $info_row[0], "<br>";
                        }
                        echo "<br>";
                        ?> </td>

                        <td valign="middle">
                            <br>
                            <form action="FavoriteList.php" method="POST">
                            <input type="hidden" name="fav_id_remove" value=<?php echo $fav_row[6] ?>>
                            <input type="submit" value="Remove" name="submit">
                            </form>
                        </td>

                    </tr>
                <?php } ?>
            <?php }else{
                echo "You don't have any favorite house yet.<br>";
            } ?>
            </tbody>
            </table>
    </div>
</body>
</html>

<?php
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
?>
