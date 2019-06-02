<?php
    session_start();

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW2";
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
                }elseif($row[4] == "User"){
                    header("Location: Homepage.php");
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
                    $del=$conn->prepare("DELETE FROM Users WHERE id=?");
                    $del->execute(array($id_to_delete));
                }elseif($_POST["submit"]=="promote"){
                    $id_to_pro=$_POST["id_to_pro"];
                    $pro=$conn->prepare("UPDATE Users SET user_or_admin='Admin' WHERE id=?");
                    $pro->execute(array($id_to_pro));
                }elseif($_POST["submit"]=="demote"){
                    $id_to_de=$_POST["id_to_de"];
                    $de=$conn->prepare("UPDATE Users SET user_or_admin='User' WHERE id=?");
                    $de->execute(array($id_to_de));
                }
            }
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW2 Member_manage</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage">
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('Homepage.php');"style="float: right"value="Home">
                <br>
                <h1 style="font-size:40px;color:Black">Members Management</h1>
                <hr>
                <p align="left"style="font-size:23px;">Admin Imformation:<br>
                    <table align="center">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Account</th>
                            <th>Mail</th>
                            <th>Identity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background: rgba(52, 152, 219, 0.6)"><?php echo $row[1]; ?></td>
                            <td style="background: rgba(52, 152, 219, 0.6)"><?php echo $row[2]; ?></td>
                            <td style="background: rgba(52, 152, 219, 0.6)"><?php echo $row[3]; ?></td>
                            <td style="background: rgba(52, 152, 219, 0.6)"><?php echo $row[4]; ?></td>
                        </tr>
                    </tbody>
                    </table>
                </p>
                <hr>
                <p align="left"style="font-size:23px;">Other Users:
                    <input type="button" onClick="window.location.replace('Signup.php');"style="float: right"value="Add New User">
                    <br>
                    <table>
                    <thead>
                        <tr>
                            <th>User_ID</th>
                            <th>Name</th>
                            <th>Account</th>
                            <th>Mail</th>
                            <th>Identity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_s = $conn->prepare("SELECT id, name, account, mail, user_or_admin FROM Users WHERE id != ?");
                        $user_s->execute(array($_SESSION['UserID']));
                        while($users_op = $user_s->fetch()){ ?>
                            <tr>
                                <td><?php echo $users_op[0]; ?></td>
                                <td><?php echo $users_op[1]; ?></td>
                                <td><?php echo $users_op[2]; ?></td>
                                <td><?php echo $users_op[3]; ?></td>
                                <td><?php echo $users_op[4]; ?></td>
                                <td><form action="Member_m.php" method="POST">
                                    <?php if($users_op[4]!="Admin"){ ?>
                                        <input type="hidden"name="id_to_pro"value= <?php echo $users_op[0] ?>>
                                        <input type="submit"name="submit"value="promote"onClick="return confirm('Are you sure to promote this user?')"id="promote">
                                    <?php }elseif($users_op[4]!="User"){ ?>
                                        <input type="hidden"name="id_to_de"value=<?php echo $users_op[0] ?>>
                                        <input type="submit"name="submit"value="demote"onClick="return confirm('Are you sure to demote this user?')"id="demote">
                                    <?php } ?>
                                        <input type="hidden"name="id_to_delete"value=<?php echo $users_op[0] ?>>
                                        <input type="submit"name="submit"value="delete"onClick="return confirm('Are you sure to delete this user?')"id="delete">
                                    </form>
                                </td>
                            </tr>
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