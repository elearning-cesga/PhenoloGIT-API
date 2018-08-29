<?php
include('db.php');

//get all the observations
$app->get('/observation/filter/:filter', function ($filter) use($app,$db) {

        if($filter=='null'){
                $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation DESC ");
        }else{
        
        $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idspecie='$filter' ORDER BY idobservation DESC ");   
        }

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//get all the observations paginated
$app->get('/observation/filter/:filter/limit/:limit', function ($filter,$limit) use($app,$db) {

        if($filter=='null'){
            if($limit!=null && $limit!='null'){
                $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation DESC LIMIT '$limit'");
            }else{
                $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation DESC");
            }
            
        }else{
            $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idspecie='$filter' ORDER BY idobservation DESC LIMIT '$limit'");   
        }

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];
        $idSpecie=$ob['idspecie'];
        $idStage=$ob['idstage'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

        //Get the specie name for given observation
        $querySpecie=$db->prepare("SELECT * FROM ".TABLE_SPECIES." WHERE idspecie='$idSpecie'");
        $querySpecie->execute();
        $specie=$querySpecie->fetch();

        //Get the stage name for given observation
        $queryStage=$db->prepare("SELECT * FROM ".TABLE_STAGES." WHERE idstage='$idStage'");
        $queryStage->execute();
        $stage=$queryStage->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "bestexample"=>$ob['best_sample'],
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount(),
                    "specie_en"=>$specie['name_en'],
                    "specie_es"=>$specie['name_es'],
                    "specie_gl"=>$specie['name_ga'],
                    "specie_dk"=>$specie['name_de'],
                    "specie_lt"=>$specie['name_li'],
                    "stage_en"=>$stage['name_en'],
                    "stage_es"=>$stage['name_es'],
                    "stage_gl"=>$stage['name_ga'],
                    "stage_dk"=>$stage['name_de'],
                    "stage_lt"=>$stage['name_li'],
            );

        }


        echo json_encode($observationsArray);

    }else{
        echo json_encode(array('errors'=>'empty recordset'));
    }
});

