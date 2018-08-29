<?php
include("db.php");

//GET stages
$app->get('/stage', function () use($app,$db) {
$query=$db->prepare("select stages.*, u.idspecie from ".TABLE_STAGES." stages left join ".TABLE_U_SPECIE_STAGE." u on u.idstage = stages.idstage");                  
$query->execute();

    while($stage=$query->fetch()){

        $stageArray[]=array(
            'id'=>$stage['idstage'],
            'name_lt'=>$stage['name_li'],
            'name_es'=>$stage['name_es'],
            'name_en'=>$stage['name_en'],
            'name_dk'=>$stage['name_de'],
            'name_gl'=>$stage['name_ga'],
            'idspecie'=>$stage['idspecie']
        );

    }

    echo json_encode($stageArray);

});


?>
