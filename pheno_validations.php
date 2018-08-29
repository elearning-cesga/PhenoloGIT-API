<?php
include('db.php');

//POST validation to observation
$app->post('/validation', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);
	$iduser=$decoded->{'iduser'};
	$idobservation=$decoded->{'idobservation'};

    //check if this user actually userValidates this observation

    $query=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE iduser='$iduser' AND idobservation='$idobservation'");
    $query->execute();
    if($query->rowCount()!=0){

        $query=$db->prepare("DELETE FROM ".TABLE_VALIDATIONS." WHERE iduser='$iduser' AND idobservation='$idobservation'");
        if($query->execute()){

            //save log
            $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','un-validated','$data')");
            $query->execute();

            //recount validations
            $queryCount=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$idobservation'");
            $queryCount->execute();

            $response=array("message"=>"validation removed successfully","validations"=>$queryCount->rowCount(),"userValidates"=>"0");
        }else{
            $response=array("error"=>"errors trying to insert the validation");
        }

    }else{

        $query=$db->prepare("INSERT INTO ".TABLE_VALIDATIONS."(iduser,idobservation) VALUES('$iduser','$idobservation')");
        if($query->execute()){

            //save log
            $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','validated','$data')");
            $query->execute();

            //save notification
                //get the owner of the notification
                $query=$db->prepare("SELECT * FROMD ".TABLE_OBSERVATIONS." WHERE idobservation='$idobservation'");
                $query->execute();
                $observation=$query->fetch();
                $userto=$observation['iduser'];
                $query=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$iduser'");
                $query->execute();
                $user=$query->fetch();
                $username=$user['name'];
                        
                //save notification
                $idnotification=time();
                $datetime=time();
                $text='Validated your observation!';
                $query=$db->prepare("INSERT INTO ".TABLE_NOTIFICATIONS."(idnotification,datetime,userto,userfrom,text_en,action,idobservation,status) VALUES('$idnotification','$datetime','$userto','$iduser','$text','validated','$idobservation','0')");
                $query->execute();

            //count new validations
            $queryCount=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$idobservation'");
            $queryCount->execute();

            $response=array("message"=>"validations inserted successfully","validations"=>$queryCount->rowCount(),"userValidates"=>"1");
        }else{
            $response=array("error"=>"errors trying to insert the validation");
        }

    }

	echo json_encode($response);
});
?>