//get all the observations paginated and filtered by school, user or specie
$app->get('/observation/filter/:filter/school/:school/user/:user/limit/:limit', function ($speciefilter,$schoolfilter,$userfilter,$limit) use($app,$db) {

    //with no limit, no filters
    $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation");   

    //with limit, no filters
    if($limit!='null' && $limit!=null){
        $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation DESC LIMIT '$limit'");
    }

    //filtered by specie
    if($speciefilter!='null' && $speciefilter!=null){
        $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idspecie='$speciefilter' ORDER BY idobservation DESC LIMIT '$limit'");   
    }

    //filtered by user
    if($userfilter!=null && $userfilter!='null'){
        $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE iduser='$userfilter' ORDER BY idobservation DESC LIMIT '$limit'");
    }

    //filtered by school
    if($schoolfilter!=null && $schoolfilter!='null'){
	if($limit=='null' || $limit==null){
		$query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." o LEFT JOIN ".TABLE_USERS." u ON o.iduser = u.iduser WHERE u.idschool='$schoolfilter' ORDER BY idobservation DESC");

	}else{
        	$query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." o LEFT JOIN ".TABLE_USERS." u ON o.iduser = u.iduser WHERE u.idschool='$schoolfilter' ORDER BY idobservation DESC LIMIT '$limit'");
	}    
}	

	$query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

            $images=array();		
            $id=$ob['idobservation'];
            $idSpecie=$ob['idspecie'];
            $idStage=$ob['idstage'];

            //$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
            $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
            $queryPictures->execute();
        
            if($queryPictures->rowCount() > 0){
                $media=$queryPictures->fetch();      
                
                //check if file exists
                if(!file_exists('../html/uploads/'.$media['media_url'])){
                    $media['media_url']="no_picture.jpg"; //if not set a default 'no found image'
                }

                $file=pathinfo($media['media_url']);

                //create media_type based on extension
                if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                    $media_type='video';
                }else{
                    $media_type='picture';
                }	
            
                $images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
                
            }else{
                $images[]=array("media_type"=>"null","media_url"=>"null");	
            }
            
            //getting comments
            $queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
            $queryComments->execute();

            //getting likes
            $queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
            $queryLikes->execute();

            //Get the validates for given observation
            $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
            $queryValidates->execute();
            
            $idUser=$ob['iduser'];
        
            //Get the user details
            $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
            $queryUser->execute();
            $user=$queryUser->fetch();

            //Get the specie name for given observation
            $querySpecie=$db->prepare("SELECT * FROM ".TABLE_SPECIES." WHERE idspecie='$idSpecie'");
            $querySpecie->execute();
            $specie=$querySpecie->fetch();

            //Get the stage name for given observation
            $queryStage=$db->prepare("SELECT * FROM ".TABLE_STAGES." WHERE idstage='$idStage'");
            $queryStage->execute();
            $stage=$queryStage->fetch();

            //Count images
            $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
            $queryCountImages->execute();

            $observationsArray[] = array(
                'id'=>$ob['idobservation'],
                'title' => $ob['title'],
                'description' =>$ob['description'],
                'media'=>$images[0],
                'countImages'=>$queryCountImages->rowCount(),
                "id"=>$ob['idobservation'],
                "user_id"=>$user['iduser'],
                "user_name"=>$user['name'],
                "user_picture"=>$user['picture'],
                "coords"=>$ob['latitude'].','.$ob['longitude'],
                "media_type"=>"picture",
                'time'=>$ob['time'],
                "likes"=>$queryLikes->rowCount(),
                "bestexample"=>$ob['best_sample'],
                "status"=>"1",
                "comments"=>$queryComments->rowCount(),
                "userLikes"=>0,
                "validations"=>$queryValidates->rowCount(),
                "specie_en"=>$specie['name_en'],
                "specie_es"=>$specie['name_es'],
                "specie_gl"=>$specie['name_ga'],
                "specie_dk"=>$specie['name_de'],
                "specie_lt"=>$specie['name_li'],
                "stage_en"=>$stage['name_en'],
                "stage_es"=>$stage['name_es'],
                "stage_gl"=>$stage['name_ga'],
                "stage_dk"=>$stage['name_de'],
                "stage_lt"=>$stage['name_li'],
            );

        }

        if(isset($observationsArray)){
            echo json_encode($observationsArray);
        }else{
            echo json_encode(array('errors'=>'empty recordset'));
        }
        
    }else{
        echo json_encode(array('errors'=>'empty recordset')); 
    }
})->name('getObservations');

