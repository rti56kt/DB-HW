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
                    $del=$conn->prepare("DELETE FROM Information WHERE id=?");
                    $del->execute(array($id_to_delete));
                }
            }
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW3 Informaiton_management</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage"> <!--edit here-->
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right"value="Home">
                <br>
                <h1 style="font-size:40px;color:Black">Information Management</h1>
                <p align="left"style="font-size:23px;">Information List:
                    <input type="button" onClick="window.location.replace('Information_insert.php');"style="float: right"value="Add New Information">
                    <br>
                    <?php
                        $Info_stmt = $conn->prepare(
                            "SELECT * FROM Information"
                        );
                        $Info_stmt->execute();
                        if($Info_stmt->rowCount()!=0){ ?>
                            <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Information</th>
                                    <th>Option</th>
                                    <th>  </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($Info_row = $Info_stmt->fetch()){ ?>
                                <tr>
                                    <td><?php echo $Info_row[0]; ?></td>
                                    <td><?php echo $Info_row[1]; ?></td>
                                    <td>
                                        <form action="Information_m.php" method="POST">
                                            <input type="hidden" name="id_to_delete" value=<?php echo $Info_row[0] ?>>
                                            <input type="submit" value="delete" name="submit" onClick="return confirm('Are you sure to delete this Information?')" id="delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php }?>   
                        <?php }else{ ?>
                            <br>
                            <p>The information list is empty :( </p>
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