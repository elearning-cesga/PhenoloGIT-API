<?php
include("db.php");

use \Firebase\JWT\JWT;

//GET user details
$app->get('/user/:iduser', function ($iduser) use($app,$db) {

	$query=$db->prepare("SELECT * FROMd ".TABLE_USERS." WHERE iduser='$iduser'");   
	$query->execute();

	if($query->rowCount()>0){

		while($user=$query->fetch()){

			$userArray[]=array(
				'name'=>$user['name'],
				'username'=>$user['name_user'],
                                'picture'=>$user['picture'],
                                'email'=>$user['email'],
				'userid'=>$user['iduser'],
				'type'=>$user['idtipuser'],
                                'lang'=>$user['lang'],
                                'idschool'=>$user['idschool']
			);
	
		}
	}else{
	
		$userArray[]=array('error'=>'no user for this id '.$iduser);	

	}

	echo json_encode($userArray);

});

//GET users by school
$app->get('/user/school/:idschool', function ($idschool) use($app,$db) {

        if($idschool==0){ //if 0 school selected show all users
                $query=$db->prepare("SELECT * FROM ".TABLE_USERS."");   
        }else{ //show only users from this school
                $query=$db->prepare("SELECT * FROMd ".TABLE_USERS." WHERE idschool='$idschool'");   
        }
	
	$query->execute();

	if($query->rowCount()>0){

		while($user=$query->fetch()){

			$userArray[]=array(
				'name'=>$user['name'],
				'username'=>$user['name_user'],
                                'picture'=>$user['picture'],
                                'email'=>$user['email'],
				'userid'=>$user['iduser'],
				'type'=>$user['idtipuser'],
                                'lang'=>$user['lang']
			);
	
		}
	}else{
	
		$userArray[]=array('error'=>'no user for this id '.$iduser);	

	}

	echo json_encode($userArray);

});

//POST create user
$app->post('/user', function () use($app,$db) {

        $name=$app->request->post('name');
        $username=$app->request->post('username');
        $passwd=$app->request->post('passwd');
        $email=$app->request->post('email');
        $idtipuser=$app->request->post('idtipuser');
        $idschool=$app->request->post('idschool');

        //getting country based on school id
        $lang='en'; //default lang
        $query=$db->prepare("SELECT * FROM ".TABLE_SCHOOLS." WHERE idschool='$idschool'");
        $query->execute();

        $schoolquery=$query->fetch();

        if($query->rowCount()>0){
                $lang=strtolower($schoolquery['cod_iso']);
                if($lang=='uk') $lang='en';
                if($lang=='es') $lang='gl'; //galician instead of spain
        }

        //delete device token for push notifications

	//check if the email exists
	$emailquery=$db->prepare("SELECT * FROMd ".TABLE_USERS." WHERE email='$email'");
        $emailquery->execute();
 	if($emailquery->rowCount()>0){
                $errors=1; //email exists
        }else{
		$query=$db->prepare("INSERT INTOd ".TABLE_USERS." (name,name_user,passw,email,idtipuser,idschool,picture,lang) VALUES ('$name','$username','$passwd','$email','$idtipuser','$idschool','male3-512.png','$lang')");
		if($query->execute()){
		        $errors=0;
		}else{
		        $errors=1;
		}
	}

        echo json_encode(array('errors'=>$errors));

});

//POST logout
$app->post('/user/logout', function () use($app,$db) {

        $data = $app->request->getBody();
        $decoded = json_decode($data);
        $iduser=$decoded->{'iduser'};
        $idtoken=$decoded->{'idtoken'};

});

//POST login
$app->post('/user/login', function () use($app,$db) {

        $data = $app->request->getBody();
        $decoded = json_decode($data);

        if(isset($decoded->{'username'})){  //if is sent from the app
                $username=$decoded->{'username'};
                $passwd=md5($decoded->{'passwd'});
        }else{ //if is sent in a common form
                $username=$app->request->post('username');
                $passwd=$app->request->post('passwd');
        }
               
        $deviceToken='nullfromiOS';
        if(isset($decoded->{'deviceToken'})){
                $deviceToken=$decoded->{'deviceToken'};
        }

        $query=$db->prepare("SELECT * FROMd ".TABLE_USERS." WHERE name_user='$username' AND passw='$passwd'");
        $query->execute();

        $user=$query->fetch();

    if($query->rowCount()>0){

        $id=time();
        $idUser=$user['iduser'];

        //save log
        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('0','loggedin','$data')");
        $query->execute();

	$key = "your_secret_key";
        $token_data = array(
                "username" => $username,
                "passwd" => $passwd,
        );

	//generats jwt token
	$token=JWT::encode($token_data, $key);

	//add token to DB
	$query=$db->prepare("UPDATEd ".TABLE_USERS." SET token='$token' WHERE name_user='$username'");
	$query->execute();
	
	$response[]=array(
    	        'name'=>$user['name'],
                'username'=>$user['name_user'],
                'picture'=>$user['picture'],
                'email'=>$user['email'],
                'userid'=>(string)$user['iduser'],
		'token'=>$token,
		'type'=>$user['idtipuser'],
                'lang'=>$user['lang'],
                'school'=>$user['idschool']
        );
  
    }else{
        $response=array('token'=>'null');

        //save log
        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('0','error login in','$data')");
        $query->execute();
    }

	echo json_encode($response);
});