//get all the observations by location
$app->get('/observation/lat/:lat/lng/:lng/radius/:radius', function ($lat,$lon,$radius) use($app,$db) {
      
    //get only the observations in this radius
    $angle_radius = $radius / ( 111 * cos( $lat ) ); // Every lat|lon degree° is ~ 111Km
    $min_lat = $lat - $angle_radius;
    $max_lat = $lat + $angle_radius;
    $min_lon = $lon - $angle_radius;
    $max_lon = $lon + $angle_radius;

	$query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE latitude BETWEEN '$min_lat' AND '$max_lat' AND longitude BETWEEN '$min_lon' AND '$max_lon'");   

	$query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//*** DEPRECATED get all the observations (for old versions of the app)
$app->get('/observation', function () use($app,$db) {

    //$query=$db->prepare("SELECT * FROM phe_observations ORDER BY id DESC");
	
	$query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." ORDER BY idobservation DESC");   

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//*** DEPRECATED get all the observations ORDERED BY likes (for old versions of the app)
$app->get('/observation/likes', function () use($app,$db) { 
	
	$query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS likes FROM ".TABLE_LIKES." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.likes,0) DESC");   

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//get all the observations ORDERED BY likes
$app->get('/observation/filter/:filter/limit/:limit/likes', function ($filter,$limit) use($app,$db) { 

    if($filter=='null'){
            $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS likes FROM ".TABLE_LIKES." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.likes,0) DESC LIMIT '$limit'");   
        }else{
            $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS likes FROM ".TABLE_LIKES." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation WHERE o.idspecie='$filter' ORDER BY COALESCE(l.likes,0) DESC LIMIT '$limit'"); 
        }
	
	//$query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS likes FROM ".TABLE_LIKES." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.likes,0) DESC");   

	$query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
})->name('getObservationsByLikes');

//*** DEPRECATED get all the observations ORDERED BY validations (for old versions of the app)
$app->get('/observation/validations', function () use($app,$db) { 
	
	$query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS validations FROM ".TABLE_VALIDATIONS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.validations,0) DESC");   

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//get all the observations ORDERED BY validations
$app->get('/observation/filter/:filter/limit/:limit/validations', function ($filter,$limit) use($app,$db) { 
	
    if($filter=='null'){
        $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS validations FROM ".TABLE_VALIDATIONS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.validations,0) DESC LIMIT '$limit'"); 
    }else{
        $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS validations FROM ".TABLE_VALIDATIONS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation WHERE o.idspecie='$filter' ORDER BY COALESCE(l.validations,0) DESC LIMIT '$limit'");   

    }

	$query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		
        	$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
})->name('getObservationsByValidations');

//*** DEPRECATED get all the observations ORDERED BY comments (for old versions of the app)
$app->get('/observation/comments', function () use($app,$db) { 
	
	$query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS comments FROM ".TABLE_COMMENTS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.comments,0) DESC");   

	 $query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
});

//get all the observations ORDERED BY comments 
$app->get('/observation/filter/:filter/limit/:limit/comments', function ($filter,$limit) use($app,$db) { 

     if($filter=='null'){
        $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS comments FROM ".TABLE_COMMENTS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation ORDER BY COALESCE(l.comments,0) DESC LIMIT '$limit'");  
    }else{
        $query=$db->prepare("SELECT *, o.idobservation AS idobservation FROM ".TABLE_OBSERVATIONS." AS o LEFT JOIN(SELECT idobservation,COUNT(*) AS comments FROM ".TABLE_COMMENTS." GROUP BY idobservation) AS l ON o.idobservation = l.idobservation WHERE o.idspecie='$filter' ORDER BY COALESCE(l.comments,0) DESC LIMIT '$limit'");  
    }

	$query->execute();

    if($query->rowCount() > 0){

        while($ob = $query->fetch()){

		$images=array();		
		$id=$ob['idobservation'];

		//$queryPictures=$db->prepare("SELECT * FROM phe_observation_media WHERE idobservation='$id'");
	    $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id' AND cover='1'");
		$queryPictures->execute();
	
		if($queryPictures->rowCount() > 0){
		

        		$media=$queryPictures->fetch();
               
			$file=pathinfo($media['media_url']);

			//create media_type based on extension
               		 if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                       		 $media_type='video';
               		 }else{

                        	$media_type='picture';
              		  }	
		
			$images[]=array("media_type"=>$media_type,"media_url"=>$media['media_url']);
        	
		}else{

			$images[]=array("media_type"=>"null","media_url"=>"null");	
	
		}
          
		//getting comments
		$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
       	$queryComments->execute();

        //getting likes
		$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
       	$queryLikes->execute();

        //Get the validates for given observation
        $queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
        $queryValidates->execute();
           
        $idUser=$ob['iduser'];
    
        //Get the user details
        $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUser'");
        $queryUser->execute();
        $user=$queryUser->fetch();

		 //Count images
        $queryCountImages=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryCountImages->execute();

                 $observationsArray[] = array(
                    'id'=>$ob['idobservation'],
                    'title' => $ob['title'],
                    'description' =>$ob['description'],
                    'media'=>$images[0],
                    'countImages'=>$queryCountImages->rowCount(),
                    "id"=>$ob['idobservation'],
                    "user_id"=>$user['iduser'],
                    "user_name"=>$user['name'],
                    "user_picture"=>$user['picture'],
                    "coords"=>$ob['latitude'].','.$ob['longitude'],
                    "media_type"=>"picture",
                    'time'=>$ob['time'],
                    "likes"=>$queryLikes->rowCount(),
                    "status"=>"1",
                    "comments"=>$queryComments->rowCount(),
                    "userLikes"=>0,
                    "validations"=>$queryValidates->rowCount()
            );

        }


        echo json_encode($observationsArray);

    }else{

        echo json_encode(array('errors'=>'empty recordset'));

    }
})->name('getObservationsByComments');

