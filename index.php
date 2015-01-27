<?php
require 'Slim/Slim.php';
require 'plugins/NotORM.php';
\Slim\Slim::registerAutoloader();

/* CONFIG */
$app = new \Slim\Slim(
    // 'MODE' => 'developement',
    // 'DEBUG' => TRUE
    //'TEMPLATES.PATH' => './templates'
);
$dbhost   = 'localhost';
$dbuser   = 'root';
$dbpass   = '';
$dbname   = 'slim';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db = new NotORM($pdo);

/* ROUTES */
$app->get('/', function(){
    echo 'Home';
});

$app->get('/users', function() use($app, $db){
    $users = array();
    foreach ($db->users() as $user) {
        $users[]  = array(
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($users, JSON_FORCE_OBJECT);
});

$app->get('/users/:id', function($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $user = $db->users()->where('id', $id);
    if($data = $user->fetch()){
        echo json_encode(array(
            'id' => $data['id'],
            'username' => $data['username'],
            'email' => $data['email']
        ));
    }
    else{
        echo json_encode(array(
            'status' => false,
            'message' => "User ID $id does not exist"
        ));
    }
});

// Run the app
$app->run();