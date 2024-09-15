<?php
/**
 * Skapar samtliga tabeller om de inte redan finns
 */
function createTables($dbconn)
{
    try {
        $createUsers = 'CREATE TABLE IF NOT EXISTS dhUser (
            id INT(6) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
            email VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            reg TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            avatar VARCHAR(255) DEFAULT "none",
            code INT(25)
        )';
        $createThreads = 'CREATE TABLE IF NOT EXISTS dhThreads (
            id INT(6) AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            text VARCHAR(255) NOT NULL,
            user VARCHAR(15) NOT NULL DEFAULT "anonymous",
            time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            filename VARCHAR(255) DEFAULT "none",
            board VARCHAR(15) NOT NULL
        )';
        $createBoards = "CREATE TABLE IF NOT EXISTS dhBoards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(25) NOT NULL,
            size INT NOT NULL DEFAULT 0
        )";
        $createVotes = "CREATE TABLE IF NOT EXISTS dhVotes (
            thread_id INT(6) AUTO_INCREMENT PRIMARY KEY,
            votes INT NOT NULL DEFAULT 0
        )";
        $createComments = "CREATE TABLE IF NOT EXISTS dhComments (
            this_id INT AUTO_INCREMENT PRIMARY KEY,
            thread_id INT NOT NULL,
            text VARCHAR(255) NOT NULL,
            user_id INT NOT NULL DEFAULT 0,
            likes INT NOT NULL DEFAULT 0
        )";
        $createInteractions = 'CREATE TABLE IF NOT EXISTS dhInteractions (
            user_id INT NOT NULL,
            thread_id INT NOT NULL,
            type INT NOT NULL
        )';
        $createCommentInteractions = "CREATE TABLE IF NOT EXISTS dhCommentLikes (
            user_id INT NOT NULL,
            comment_id INT NOT NULL,
            value INT NOT NULL
        )";
        $createCodes = "CREATE TABLE IF NOT EXISTS dhCodes (
            user_id INT NOT NULL,
            code INT(25),
            reg TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $dbconn->exec($createUsers);
        $dbconn->exec($createThreads);
        $dbconn->exec($createBoards);
        $dbconn->exec($createVotes);
        $dbconn->exec($createComments);
        $dbconn->exec($createInteractions);
        $dbconn->exec($createCommentInteractions);
        $dbconn->exec($createCodes);
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

/**
 * Skapar alla rum om de inte redan existerar
*/
function insertBoards($conn)
{
    $boards = array('gaming','TV/movies','politics','sports','music','food','art','technology','science','animals','nature');
    try {
        $sql = "SELECT COUNT(*) FROM dhBoards";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_NUM);
        if ($res[0] == 0) {
            $sql = "INSERT INTO dhBoards (name) VALUES ";

            for($x = 0;$x < count($boards);$x++) {
                if($x == count($boards) - 1) $sql .= '("'.$boards[$x].'")';
                else $sql .= '("'.$boards[$x].'"),';
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

/***
 * Klass för hantering av rum
 */
class Board
{
    public $name;
    public $size;

    public function __construct($name)
    {
        $this->name = $name;
    }

    // Bestämmer hur många inlägg som finns i ett rum
    public function setNewSize($conn) {
        try {
            $sql = "SELECT size FROM dhBoards WHERE name=?";
            $stmt = $conn->prepare($sql);
            $data = array($this->name);
            $stmt->execute($data);
            $rows = $stmt->fetch(PDO::FETCH_NUM);
            $size = $rows[0];
            $this->size = $size - 1;
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
    }

    // Uppdaterar tabellen för rum där antalet inlägg lagras
    public function editSelf($conn) {
        try {
            $sql = "UPDATE dhBoards SET size=? WHERE name=?";
            $data = array($this->size, $this->name);
            $stmt = $conn->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $f) {
            echo $f->getMessage();
        }
    }
}

/**
 * Hämtar användar-ID från namn
 */
function getUserID($conn, $name) {
    try {
        $sql = "SELECT id FROM dhUser WHERE name=?";
        $data = array($name);
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['id'];
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}

/***
 * Hämtar användarnamn från id
 */
function getUserName($conn, $id) {
    try {
        $sql = "SELECT name FROM dhUser WHERE id=?";
        $data = array($id);
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['name'];
    } catch (PDOException $f) {
        echo $f->getMessage();
    }
}