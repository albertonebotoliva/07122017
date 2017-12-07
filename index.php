<?php
# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Language\LanguageClient;
$language = new LanguageClient([
    'projectId' => ''
]);


function system_autoload( $class_name )
{
    // Convert class name to filename format.
    $class_name = strtolower( $class_name );
    $paths = array(
        LIB_PATH
      );

    // Search the files
    foreach( $paths as $path ) {
        if( file_exists( "$path/$class_name.php" ) )
            require_once( "$path/$class_name.php" );
    }
}

function initializeEnvironment(){
  define( 'PATH', dirname( __FILE__ ) );
  // MVC PATHs
  define( 'LIB_PATH',         PATH . '/libs' );
  define( 'BASE_URI',                '/ABTASTY/');
  //Autoload the system.
  spl_autoload_register( 'system_autoload' );
}

initializeEnvironment();

//Connect to the database.
$db = new Database();

//Retrieve the comments from the table comments
$comments = $db->filter('comments','1','*');

//Delete the data in the table entities. In case you run the process several times
$delete = $db->delete('entities','1');


//Get the entities with the language API

foreach ( $comments as $comment ) {
  $entitiesObj = $language->analyzeEntities($comment['comment']);
  $sentimentsObj = $language->analyzeSentiment($comment['comment']);
  $sentiments = $sentimentsObj->sentiment();
  $entities = $entitiesObj->entities();
  

  foreach ( $entities as $entity){
    $data['comment'] = $comment['comment'];
    $data['name'] = $entity['name'];
    $data['type'] = $entity['type'];
    $data['metadata'] = json_encode($entity['metadata']);
    $data['salience'] = $entity['salience'];
    $data['mentions'] = json_encode($entity['mentions']);
    $data['magnitude'] = $sentiments['magnitude'];
    $data['score'] = $sentiments['score'];
    

    //Insert every entity in the table entities with his sentiment
    $db->insert('entities',$data);
  }
}


echo "<pre><h4>Most popular entities</h4>";
print_r($db->getPopular());
echo "</pre>";

echo "<pre><h4>Comments with most sentiment</h4>";
print_r($db->mostSentiment());
echo "</pre>";







