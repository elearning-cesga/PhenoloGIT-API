<?php
include ('db.php');

//POST like to observation
$app->post('/like', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);
	$iduser=$decoded->{'iduser'};
	$idobservation=$decoded->{'idobservation'};

    //check if this user actually liked this observation

    $query=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE iduser='$iduser' AND idobservation='$idobservation'");
    $query->execute();
    if($query->rowCount()!=0){

        $query=$db->prepare("DELETE FROM ".TABLE_LIKES." WHERE iduser='$iduser' AND idobservation='$idobservation'");
        if($query->execute()){

            //save log
            $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','unlikes','$data')");
            $query->execute();

            //recount likes
            $queryCount=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$idobservation'");
            $queryCount->execute();

            $response=array("message"=>"liked removed successfully","likes"=>$queryCount->rowCount(),"liked"=>"0");
        }else{
            $response=array("error"=>"errors trying to insert the like");
        }

    }else{

        $query=$db->prepare("INSERT INTO ".TABLE_LIKES."(iduser,idobservation) VALUES('$iduser','$idobservation')");
        if($query->execute()){

            //save log
            $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','likes','$data')");
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
            $text='Liked your observation!';
            $query=$db->prepare("INSERT INTO ".TABLE_NOTIFICATIONS."(idnotification,datetime,userto,userfrom,text_en,action,idobservation,status) VALUES('$idnotification','$datetime','$userto','$iduser','$text','like','$idobservation','0')");
            $query->execute();


            //count new likes
            $queryCount=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$idobservation'");
            $queryCount->execute();

            $response=array("message"=>"liked inserted successfully","likes"=>$queryCount->rowCount(),"liked"=>"1");
        }else{
            $response=array("error"=>"errors trying to insert the like");
        }

    }

	echo json_encode($response);

});
?>
