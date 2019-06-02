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
            $orderby="Book.id";$sortway="ASC";
            
            if(isset($_POST["submit"])){
                if($_POST["submit"]=="delete"){
                    $id_to_delete=$_POST["id_to_delete"];
                    $del=$conn->prepare("DELETE FROM Book WHERE id=?");
                    $del->execute(array($id_to_delete));
                }elseif($_POST["submit"]=="edit"){
                    $id_to_edit = $_POST["id_to_edit"];
                    $user_id = $_POST["user_id"];

                }elseif($_POST["submit"]=="▲"){
                    if(isset($_POST["price"])) $orderby="House.price";
                    if(isset($_POST["time"]))  $orderby="House.time";
                    $sortway="ASC";
                }elseif($_POST["submit"]=="▼"){
                    if(isset($_POST["price"])) $orderby="House.price";
                    if(isset($_POST["time"]))  $orderby="House.time";
                    $sortway="DESC";
                }
            }
?>

<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW3 Order Page</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage">
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Home">
                <br>
                <h1 style="font-size:40px">Order Page</h1><br>
                <br><br><hr>
                
                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>
                            <form  action=Order_Page.php method="post">
                                <input type="submit" name="submit" value="▲" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="price" value="price">
                                Price
                                <input type="submit" name="submit" value="▼" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="price" value="price">
                            </form>
                        </th>
                        <th>Location</th>
                        <th>
                            <form  action=Order_Page.php method="post">
                                <input type="submit" name="submit" value="▲" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="time" value="time">
                                Time
                                <input type="submit" name="submit" value="▼" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="time" value="time">
                            </form>
                        </th>
                        <th>Owner</th>
                        <th>Infomation</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Option</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $house_stmt = $conn->prepare(
                        "SELECT DISTINCT Book.id, House.name, House.price, House.time, Users.name, Book.check_in, Book.check_out, House.id
                        FROM (((House JOIN Users ON House.owner_id = Users.id)
                        LEFT JOIN House_Info_mapping ON House_Info_mapping.house_id = House.id)
                        JOIN Book ON Book.house_id = House.id)
                        WHERE Book.visitor_id = '$cur_userid'
                        ORDER BY $orderby $sortway, Book.id ASC"
                    );
                    $house_stmt->execute();
                    if($house_stmt->rowCount()!=0){
                        while($house_row = $house_stmt->fetch()){?>
                            <tr>
                                <td><?php echo $house_row[0]; ?></td>
                                <td><?php echo $house_row[1]; ?></td>
                                <td><?php echo $house_row[2]; ?></td>
                                <td><?php
                                    $locid_stmt = $conn->prepare(
                                        "SELECT location_id FROM House WHERE id = '$house_row[7]'"
                                    );
                                    $locid_stmt->execute();
                                    $locid_id = $locid_stmt->fetch();
                                    if($locid_id[0] == NULL){
                                        echo "Unknown";
                                    }else{
                                        $loc_stmt = $conn->prepare(
                                            "SELECT Locations.location FROM Locations
                                            JOIN House ON House.location_id = Locations.id
                                            WHERE Locations.id = '$locid_id[0]'"
                                        );
                                        $loc_stmt->execute();
                                        $loc = $loc_stmt->fetch();
                                        echo $loc[0];
                                    }
                                    ?>
                                </td>
                                <td><?php echo $house_row[3]; ?></td>
                                <td><?php echo $house_row[4]; ?></td>
                                <td><br><?php
                                    $info_stmt = $conn->prepare(
                                        "SELECT Information.information
                                        FROM House_Info_mapping
                                        JOIN Information ON House_Info_mapping.info_id = Information.id
                                        WHERE House_Info_mapping.house_id = '$house_row[7]' "
                                    );
                                    $info_stmt->execute();
                                    while($info_row = $info_stmt->fetch()){
                                        echo $info_row[0], "<br>";
                                    }?>
                                    <br>
                                </td>
                                <td><?php echo $house_row[5]; ?></td>
                                <td><?php echo $house_row[6]; ?></td>
                                <td valign="middle">
                                    <form action="Order_Page.php" method="POST">
                                        <input type="hidden"name="id_to_edit"value=<?php echo $house_row[0] ?>>
                                        <input type="hidden"name="user_id"value=<?php echo $cur_userid ?>>
                                        <input type="submit"name="submit"value="edit"id="edit">
                                    
                                        <br>
                                        <input type="hidden"name="id_to_delete"value=<?php echo $house_row[0] ?>>
                                        <input type="submit"name="submit"value="delete"onClick="return confirm('Are you sure to delete this order?')"id="delete">
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    }else{ ?>
                        <br>
                        <p>You don't have any order yet.</p>
                    <?php } ?>
                </tbody>
                </table>
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