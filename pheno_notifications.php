<?php
include ('db.php');

//POST notification
$app->post('/notification', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);
    $to=$decode->{'to'};
    $text=$decode->{'text'};
    $action=$decode->{'action'};
    $status=$decode->{'unread'};
    $idnotification=time();
    $datetime=time();
    $from=$decode->{'from'};

    //save notification
    $query=$db->prepare("INSERT INTO ".TABLE_NOTIFICATIONS."(idnotification,datetime,userto,userfrom,text_en,action,status) VALUES('$idnotification','$datetime','$to','$from','$text','$action','$status')");
    $query->execute();

    echo 'notification posted!';

});

//update notification  details
$app->options('/notification/read', function() use($app,$db){

        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

//Mark as read this notification
$app->put('/notification/read', function () use($app,$db) {

    $data = $app->request->getBody();
    $decoded = json_decode($data);
    $idnotification=$decoded->{'idnotification'};
    $iduser=$decoded->{'to'};

    //save notification
    $query=$db->prepare("UPDATE ".TABLE_NOTIFICATIONS." SET status='1' WHERE idnotification='$idnotification'");
    $query->execute();

    $route=$app->router->getNamedRoute('getNotifications');
    $route->setParams(array($iduser));
    $route->dispatch(); //call GET notifications


});

//update notifications 
$app->options('/notification/read/all', function() use($app,$db){

        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

//Mark as read all notifications for a user
$app->put('/notification/read/all', function () use($app,$db) {

    $data = $app->request->getBody();
    $decoded = json_decode($data);
    $iduser=$decoded->{'iduser'};

    //save notification
    $query=$db->prepare("UPDATE ".TABLE_NOTIFICATIONS." SET status='1' WHERE userto='$iduser'");
    $query->execute();

    $route=$app->router->getNamedRoute('getNotifications');
    $route->setParams(array($iduser));
    $route->dispatch(); //call GET notifications


});

//GET notification for user
$app->get('/notification/:user', function ($iduser) use($app,$db) {

    //load notifications
    $query=$db->prepare("SELECT * FROM ".TABLE_NOTIFICATIONS." WHERE userto='$iduser' AND status='0' ORDER BY datetime DESC");
    $query->execute();

    if($query->rowCount()>0){

        while($notification=$query->fetch()){

            $userfrom=$notification['userfrom'];
                $userto=$notification['userto'];

            $queryuser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$userfrom'");
            $queryuser->execute();
            $user=$queryuser->fetch();

            //formatting date
            $date=$notification['datetime'];
            $now=time();
            $datetoshow=date('d-m-Y',$notification['datetime']);
            if(($now - $date > 3000)&&($now - $date < 86400)) $datetoshow='Today';
            if($now - $date < 3000) $datetoshow='1 hour ago';
            if($now - $date < 300) $datetoshow='5 minutes ago';
            if($now - $date >  86400) $datetoshow= date('d \o\f M', $date );


            $notificationsArray[]=array(
                'date'=>$datetoshow,
                'text'=>$notification['text_en'],
                'from'=>$userfrom,
                'to'=>$userto,
                'userfrompicture'=>$user['picture'],
                'userfromname'=>$user['name'],
                'action'=>$notification['action'],
                'idobservation'=>$notification['idobservation'],
                'idnotification'=>$notification['idnotification']
            );
        }


    }else{

         $notificationsArray=array('error'=>'1');

    }

    echo json_encode($notificationsArray);

})->name('getNotifications');

?>