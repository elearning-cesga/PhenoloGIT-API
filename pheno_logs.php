<?php
include ('db.php');

//POST custom log
$app->post('/log', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);

    //save log
    $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('0','custom log','$data')");
    $query->execute();

    echo 'log posted!';

});
?>