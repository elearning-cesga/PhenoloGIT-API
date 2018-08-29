<?php
include('db.php');

//POST mark obseration to best example
$app->post('/bestexample', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);
	$iduser=$decoded->{'iduser'};
	$idobservation=$decoded->{'idobservation'};

    $query=$db->prepare("UPDATE ".TABLE_OBSERVATIONS." SET best_sample='1' WHERE idobservation='$idobservation' ");
    if($query->execute()){

        //save log
        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','marked as best example','$data')");
        $query->execute();

        //save internal notification
        
        //get the owner of the notification
        $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idobservation='$idobservation'");
        $query->execute();
            
        $observation=$query->fetch();
        $userto=$observation['iduser'];
        $query=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$iduser'");
        $query->execute();
        
        $user=$query->fetch();
        $username=$user['name'];
                
        //save internal notification
        $idnotification=time();
        $datetime=time();
        $text='Marked your observation as best example!';
        $query=$db->prepare("INSERT INTO ".TABLE_NOTIFICATIONS."(idnotification,datetime,userto,userfrom,text_en,action,idobservation,status) VALUES('$idnotification','$datetime','$userto','$iduser','$text','like','$idobservation','0')");
        $query->execute();

        $response=array("message"=>"marked as best example successfully");
        
    }else{
        $response=array("error"=>"errors trying to insert the best example");
    }

	echo json_encode($response);

});
?>
