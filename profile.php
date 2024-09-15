<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogHouse - My profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
        include('php/nav.php');
        include('connect.php');
        include('php/upload.php');

        // Uppdaterar profilbild
        if(isset($_GET['setavatar'])) {

            $filename = upload_file('avatar');

            try {
                $sql = "UPDATE dhUser SET avatar=? WHERE name=?";
                $data = array($filename, $_SESSION['user']);
                $stmt = $dbconn->prepare($sql);
                $stmt->execute($data);
            } catch (PDOException $f) {
                echo $f->getMessage();
            }
            header('Location: profile.php?view=me');
        } 
    ?>
    <div class="container">
        <div class="box">
            <div class="post">
                <div>
                <?php
                    include('connect.php');

                    $sql = "SELECT avatar FROM dhUser WHERE name=?";
                    
                    if(isset($_SESSION['user']) && $_GET['view'] == 'me' ) {
                        $data = array($_SESSION['user']);
                    }
                    else {
                        $data = array($_GET['view']);
                        $sql = "SELECT avatar FROM dhUser WHERE id=?";
                    }

                    $stmt = $dbconn->prepare($sql);
                    $stmt->execute($data);
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);

                    $output = '<img src="';
                    if($res['avatar'] == 'none') $output .= 'img/default.jpg';
                    else $output .= 'filer/'.htmlentities($res['avatar']);

                    $output .= '" class="image">';
                    echo $output;

                    if($_GET['view'] == 'me') {
                        echo '<form action="profile.php?setavatar=true" method="post" enctype="multipart/form-data">
                        <p>Upload profile picture:</p>
                        <input type="file" name="avatar" id="" style="color: white"><br>
                        <input type="submit" value="Uppdatera">
                        </form>';
                    }
                ?>
                
                </div>
                <div>
                    <?php
                        include('connect.php');
                        include('php/functions.php');

                        function isMyProfile() {
                            return isset($_SESSION['user']) && $_GET['view'] == 'me';
                        }

                        $sql = "SELECT * FROM dhUser WHERE name=?";

                        if(isMyProfile()) $data = array($_SESSION['user']);
                        else $data = array(getUserName($dbconn, $_GET['view']));

                        $stmt = $dbconn->prepare($sql);
                        $stmt->execute($data);
                        $res = $stmt->fetch(PDO::FETCH_ASSOC);

                        $output = '<p>Username: '.htmlentities($res['name']).'</p>';

                        if(isMyProfile() == true) {
                            $output .= '<p>Mail: '.htmlentities($res['email']).'</p><br>';
                        }
                        
                        $output .= '<p>Member since: '.htmlentities($res['reg']).'</p>';

                        $points = 0;
                        $sql = "SELECT * FROM dhThreads 
                        INNER JOIN dhVotes ON dhThreads.id = dhVotes.thread_id
                        WHERE dhThreads.user=?";
                        
                        if(isMyProfile() == true) $data = array($_SESSION['user']);
                        else $data = array($res['name']);
                        
                        $stmt = $dbconn->prepare($sql);
                        $stmt->execute($data);
                        while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $points += htmlentities($res['votes']);
                        }
                        $output .= '<p>Points: '.$points.'</p>';

                        echo $output;
                    ?>  
                </div>
            </div>
        </div>
    </div>
</body>
</html>