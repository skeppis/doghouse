<?php
/**
 * Nav-bar som anpassas beroende på om användaren är inloggad eller ej.
 */
if (isset($_SESSION['user'])) {
    echo '
        <header>
        <a href="index.php"><img src="img/logo.png" id="logo"></a>
        <h2>DogHouse</h2>
        
        <div id="header-div">
            <img src="img/icon.png" id="icon">
            <a href="profile.php?view=me">Profile</a>
            <a href="register.php?action=3" id="logout">Log out</a>
        </div>
        </header>
    ';
} else {
    echo '
        <header>
            <a href="index.php"><img src="img/logo.png" id="logo"></a>
            <h2>DogHouse</h2>
            <div>
            <a href="register.php?action=1">Log in</a>
            <a href="register.php?action=2">Register</a>
            </div>
        </header>
    ';
}