//update user details
$app->options('/user', function() use($app,$db){

        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

$app->put('/user', function() use($app,$db){
        
                //get data by post (js)
            if(null!=$app->request->post('id')){
                $data = $app->request->post('id');
                $user_id=$app->request->post('id');
                $user_name=$app->request->post('name');
                $user_picture=$app->request->post('user_picture');
                $user_lang=$app->request->post('user_lang');
        
            }else{
                //get data by body parse (app)
                $data = $app->request->getBody();
                $decoded = json_decode($data);
        
                $user_id=$decoded->{'id'};
                $user_name=$decoded->{'name'};
                $user_picture=$decoded->{'picture'};
                $user_lang=$decoded->{'lang'};
            }
        
                //change email if set
                if(null!=$app->request->post('email')){
        
                    $user_email=$app->request->post('email');
        
                        $queryEmail=$db->prepare("UPDATEd ".TABLE_USERS." SET email='$user_email' WHERE iduser='$user_id'");
                        $queryEmail->execute();
        
                }
                
                //change password if set
                if((isset($decoded->{'passwd'}))||(null!=$app->request->post('password'))){
                        if(isset($decoded->{'passwd'})){
                            $user_passwd=md5($decoded->{'passwd'});
                        }
                        if(null!=$app->request->post('password')){
                            $user_passwd=md5($app->request->post('password'));
                        }
                        
                        $queryPassword=$db->prepare("UPDATEd ".TABLE_USERS." SET passw='$user_passwd' WHERE iduser='$user_id'");
                        $queryPassword->execute();
        
                        //save log
                        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$user_id','changed his password','$data')");
                        $query->execute();
                }
        
                $query=$db->prepare("UPDATEd ".TABLE_USERS." SET name='$user_name', picture='$user_picture', lang='$user_lang' WHERE iduser='$user_id'");
                if($query->execute()){
                        $response=array('errors'=>'0');
                        
                        //save log
                        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$user_id','changed his profile','$data')");
                        $query->execute();
                }else{
                        $response=array('errors'=>'1');
                }
        
                echo json_encode($response);
        
        });

//delete user

$app->delete('/user', function() use($app,$db){
	 
        //get the id
        $user_id=$app->request->post('id');
       
        $query=$db->prepare("DELETE FROMd ".TABLE_USERS." WHERE iduser='$user_id'");
       
        if($query->execute()){
                //save log
                $querylog=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$user_id','user deleted','Deleted from control Panel')");
                $querylog->execute();
                $response=array('errors'=>'0');
               
        }else{
                $response=array('errors'=>'1');
        }

        echo json_encode($response);

});

//update user details
$app->options('/user/password', function() use($app,$db){

        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

//POST reset password
$app->put('/user/password', function () use($app,$db) {
        
        $data = $app->request->getBody();
        $decoded = json_decode($data);
        $email=$decoded->{'email'};

        //check if the password is unique in the db
        $query=$db->prepare("SELECT * FROMd ".TABLE_USERS." WHERE email='$email'");
        $query->execute();
        $user=$query->fetch();

        if($query->rowCount()<1){
                $response=array('errors'=>'1'); //email does not exists
        }elseif($query->rowCount()>1){
                $response=array('errors'=>'2'); //multiple emails found, maybe a group account?
        }elseif($query->rowCount()==1){
                //chars for the creation of the random password
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        
                //creates random password
                $newPassword=substr(str_shuffle($chars),0,8);
                $newPasswordMd5=md5($newPassword);

                $username=$user['name_user'];

                //changing the password in the db
                $query=$db->prepare("UPDATEd ".TABLE_USERS." SET passw='$newPasswordMd5' WHERE email='$email'");
                $query->execute();

                //sending mail
                $to = $email;
                $subject = "Password restore";

                $headers = "From: youremail@here\r\n";
                $headers .= "Reply-To: youremail@here\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                $message = '<html><body>';
                $message .= "The password for your account was succesfully reset.<br>Your details:<br>
                <p>Username = ".$username."<br>Password = ".$newPassword."</p>";
                $message .= '</body></html>';

                mail($to, $subject, $message, $headers);

                $response=array(
                        'errors'=>'null',
                        'newPassword'=>$newPassword
                );
        }

        echo json_encode($response);

});



?>
