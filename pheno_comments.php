<?php
include ('db.php');

//GET comments for given observation
$app->get('/comment/:idobservation', function ($idobservation) use($app,$db) {

        $query=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$idobservation'");   
	$query->execute();

	if($query->rowCount()>0){

                while($comment=$query->fetch()){

                        $idUser=$comment['iduser'];
                        //Get the user details
                        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
                        $queryUser->execute();
                        $user=$queryUser->fetch();

                        $commentsArray[]=array(
                                'iduser'=>$comment['iduser'],
                                'username'=>$user['name'],
                                'userpicture'=>$user['picture'],
                                'comment'=>$comment['comment'],
                                'id'=>$comment['idobserva_comment']
                        );
        
                }
        }else{
                        $commentsArray[]=array('error'=>'no comments for observation '.$idobservation); 
        }

        $response=array('count'=>$query->rowCount(),'comments'=>$commentsArray);
        echo json_encode($response);


})->name('getComments');

//POST new comment
$app->post('/comment', function () use($app,$db) {

   	$data = $app->request->getBody();
   	$decoded = json_decode($data);
	$iduser=$decoded->{'iduser'};
	$comment=$decoded->{'comment'};
	$idobservation=$decoded->{'idobservation'};

	$query=$db->prepare("INSERT INTO ".TABLE_COMMENTS."(iduser,comment,idobservation) VALUES('$iduser','$comment','$idobservation')");
	if($query->execute()){
		$response=array("message"=>"comment inserted successfully");
                
                //save log
                $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','commented','$data')");
                $query->execute();

                //save notification
                //get the owner of the notification
                $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idobservation='$idobservation'");
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
                $text='Commented on your observation!';
                $query=$db->prepare("INSERT INTO ".TABLE_NOTIFICATIONS."(idnotification,datetime,userto,userfrom,text_en,action,idobservation,status) VALUES('$idnotification','$datetime','$userto','$iduser','$text','comment','$idobservation','0')");
                $query->execute();

	}else{
		$response=array("error"=>"errors trying to insert the comment");
                
                //save log
                $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$iduser','ERROR commenting','$data')");
                $query->execute();
	}

	echo json_encode($response);
});

$app->options('/comment/:id', function ($id) use($app,$db) {
        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

//Delete comment
$app->delete('/comment/:id',function($id) use($app,$db){

	//get the observationid from comment
	$query=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobserva_comment='$id'");
	$query->execute();
	$comment=$query->fetch();
	$observationid=$comment['idobservation'];

        $query=$db->prepare("DELETE FROM ".TABLE_COMMENTS." WHERE idobserva_comment='$id'");
        $query->execute();
        $route= $app->router->getNamedRoute('getComments');
	$route->setParams(array($observationid));
	$route->dispatch(); //call GET observation's comments
});
?>
