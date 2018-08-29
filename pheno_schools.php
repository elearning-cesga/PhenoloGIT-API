<?php
include('db.php');

//GET schools
$app->get('/school', function () use($app,$db) {
$query=$db->prepare("SELECT * FROM ".TABLE_SCHOOLS." ORDER BY cod_iso");                                                                                          
$query->execute();

    while($school=$query->fetch()){

        $schoolArray[]=array(
            'id'=>$school['idschool'],
            'name'=>$school['name'],
            'cod_iso'=>$school['cod_iso'],
            'longitude'=>$school['longitude'],
            'latitude'=>$school['latitude'],
        );

    }

    echo json_encode($schoolArray);

});

//GET schools
$app->get('/school/:id', function ($id) use($app,$db) {
$query=$db->prepare("SELECT * FROM ".TABLE_SCHOOLS." WHERE idschool='$id'");                                                                                          
$query->execute();

    while($school=$query->fetch()){

        $schoolArray[]=array(
            'id'=>$school['idschool'],
            'name'=>$school['name'],
            'cod_iso'=>$school['cod_iso'],
            'longitude'=>$school['longitude'],
            'latitude'=>$school['latitude'],
            'idcountry'=>$school['idcountry']
        );

    }

    echo json_encode($schoolArray);

});

//POST school
$app->post('/school', function () use($app,$db) {

    $school_name=$app->request->post('school_name');
    $school_codIso=$app->request->post('school_codIso');
    $school_country=$app->request->post('school_country');
    $school_location=$app->request->post('school_location');
    $location=explode(",",$school_location);
    $school_website=$app->request->post('school_website');
    
    
    
    $query=$db->prepare("INSERT INTO ".TABLE_SCHOOLS."(name,cod_iso,longitude,latitude,idcountry,lnk_web) VALUES('$school_name','$school_codIso','$location[1]','$location[0]','$school_country','$school_website')");                                                                                          
    if($query->execute()){
        $response=array("errors"=>0);
    }else{
        $response=array("errors"=>1);
    }

    echo json_encode($response);
    
    });

?>