//post a observation
$app->post('/observation', function () use($app,$db) {

    $data = $app->request->getBody();
    $decoded = json_decode($data);

        $pressure=0;

        $title=$decoded->{'title'};
        $description=$decoded->{'description'};
        $userid=$decoded->{'userid'};
        $time=gmdate("Y-m-d H:m", time()); //convert to postgretimestap
        $coords=explode(",",$decoded->{'coords'});
        $lat=$coords[0];
        $lon=$coords[1];
        $cover=$decoded->{'cover'};
        $temperature=$decoded->{'temperature'};
        $specie='1';     
        $stage='1';     

        if((isset($decoded->{'stage'})&&(is_numeric($decoded->{'stage'})))){
            $specie=$decoded->{'specie'};
                $stage=$decoded->{'stage'};
        }       

        //save log
        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$userid','observation created','$data')");
        $query->execute();

        //check if pressure exists in the post params
        if(isset($decoded->{'pressure'})){
            $pressure=$decoded->{'pressure'};
        }
        $humidity=$decoded->{'humidity'};
        $weatherstate=$decoded->{'weatherstate'};

        //get the relation id
        $query=$db->prepare("SELECT * FROM ".TABLE_U_SPECIE_STAGE." WHERE idspecie='$specie' AND idstage='$stage'");
        $query->execute();
        $relspeciestage=$query->fetch();
        $idspeciestage=$relspeciestage['idspeciestage'];
        
        $query=$db->prepare("insert into ".TABLE_OBSERVATIONS."(title,description,iduser,idstage,idspecie,longitude,latitude,time,weather_p,weather_h,weather_t,weather_state,idspeciestage) values('$title','$description','$userid','$stage','$specie','$lon','$lat','$time','$pressure','$humidity','$temperature','$weatherstate','$idspeciestage') RETURNING idobservation");
            if($query->execute()){
                $errors=array('error'=>'0');
                $returnedvals=$query->fetch();
                $lastId= $returnedvals['idobservation'];
                //qet the last inserted id
                $media_url=BASE_URL.$lastId;
            }else{
                $errors=array('error'=>'1');
            }


        //modify media_url field
        $query=$db->prepare("update ".TABLE_OBSERVATIONS." set media_url='$media_url' where idobservation='$lastId'");
        $query->execute();
        //---------------
	
        $picture=$decoded->{'media'};

        foreach($picture as $media){
            
            $isCover=0;

            //is this image the observation cover?
            if($media==$cover){
                $isCover=1;
            }

            $file=pathinfo($media);

            //create media_type based on extension
            if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                $media_type='video';
            }else{

                $media_type='picture';
            }
                  
            //$query_media=$db->prepare("insert into phe_observation_media(media_url,media_type,idobservation) values('$media','$media_type','$lastId')");
            $query_media=$db->prepare("insert into ".TABLE_OBSERVATION_MEDIA."(media_url,idobservation,cover) values('$media','$lastId','$isCover')");

            $query_media->execute();
        }
        //get media_type from media
        $media_type = 'video';

        echo json_encode($errors);

});

