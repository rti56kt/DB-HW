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
                        alert("Something went wrong. 1");
                        window.location.replace("Index.php");
                    </script>
                    </body>
                </html>
EOT;
            }
            if(!isset($_SESSION["searchID"])) $_SESSION["searchID"]="";
            if(!isset($_SESSION["searchName"])) $_SESSION["searchName"]="";
            if(!isset($_SESSION["searchPrice"])) $_SESSION["searchPrice"]="";
            if(!isset($_SESSION["searchLocation"])) $_SESSION["searchLocation"]="OR location_id IS NULL";
            if(!isset($_SESSION["searchTime"])) $_SESSION["searchTime"]="";
            if(!isset($_SESSION["searchCin"])) $_SESSION["searchCin"]="";
            if(!isset($_SESSION["searchCout"])) $_SESSION["searchCout"]="";
            if(!isset($_SESSION["searchOwner"])) $_SESSION["searchOwner"]="";
            if(!isset($_SESSION["InfoList"])) $_SESSION["InfoList"]="";
            if(!isset($_SESSION["searchInfo"])) $_SESSION["searchInfo"]="1";
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
                }elseif($_POST["submit"]=="search"){
                    $today = date("Y/m/d");
                    if(strtotime($_POST["Checkin2search"]) >= strtotime($_POST["Checkout2search"]))
                        echo "<script>alert('退房日期必須比入住日期還晚！！'); window.location.replace('Homepage.php');</script>";
                    elseif(strtotime($_POST["Checkin2search"]) <= strtotime($today))
                        echo "<script>alert('入住日期必須晚於今天！！'); window.location.replace('Homepage.php');</script>";
                    else{
                        $_SESSION["searchID"]=$_POST["ID2search"];
                        $_SESSION["searchName"]=$_POST["Name2search"];
                        if(isset($_POST["Price2search"])) $_SESSION["searchPrice"]=$_POST["Price2search"];
                        if(isset($_POST["Location2search"])) $_SESSION["searchLocation"]=$_POST["Location2search"];
                        $_SESSION["searchTime"]=$_POST["Time2search"];
                        $_SESSION["searchCin"]=$_POST["Checkin2search"];
                        $_SESSION["searchCout"]=$_POST["Checkout2search"];
                        $_SESSION["searchOwner"]=$_POST["Owner2search"];
                        if(isset($_POST["Info2search"])){
                            $_SESSION["InfoList"]=$_POST["Info2search"];
                            $_SESSION["searchInfo"]=implode(" OR ", $_POST["Info2search"]);
                            $Infocount=count($_POST["Info2search"]);
                        }else{
                            $_SESSION["InfoList"]="";
                        }
                    }
                    $_SESSION["totaldata"]=0;
                }elseif($_POST["submit"]=="book"){
                    $check_in = $_POST["check_in"];
                    $check_out = $_POST["check_out"];
                    if($check_in!="1911-01-01"&&$check_out!="1911-01-02"){
                        $book_house_id = $_POST["book_house_id"];
                        $user_id = $_SESSION['UserID'];

                        $book_stmt = $conn->prepare("INSERT INTO Book(house_id, check_in, check_out, visitor_id) VALUES(:house_id, :check_in, :check_out, :visitor_id) ");
        
                        $book_stmt->bindParam(':house_id', $book_house_id);
                        $book_stmt->bindParam(':check_in', $check_in);
                        $book_stmt->bindParam(':check_out', $check_out);
                        $book_stmt->bindParam(':visitor_id', $user_id);
                        $book_stmt->execute();
                        echo "<script>alert('Success.');</script>";
                    }elseif($check_in=="1911-01-01"&&$check_out=="1911-01-02"){
                        echo "<script>alert('Please set the check-in and check-out time first.');</script>";
                    }
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
            $searchID=$_SESSION["searchID"];
            $searchName=$_SESSION["searchName"];
            $searchPrice=$_SESSION["searchPrice"];
            $searchLocation=$_SESSION["searchLocation"];
            $searchTime=$_SESSION["searchTime"];
            $searchCin=$_SESSION["searchCin"];
            $searchCout=$_SESSION["searchCout"];
            $searchOwner=$_SESSION["searchOwner"];
            $InfoList=$_SESSION["InfoList"];
            if($_SESSION["InfoList"]!=""){
                $searchInfo=implode(" OR ", $_SESSION["InfoList"]);
                $Infocount=count($_SESSION["InfoList"]);
            }
            else $searchInfo=1;
?>

<!DOCTYPE html>
    <html>
        <head>
            <title>DB_HW3 Homepage</title>
            <meta charset="UTF-8">
            <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
            <link type="text/css" rel="stylesheet" href="Design.css">
        </head>
        <body>
            <div class="adminpage">
                <input type="button"onClick="window.location.replace('Index.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Logout">
                <input type="button"onClick="window.location.replace('FavoriteList.php');"style="float: right;margin: 0px 0px 0px 5px;"value="My Favorite">
                <input type="button"onClick="window.location.replace('House_m.php');"style="float: right;margin: 0px 0px 0px 5px;"value="House Management">
                <input type="button"onClick="window.location.replace('House_page.php');"style="float: right;margin: 0px 0px 0px 5px;"value="House page">
                <input type="button"onClick="window.location.replace('Order_Page.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Order Page">
                <?php if($_SESSION['UserIdentity'] == "Admin"){ ?>
                    <br><br>
                    <input type="button"onClick="window.location.replace('Member_m.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Members Management">
                    <input type="button"onClick="window.location.replace('Locations_m.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Locations Management">
                    <input type="button"onClick="window.location.replace('Information_m.php');"style="float: right;margin: 0px 0px 0px 5px;"value="Information Management">
                <?php } ?>
                <br>
                <h1 style="font-size:40px">Homepage</h1><br>
                <p align="left"style="font-size:20px">Search:</p>

                <form action="Homepage.php" method="POST" align="left">
                    <input type="number"name="ID2search"placeholder="ID"value="<?php echo $searchID ?>">
                    <input type="text"name="Name2search"placeholder="Name"value="<?php echo $searchName ?>"style="margin: 0px 0px 0px 5px">
                    <select name="Price2search"id="p_select"style="margin: 0px 0px 0px 5px">
                        <option value=""<?php if($searchPrice=="")echo 'selected' ?> disabled hidden>Price</option>
                        <option value="">None</option>
                        <option value="BETWEEN 0 AND 3000"<?php if($searchPrice=="BETWEEN 0 AND 3000")echo 'selected' ?>>~3000</option>
                        <option value="BETWEEN 3000 AND 6000"<?php if($searchPrice=="BETWEEN 3000 AND 6000")echo 'selected' ?>>3000~6000</option>
                        <option value="BETWEEN 6000 AND 12000"<?php if($searchPrice=="BETWEEN 6000 AND 12000")echo 'selected' ?>>6000~12000</option>
                        <option value="NOT BETWEEN 0 AND 12000"<?php if($searchPrice=="NOT BETWEEN 0 AND 12000")echo 'selected' ?>>12000~</option>
                    </select>
                    <select name="Location2search"id="l_select"style="margin: 0px 0px 0px 5px">
                        <option value="OR location_id IS NULL"<?php if($searchLocation=="OR location_id IS NULL")echo 'selected' ?> disabled hidden>Location</option>
                        <option value="OR location_id IS NULL">None</option>
                        <?php
                        $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

                        $location_stmt = $db->prepare("SELECT id, location FROM Locations");
                        $location_stmt ->execute();
                        while($location_row = $location_stmt->fetch()){
                            echo "<option value='=$location_row[0]' ";
                            if($searchLocation=="=$location_row[0]")echo "selected";
                            echo ">"; 
                            echo $location_row[1];
                            echo "</option>"; 
                        }
                        ?>
                    </select>
                    <input type="text"name="Time2search"placeholder="Last Updated"onfocus="(this.type='date')"onblur="(this.type='text')"value="<?php echo $searchTime ?>"style="margin: 0px 0px 0px 5px">
                    <input type="text"name="Owner2search"placeholder="Owner"value="<?php echo $searchOwner ?>"style="margin: 0px 0px 0px 5px">
                    <br><br>
                    <input type="text"name="Checkin2search"placeholder="Checkin Time"onfocus="(this.type='date')"onblur="(this.type='text')"value="<?php echo $searchCin ?>"style="margin: 0px 0px 0px 5px" required>
                    <input type="text"name="Checkout2search"placeholder="Checkout Time"onfocus="(this.type='date')"onblur="(this.type='text')"value="<?php echo $searchCout ?>"style="margin: 0px 0px 0px 5px" required>
                    <br>
                    <?php
                        $cnt = 0;
                        $db = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_password);
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

                        $info_stmt = $db->prepare("SELECT id, information FROM Information");
                        $info_stmt ->execute();
                        while($info_row = $info_stmt->fetch()){
                            echo "<input type='checkbox' name='Info2search[]' value='House_Info_mapping.info_id=$info_row[0]'";
                            if(strpos($InfoList, "House_Info_mapping.info_id=$info_row[0]"))echo 'checked';
                            echo ">";
                            echo " $info_row[1]";
                            echo "<br>";
                        }
                    ?>
                    <input type="submit"name="submit"value="search"style="float: right"id="search">
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
                    if($searchCin==""&&$searchCout==""){
                        $searchCin="1911-01-01";
                        $searchCout="1911-01-02";
                    }
                    $stmt = "SELECT House.id, House.name, House.price, House.location_id, House.time, Users.name, 
                                COUNT(House_Info_mapping.info_id), Book.check_in, Book.check_out
                            FROM ((House JOIN Users ON House.owner_id = Users.id)
                            LEFT JOIN Book ON Book.house_id = House.id)
                            LEFT JOIN House_Info_mapping ON House_Info_mapping.house_id = House.id
                            WHERE (House.id LIKE '%$searchID%')
                            AND (House.name LIKE '%$searchName%')
                            AND (House.price $searchPrice)
                            AND (House.location_id $searchLocation)
                            AND (House.time LIKE '%$searchTime%')
                            AND (Users.name LIKE '%$searchOwner%')
                            AND ($searchInfo)
                            AND (CASE WHEN Book.check_in IS NULL THEN 1
                                WHEN '$searchCin'>=Book.check_out OR '$searchCout'<=Book.check_in THEN 1
                                END)
                            GROUP BY House.name";
                    if(isset($Infocount)) $stmt = $stmt.' HAVING COUNT(House_Info_mapping.info_id)='.$Infocount;
                    $stmt = $stmt.' ORDER BY '.$orderby.' '.$sortway.' , House.id ASC ';
                    $house_stmt = $conn->prepare($stmt);
                    $house_stmt->execute();

                    $data_nums = $house_stmt->rowCount();
                    $per = 5;
                    $pages = ceil($data_nums/$per);
                    if (!isset($_GET["page"])){
                        $page=1;
                    } else {
                        $page = intval($_GET["page"]);
                        $page = ($page > 0) ? $page : 1;
                        $page = ($pages > $page) ? $page : $pages;
                    }
                    $start = ($page-1)*$per;
                    $stmt = $stmt.' LIMIT '.$start.', '.$per;

                    $house_stmt = $conn->prepare($stmt);
                    $house_stmt->execute();
                    while($house_row = $house_stmt->fetch()){
                        if((!isset($Infocount)) || (isset($Infocount) && $house_row[6]==$Infocount)){?>
                        <tr>
                            <td><?php echo $house_row[0]; ?></td>
                            <td><?php echo $house_row[1]; ?></td>
                            <td><?php echo $house_row[2]; ?></td>
                            <td><?php
                                $locid_stmt = $conn->prepare(
                                    "SELECT location_id FROM House WHERE id = '$house_row[0]'"
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
                            <td><?php echo $house_row[4]; ?></td>
                            <td><?php echo $house_row[5]; ?></td>
                            <td><br><?php
                                $info_stmt = $conn->prepare(
                                    "SELECT Information.information
                                    FROM House_Info_mapping
                                    JOIN Information ON House_Info_mapping.info_id = Information.id
                                    WHERE House_Info_mapping.house_id = '$house_row[0]' "
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
                                <input type="hidden"name="book_house_id"value=<?php echo $house_row[0] ?>>
                                <input type="hidden"name="check_in"value=<?php echo $searchCin ?>>
                                <input type="hidden"name="check_out"value=<?php echo $searchCout ?>>
                                <input type="submit"name="submit"value="book"id="book">
                            <br>
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
                <?php
                    echo '共 '.$data_nums.' 筆-在 '.$page.' 頁-共 '.$pages.' 頁';
                    echo '<br>';
                    echo "第 ";
                    for( $i=1 ; $i<=$pages ; $i++ ) {
                        if($i == $page) echo ' '.$i.' ';
                        else echo "<a href=?page=".$i.">".$i."</a> ";
                    } 
                    echo " 頁";
                ?>
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
                alert("Something went wrong. 2");
                window.location.replace("Index.php");
            </script>
            </body>
        </html>
EOT;
    }
?>