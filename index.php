<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// Create the base app

$app = new \Slim\App;

// Define the app routes
$app->get('/', 'home');
$app->get('/get/emojis', 'getEmojis');
$app->post('/create/emoji', 'createEmoji');

// Run the application
$app->run();

// Define the API functions

function home(){
	echo 'No direct access, sorry :(';
}

function createEmoji($dbh) {
    $request = Slim::getInstance()->request();
    $emoji = json_decode($request->getBody());
    $sql = "INSERT INTO checkins (lattitude, longitud, emoji) VALUES (:lattitude, :longitud, :emoji)";
    try {
		$db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("lattitude", $emoji->lattitude);
        $stmt->bindParam("longitud", $emoji->longitud);
        $stmt->bindParam("emoji", $emoji->emoji);
        $stmt->execute();
        $emoji->id = $db->lastInsertId();
        $stmt = null;
		$db = null;
        echo json_encode($emoji);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getEmojis($dbh){
	$sql = "SELECT * FROM checkins";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $emojis = $stmt->fetchObject();
		$stmt = null;
        $db = null;
        echo json_encode($emojis);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getConnection() {
	$user = "emoji";
	$pass = "";
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=emoji', $user, $pass);
		return $dbh;
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}