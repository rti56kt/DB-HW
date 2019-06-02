<?php
    session_start();

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW1";
    $db_user = "yyli0911_cs";
    $db_password = "IloveNCTUDB";

    if(isset($_SESSION['UserID'])){
        try{
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # set the PDO error mode to exception
            $stmt = $conn->prepare("SELECT id, name, account, mail, user_or_admin FROM users WHERE id=?");
            $stmt->execute(array($_SESSION['UserID']));

            if($stmt->rowCount()==1){
                $row = $stmt->fetch();
                $_SESSION['Authenticated']=true;
                if($row[4] == "User"){
                    $_SESSION['UserIdentity'] = "User";
                }elseif($row[4] == "Admin"){
                    header("Location: List_admin.php");
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
            <title>DB_HW1 List</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="userpage">
                <h1 style="font-size:40px;color:Black">User Page</h1>                        
                <hr>
                <p align="left"style="font-size:23px;">User Imformation:<br>
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
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right"value="Logout"><br>
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