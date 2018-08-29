<?php
header('Access-Control-Allow-Origin: *');

//include slim
include('vendor/autoload.php');

//include database file
include('db.php');

$app = new \Slim\Slim();

include('pheno_observations.php');
include('pheno_comments.php');
include('pheno_users.php');
include('pheno_likes.php');
include('pheno_validations.php');
include('pheno_species.php');
include('pheno_stages.php');
include('pheno_logs.php');
include('pheno_notifications.php');
include('pheno_watson.php');
include('pheno_schools.php');
include('pheno_bestExample.php');

$app->run();

?>
