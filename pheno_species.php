<?php
include('db.php');

//GET species
$app->get('/specie', function () use($app,$db) {
$query=$db->prepare("SELECT * FROM ".TABLE_SPECIES." ORDER BY n_order");                                                                                          
$query->execute();

    while($specie=$query->fetch()){

        $idspecie=$specie['idspecie'];
        
        $queryPicture=$db->prepare("SELECT picture_url FROM ".TABLE_SPECIES_PICTURES." WHERE idspecie='$idspecie'");
        $queryPicture->execute();        
        $picture=$queryPicture->fetch();
        
        $queryStages=$db->prepare("SELECT * FROM ".TABLE_U_SPECIE_STAGE." as rel LEFT JOIN ".TABLE_STAGES." as stages ON rel.idstage=stages.idstage where rel.idspecie='$idspecie'"); 
        $queryStages->execute();
        $stages="";      
        while($stage=$queryStages->fetch()){
           $stages.=$stage['name_sc'].',';
        }

        $specieArray[]=array(
            'id'=>$specie['idspecie'],
            'name'=>$specie['name_sc'],
            'name_li'=>$specie['name_li'],
            'name_es'=>$specie['name_es'],
            'name_en'=>$specie['name_en'],
            'name_de'=>$specie['name_de'],
            'name_gl'=>$specie['name_gl'],
            'desc_li'=>$specie['desc_li'],
            'desc_es'=>$specie['desc_es'],
            'desc_en'=>$specie['desc_en'],
            'desc_de'=>$specie['desc_de'],
            'desc_gl'=>$specie['desc_gl'],
            'thumbnail'=>$specie['thumbnail'],
            'picture'=>$picture['picture_url'],
            'stages'=>rtrim($stages, ",") 
        );

    }

    echo json_encode($specieArray);

});

//GET details for specie
$app->get('/specie/:id', function ($id) use($app,$db) {

    $query=$db->prepare("SELECT * FROM ".TABLE_SPECIES." WHERE idspecie='$id'");                                                                                          
    $query->execute();

    while($specie=$query->fetch()){

        $queryStages=$db->prepare("SELECT * FROM ".TABLE_U_SPECIE_STAGE." as rel LEFT JOIN ".TABLE_STAGES." as stages ON rel.idstage=stages.idstage where rel.idspecie='$id'");
        $queryStages->execute();

        while($stage=$queryStages->fetch()){

            $arrayStages[]=array(
                'stage_id'=>$stage['idstage'],
                'stage_sc'=>$stage['name_sc'],
                'stage_en'=>$stage['name_en'],
                'stage_es'=>$stage['name_es'],
                'stage_lt'=>$stage['name_li'],
                'stage_dk'=>$stage['name_de'],
                'stage_gl'=>$stage['name_ga'],
		        'desc_en'=>$stage['desc_en'],
		        'desc_es'=>$stage['desc_es'],
		        'desc_dk'=>$stage['desc_dk'],
                'desc_lt'=>$stage['desc_li'],
                'desc_gl'=>$stage['desc_gl'],
		        'stage_picture'=>$stage['stage_picture']
            );
        
        }

        $queryPictures=$db->prepare("SELECT * FROM ".TABLE_SPECIES_PICTURES." WHERE idspecie='$id'");
        $queryPictures->execute();

        while($picture=$queryPictures->fetch()){

            $arrayPictures[]=array(

                'picture'=>$picture['picture_url']

            );
        
        }

        $arraySpecie[]=array(

            'id'=>$id,
            'name'=>$specie['name_sc'],
            'name_li'=>$specie['name_li'],
            'name_es'=>$specie['name_es'],
            'name_en'=>$specie['name_en'],
            'name_de'=>$specie['name_de'],
            'name_gl'=>$specie['name_gl'],
            'desc_li'=>$specie['desc_li'],
            'desc_es'=>$specie['desc_es'],
            'desc_en'=>$specie['desc_en'],
            'desc_de'=>$specie['desc_de'],
            'desc_gl'=>$specie['desc_gl'],
            'picture'=>$arrayPictures,
            'stages'=>$arrayStages

        );

        echo json_encode($arraySpecie);

    }

});


?>