//get details for given observation
$app->get('/observation/:id/:user', function ($id,$user) use($app,$db) {

    $query=$db->prepare("SELECT * FROM ".TABLE_OBSERVATIONS." WHERE idobservation='$id' ORDER BY idobservation DESC");
    $query->execute();

    $images= array();

    while($ob = $query->fetch()){

        $queryPictures=$db->prepare("SELECT * FROM ".TABLE_OBSERVATION_MEDIA." WHERE idobservation='$id'");
        $queryPictures->execute();

        while($media=$queryPictures->fetch()){

                $mediaType='picture';

                //check what kind of file is based in its extension
                $file=pathinfo($media['media_url']);

                if(($file['extension']=='mp4') || ($file['extension']=='mov')){
                         $mediaType='video';
                }

                $images[]=array("media_type"=>$mediaType,"media_url"=>$media['media_url']);
	}

    $idUserOwner=$ob['iduser'];
    $idSpecie=$ob['idspecie'];
    $idStage=$ob['idstage'];

    //Get the comments for given observation
	$queryComments=$db->prepare("SELECT * FROM ".TABLE_COMMENTS." WHERE idobservation='$id'");
	$queryComments->execute();

    //Get the likes for given observation
	$queryLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id'");
	$queryLikes->execute();

     //Get the validates for given observation
	$queryValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id'");
	$queryValidates->execute();

    //Get the specie name for given observation
	$querySpecie=$db->prepare("SELECT * FROM ".TABLE_SPECIES." WHERE idspecie='$idSpecie'");
	$querySpecie->execute();
    $specie=$querySpecie->fetch();

    //Get the stage name for given observation
	$queryStage=$db->prepare("SELECT * FROM ".TABLE_STAGES." WHERE idstage='$idStage'");
	$queryStage->execute();
    $stage=$queryStage->fetch();

    //Check if current user liked this observation
	$queryUserLikes=$db->prepare("SELECT * FROM ".TABLE_LIKES." WHERE idobservation='$id' AND iduser='$user'");
	$queryUserLikes->execute();
    $userlikes=0;
    if($queryUserLikes->rowCount()!=0){
        $userlikes=1;
    }

    //Check if current user validated this observation
	$queryUserValidates=$db->prepare("SELECT * FROM ".TABLE_VALIDATIONS." WHERE idobservation='$id' AND iduser='$user'");
	$queryUserValidates->execute();
    $userValidates=0;
    if($queryUserValidates->rowCount()!=0){
        $userValidates=1;
    }
    
    //Get the user details
    $queryUser=$db->prepare("SELECT * FROM ".TABLE_USERS." WHERE iduser='$idUserOwner'");
	$queryUser->execute();
    $user=$queryUser->fetch();

        $observationsArray[] = array(
                'title' => $ob['title'],
                'description' =>$ob['description'],
                'media' => $images,
                "id"=>$ob['idobservation'],
                "user_id"=>$idUserOwner,
                "user_name"=>$user['name'],
                "user_picture"=>$user['picture'],
                "coords"=>$ob['longitude'].','.$ob['latitude'],
                "media_type"=>"picture",
                'time'=>$ob['time'],
                "likes"=>$queryLikes->rowCount(),
                "bestexample"=>$ob['best_sample'],
                "validations"=>$queryValidates->rowCount(),
                "status"=>"1",
                "comments"=>$queryComments->rowCount(),
                "userLikes"=>$userlikes,
                "userValidates"=>$userValidates,
                "w_state"=>$ob['weather_state'],
                "w_pressure"=>$ob['weather_p'],
                "w_temperature"=>$ob['weather_t'],
                "w_humidity"=>$ob['weather_h'],
                "specie_en"=>$specie['name_en'],
                "specie_es"=>$specie['name_es'],
                "specie_gl"=>$specie['name_ga'],
                "specie_dk"=>$specie['name_de'],
                "specie_lt"=>$specie['name_li'],
                "stage_en"=>$stage['name_en'],
                "stage_es"=>$stage['name_es'],
                "stage_gl"=>$stage['name_ga'],
                "stage_dk"=>$stage['name_de'],
                "stage_lt"=>$stage['name_li'],
                
        );
    }

        echo json_encode($observationsArray);
})->name('getObservationDetails');

$app->options('/observation/:id/:userid', function ($id,$userid) use($app,$db) {

        $headers = $app->request->headers;
        $method=$headers["Access-Control-Request-Method"];

});

//edit observation
$app->put('/observation/:userid/:observationid', function ($userid, $id) use($app,$db){

    $data = $app->request->getBody();
    $decoded = json_decode($data);
    $title=$decoded->{'title'};
    $description=$decoded->{'description'};

    $query=$db->prepare("UPDATE ".TABLE_OBSERVATIONS." SET title='$title', description='$description' WHERE idobservation='$id'");
    $query->execute();

    $route = $app->router->getNamedRoute('getObservationDetails'); //call GET observation details
    call_user_func($route->getCallable(),$id,$userid);
        
});

$app->delete('/observation/:id/:userid',function($id,$userid) use($app,$db){

        $query=$db->prepare("DELETE FROM ".TABLE_OBSERVATIONS." WHERE idobservation='$id'");
        $query->execute();

        //save log
        $query=$db->prepare("INSERT INTO ".TABLE_LOGS."(iduser,action,data) VALUES('$userid','observation deteted','$id')");
        $query->execute();

        $route = $app->router->getNamedRoute('getObservations'); //call GET observation
        call_user_func($route->getCallable(),'null','null','null',10);

});


?>
