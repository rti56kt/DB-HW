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

?>
<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW3 House_page</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage"> <!--edit here-->
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right"value="Home">
                <br>
                <h1 style="font-size:40px;color:Black">House Page</h1>
                <p align="left"style="font-size:23px;">
                    <br>
                    <?php
                        $house_stmt = $conn->prepare(
                            "SELECT sub.name, sub.check_in, sub.check_out, Users.name 
                            FROM( 
                                SELECT House.name, Book.check_in, Book.check_out, House.owner_id, Book.visitor_id 
                                FROM Book JOIN House
                                ON Book.house_id = House.id
                            )as sub JOIN Users
                            ON sub.visitor_id = Users.id
                            WHERE sub.owner_id = ?"
                        );
                        $house_stmt->execute(array($_SESSION['UserID']));
                        if($house_stmt->rowCount()!=0){ ?>
                            <table>
                            <thead>
                                <tr>
                                    <th>House</th>
                                    <th>Check_in</th>
                                    <th>Check_out</th>
                                    <th>Visitor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($house_row = $house_stmt->fetch()){ ?>
                                <tr>
                                    <td><?php echo $house_row[0]; ?></td>
                                    <td><?php echo $house_row[1]; ?></td>
                                    <td><?php echo $house_row[2]; ?></td>
                                    <td><?php echo $house_row[3]; ?></td>
                                </tr>
                            <?php }   
                        }else{ ?>
                            <br>
                            <p>The house list is empty :( </p>
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