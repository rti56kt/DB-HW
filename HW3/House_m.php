<?php
    session_start();

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW3";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";

    if(isset($_SESSION['UserID'])){
        try{
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # set the PDO error mode to exception
            $stmt = $conn->prepare("SELECT id, name, account, mail, user_or_admin FROM Users WHERE id=?");
            $stmt->execute(array($_SESSION['UserID']));
            
            if($stmt->rowCount()==1){
                $row = $stmt->fetch();
                $_SESSION['Authenticated']=true;
                if($row[4] == "Admin"){
                    $_SESSION['UserIdentity'] = "Admin";
                }
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
                if($_POST["submit"]=="delete"){
                    $id_to_delete=$_POST["id_to_delete"];
                    $del=$conn->prepare("DELETE FROM House WHERE id=?");
                    $del->execute(array($id_to_delete));
                }
            }
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW3 House_manage</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage"> <!--edit here-->
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right"value="Home">
                <br>
                <h1 style="font-size:40px;color:Black">House Management</h1>
                <p align="left"style="font-size:23px;">Your House:
                    <input type="button" onClick="window.location.replace('House_insert.php');"style="float: right"value="Add New House">
                    <br>
                    <?php
                        $house_stmt = $conn->prepare(
                            "SELECT House.id, House.name, House.price, House.location_id, House.time, Users.name AS Owner
                            FROM House JOIN
                            Users ON House.owner_id = Users.id
                            WHERE House.owner_id =?
                            ORDER BY House.id ASC"
                        );
                        $house_stmt->execute(array($_SESSION['UserID']));
                        if($house_stmt->rowCount()!=0){ ?>
                            <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Location</th>
                                    <th>Time</th>
                                    <th>Owner</th>
                                    <th>Information</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($house_row = $house_stmt->fetch()){ ?>
                                <tr>
                                    <td><?php echo $house_row[0]; ?></td>
                                    <td><?php echo $house_row[1]; ?></td>
                                    <td><?php echo $house_row[2]; ?></td>
                                    
                                    <td valign="middle"><?php
                                    if(is_null($house_row[3])){
                                        echo "Unknown <br>";
                                    }else{
                                        $location_stmt = $conn->prepare(
                                            "SELECT location
                                            FROM Locations
                                            WHERE id = '$house_row[3]'"
                                        );
                                        $location_stmt->execute();
                                        $location_row = $location_stmt->fetch();
                                        echo $location_row[0], "<br>";
                                    }
                                    ?> </td>

                                    <td><?php echo $house_row[4]; ?></td>
                                    <td><?php echo $house_row[5]; ?></td>
                                    <td><br><?php $info_stmt = $conn->prepare(
                                            "SELECT information
                                            FROM `House_Info_mapping` JOIN Information
                                            ON House_Info_mapping.info_id = Information.id
                                            WHERE house_id = '$house_row[0]'
                                            ORDER BY `House_Info_mapping`.`house_id` ASC"
                                        );
                                        $info_stmt->execute();
                                        while($info_row = $info_stmt->fetch()){
                                            echo $info_row[0], "<br>";
                                        }?>
                                        <br>
                                    </td>
                                    <td>
                                        <form action="House_update.php" method="POST">
                                            <input type="hidden" name="update_house_id" value=<?php echo "'".$house_row[0]."'" ?>>
                                            <input type="submit" value="edit" name="edit" id="edit">
                                        </form>
                                        <form action="House_m.php" method="POST">
                                            <input type="hidden" name="id_to_delete" value=<?php echo $house_row[0] ?>>
                                            <input type="submit" value="delete" name="submit" onClick="return confirm('Are you sure to delete this house?')" id="delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php }?>   
                        <?php }else{ ?>
                            <br>
                            <p>You don't have any house yet.</p>
                        <?php } ?>
                    </tbody>
                    </table>
                </p>
            </div>
        </body>
    </html>
<?php
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
    }else{
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
?>