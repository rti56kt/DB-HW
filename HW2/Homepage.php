<?php
    session_start();

    $db_host = "dbhome.cs.nctu.edu.tw";
    $db_name = "yyli0911_cs_HW2";
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
            
            $searchID="";$searchName="";$searchPrice="";$searchLocation="";$searchTime="";$searchOwner="";$searchInfo="1";$InfoList="";
            $orderby="House.id";$sortway="ASC";
            
            if(isset($_POST["submit"])){
                if($_POST["submit"]=="delete"){
                    $id_to_delete=$_POST["id_to_delete"];
                    $del=$conn->prepare("DELETE FROM House WHERE id=?");
                    $del->execute(array($id_to_delete));
                }elseif($_POST["submit"]=="favorite"){
                    $favorite_house_id = $_POST["favorite_house_id"];
                    $user_id = $_POST["user_id"];

                    //echo "$user_id <br>", "$favorite_house_id <br>";
                    $fav_stmt = $conn->prepare("INSERT INTO Favorite(user_id, favorite_id) VALUES(:user_id, :favorite_house_id) ");

                    $fav_stmt->bindParam(':user_id', $user_id);
                    $fav_stmt->bindParam(':favorite_house_id', $favorite_house_id);
                    $fav_stmt->execute();
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
            if(isset($_POST["ID2search"])) $searchID=$_POST["ID2search"];
            if(isset($_POST["Name2search"])) $searchName=$_POST["Name2search"];
            if(isset($_POST["Price2search"])) $searchPrice=$_POST["Price2search"];
            if(isset($_POST["Location2search"])) $searchLocation=$_POST["Location2search"];
            if(isset($_POST["Time2search"])) $searchTime=$_POST["Time2search"];
            if(isset($_POST["Owner2search"])) $searchOwner=$_POST["Owner2search"];
            if(isset($_POST["Info2search"])){
                $InfoList=$_POST["Info2search"];
                $searchInfo=implode(" OR ", $_POST["Info2search"]);
                $Infocount=count($_POST["Info2search"]);
            }
?>

<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW2 Homepage</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage">
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('FavoriteList.php');"style="float: right;margin: 0px 0px 0px 5px;"value="My Favorite">
                <input type="button"onClick="window.location.replace('House_m.php');"style="float: right;margin: 0px 0px 0px 5px;"value="House Management">
                <?php if($_SESSION['UserIdentity'] == "Admin"){ ?>
                    <input type="button"onClick="window.location.replace('Member_m.php');"style="float: right"value="Members Management">
                <?php } ?>
                <br>
                <h1 style="font-size:40px">Homepage</h1><br>
                <p align="left"style="font-size:20px">Search:</p>
                <form action="Homepage.php" method="POST" align="left">
                    <input type="text"name="ID2search"placeholder="ID"value="<?php echo $searchID ?>"style="width: 15px;margin: 0px 0px 0px 5px;">
                    <input type="text"name="Name2search"placeholder="Name"value="<?php echo $searchName ?>"style="margin: 0px 0px 0px 5px">
                    <select name="Price2search"id="p_select"style="margin: 0px 0px 0px 5px">
                        <option value=""<?php if($searchPrice=="")echo 'selected' ?>>None</option>
                        <option value="BETWEEN 0 AND 3000"<?php if($searchPrice=="BETWEEN 0 AND 3000")echo 'selected' ?>>~3000</option>
                        <option value="BETWEEN 3000 AND 6000"<?php if($searchPrice=="BETWEEN 3000 AND 6000")echo 'selected' ?>>3000~6000</option>
                        <option value="BETWEEN 6000 AND 12000"<?php if($searchPrice=="BETWEEN 6000 AND 12000")echo 'selected' ?>>6000~12000</option>
                        <option value="NOT BETWEEN 0 AND 12000"<?php if($searchPrice=="NOT BETWEEN 0 AND 12000")echo 'selected' ?>>12000~</option>
                    </select>
                    <input type="text"name="Location2search"placeholder="Location"value="<?php echo $searchLocation ?>"style="margin: 0px 0px 0px 5px">
                    <input type="text"name="Time2search"placeholder="Time"value="<?php echo $searchTime ?>"style="margin: 0px 0px 0px 5px">
                    <input type="text"name="Owner2search"placeholder="Owner"value="<?php echo $searchOwner ?>"style="margin: 0px 0px 0px 5px">
                    <br><br>
                    <label for="laun">
                    <input type="checkbox"name="Info2search[]"value="Information.information='laundry facilities'"id="laun"
                        <?php if(in_array("Information.information='laundry facilities'", $InfoList))echo 'checked' ?>>laundry facilities<br>
                    </label>
                    <label for="wifi">
                    <input type="checkbox"name="Info2search[]"value="Information.information='wifi'"id="wifi"
                        <?php if(in_array("Information.information='wifi'", $InfoList))echo 'checked' ?>>wifi<br>
                    </label>
                    <label for="lock">
                    <input type="checkbox"name="Info2search[]"value="Information.information='lockers'"id="lock"
                        <?php if(in_array("Information.information='lockers'", $InfoList))echo 'checked' ?>>lockers<br>
                    </label>
                    <label for="kitc">
                    <input type="checkbox"name="Info2search[]"value="Information.information='kitchen'"id="kitc"
                        <?php if(in_array("Information.information='kitchen'", $InfoList))echo 'checked' ?>>kitchen<br>
                    </label>
                    <label for="elev">
                    <input type="checkbox"name="Info2search[]"value="Information.information='elevator'"id="elev"
                        <?php if(in_array("Information.information='elevator'", $InfoList))echo 'checked' ?>>elevator<br>
                    </label>
                    <label for="nsmo">
                    <input type="checkbox"name="Info2search[]"value="Information.information='no smoking'"id="nsmo"
                        <?php if(in_array("Information.information='no smoking'", $InfoList))echo 'checked' ?>>no smoking<br>
                    </label>
                    <label for="tele">
                    <input type="checkbox"name="Info2search[]"value="Information.information='television'"id="tele"
                        <?php if(in_array("Information.information='television'", $InfoList))echo 'checked' ?>>television<br>
                    </label>
                    <label for="brea">
                    <input type="checkbox"name="Info2search[]"value="Information.information='breakfast'"id="brea"
                        <?php if(in_array("Information.information='breakfast'", $InfoList))echo 'checked' ?>>breakfast<br>
                    </label>
                    <label for="toil">
                    <input type="checkbox"name="Info2search[]"value="Information.information='toiletries provided'"id="toil"
                        <?php if(in_array("Information.information='toiletries provided'", $InfoList))echo 'checked' ?>>toiletries provided<br>
                    </label>
                    <label for="shut">
                    <input type="checkbox"name="Info2search[]"value="Information.information='shuttle service'"id="shut"
                        <?php if(in_array("Information.information='shuttle service'", $InfoList))echo 'checked' ?>>shuttle service<br>
                    </label>
                    <input type="submit"name="search"value="search"style="float: right"id="search">
                </form>
                <br><br><hr>
                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>
                            <form  action=Homepage.php method="post">
                                <input type="submit" name="submit" value="▲" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="price" value="price">
                                Price
                                <input type="submit" name="submit" value="▼" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="price" value="price">
                            </form>
                        </th>
                        <th>Location</th>
                        <th>
                            <form  action=Homepage.php method="post">
                                <input type="submit" name="submit" value="▲" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="time" value="time">
                                Time
                                <input type="submit" name="submit" value="▼" style="padding: 5px 5px 5px 5px">
                                <input type="hidden" name="time" value="time">
                            </form>
                        </th>
                        <th>Owner</th>
                        <th>Infomation</th>
                        <th>Option</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $house_stmt = $conn->prepare(
                        "SELECT House.id, House.name, House.price, House.location, House.time, Users.name, COUNT(Information.information)
                        FROM (House JOIN Users ON House.owner_id = Users.id) LEFT JOIN Information ON Information.house_id = House.id
                        WHERE (House.id LIKE '%$searchID%')
                        AND (House.name LIKE '%$searchName%')
                        AND (House.price $searchPrice)
                        AND (House.location LIKE '%$searchLocation%')
                        AND (House.time LIKE '%$searchTime%')
                        AND (Users.name LIKE '%$searchOwner%')
                        AND ($searchInfo)
                        GROUP BY House.name
                        ORDER BY $orderby $sortway, House.id ASC"
                    );
                    $house_stmt->execute();
                    while($house_row = $house_stmt->fetch()){
                        if((!isset($Infocount)) || (isset($Infocount) && $house_row[6]==$Infocount)){?>
                        <tr>
                            <td><?php echo $house_row[0]; ?></td>
                            <td><?php echo $house_row[1]; ?></td>
                            <td><?php echo $house_row[2]; ?></td>
                            <td><?php echo $house_row[3]; ?></td>
                            <td><?php echo $house_row[4]; ?></td>
                            <td><?php echo $house_row[5]; ?></td>
                            <td><br><?php
                            $info_stmt = $conn->prepare(
                                "SELECT Information
                                FROM Information
                                WHERE house_id = '$house_row[0]' "
                            );
                            $info_stmt->execute();
                            while($info_row = $info_stmt->fetch()){
                                echo $info_row[0], "<br>";
                            }?>
                                <br>
                            </td>

                            <td valign="middle"><?php
                            $favor_stmt = $conn->prepare(
                                "SELECT favorite_id
                                FROM Favorite
                                WHERE favorite_id = '$house_row[0]' &&
                                user_id = '$cur_userid'"
                            );
                            $favor_stmt->execute();
                            ?>
                            <form action="Homepage.php" method="POST">
                            <?php if($favor_row = $favor_stmt->fetch()){
                                echo "已在我的最愛";
                            }else{ ?>
                                <input type="hidden"name="favorite_house_id"value=<?php echo $house_row[0] ?>>
                                <input type="hidden"name="user_id"value=<?php echo $cur_userid ?>>
                                <input type="submit"name="submit"value="favorite"id="favorite">
                            <?php } ?>
                                <?php if($_SESSION['UserIdentity'] == "Admin"){ ?>
                                    <br>
                                    <input type="hidden"name="id_to_delete"value=<?php echo $house_row[0] ?>>
                                    <input type="submit"name="submit"value="delete"onClick="return confirm('Are you sure to delete this house?')"id="delete">
                                <?php } ?>
                            </form>
                            </td>
                        </tr>
                        <?php } ?>
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