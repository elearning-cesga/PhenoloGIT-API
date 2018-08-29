<?php

/*Config details of database*/

$dbname='dbname';
$host='db.host';
$username='dbuser';
$password='dbpassword';
$db = new PDO("pgsql:dbname=$dbname;host=$host", $username, $password );

//TABLES
define('TABLE_COMMENTS', "[yourCommentsTable]");
define('TABLE_USERS', "[yourUsersTable]");
define('TABLE_OBSERVATIONS', "[yourObservationsTable]");
define('TABLE_LOGS', "[yourLogsTable]");
define('TABLE_NOTIFICATIONS', "[yourNotificationsTable]");
define('TABLE_LIKES', "[yourLikesTable]");
define('TABLE_OBSERVATION_MEDIA', "[yourObservationsMediaTable]");
define('TABLE_VALIDATIONS', "[yourValidationsTable]");
define('TABLE_SPECIES', "[yourSpeciesTable]");
define('TABLE_STAGES', "[yourStagesTable]");
define('TABLE_U_SPECIE_STAGE', "[yourUSpecieStageTable]");
define('TABLE_SCHOOLS', "[yourSchoolsTable]");
define('TABLE_SPECIES_PICTURES', "[yourSpeciesPicturesTable]");

define('BASE_URL', "[yourBaseUrl]");
?>