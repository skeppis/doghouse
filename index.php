<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogHouse - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    include('connect.php');
    include('php/functions.php');

    createTables($dbconn);
    insertBoards($dbconn);
    
    include('php/nav.php');
    ?>
    <div id="hero">
        <div class="box">
            <h3>What is DogHouse?</h3>
            <p>DogHouse is an image-based forum with the possibility of remaining completely anonymous.</p>
            <table>
                <tr>
                    <th><h3>Boards</h3></th>
                    <th><h3># of posts</h3></th>
                </tr>
                <?php
                    include('connect.php');

                    // Visar och skapar länkar till alla rum
                    try {
                        $sql = "SELECT name, size FROM dhBoards ORDER BY name";
                        $stmt = $dbconn->prepare($sql);
                        $stmt->execute();
                        while($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>
                                <td><a href="view.php?board='.htmlentities($res['name']).'">'.htmlentities($res['name']).'</a></td>
                                <td><p>'.htmlentities($res['size']).'</p></td>
                            </tr>';
                        }
                    } catch (PDOException $f) {
                        echo $f->getMessage();
                    }
                ?>
            </table>
            <p class="comment">
                **Create an account to use the vote function, make comments and get your own profile.**
            </p>
        </div>
    </div>
    <?php
        include('php/bg.php')
    ?>
</body>

</html>