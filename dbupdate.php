<?
   $sdbh = connectELDB();
   $sdbh->query('SET NAMES utf8');

   //re-arrange tables
   $rearrange = $sdbh->prepare("ALTER TABLE template_fields ADD language_id 
      INT(2) NOT NULL AFTER topic_id");
   $rearrange->execute();

   $rearrange = $sdbh->prepare("ALTER TABLE template_fields ADD UNIQUE (
      template_id ,
      topic_id ,
      language_id
   )");

   $rearrange->execute();

   //create new language table
   $new_table = $sdbh->prepare("CREATE TABLE languages(language_id INT(2),
      description VARCHAR(32))");
   $new_table->execute();

   $new_table = $sdbh->prepare("ALTER TABLE  `languages` 
      ADD INDEX (language_id)");
   $new_table->execute();

   //create new languages
   $lang = $sdbh->prepare("INSERT INTO languages VALUES('1','English')");
   $lang->execute();
   $lang = $sdbh->prepare("INSERT INTO languages VALUES('2','Khmer')");
   $lang->execute();

   //re-arrange table
   $query = $sdbh->prepare("SELECT * from template_fields");
   $query->execute();
   $template_fields = $query->fetchALL();

   $bye= $sdbh->prepare("DELETE from template_fields");
   $bye->execute();

   foreach($template_fields as $template_field){
	print("$text_kh\n");
	$template_id = $template_field['template_id'];
	$topic_id = $template_field['topic_id'];
	$text_en = $template_field['text_en'];
	$text_kh = $template_field['text_kh'];
	$is_graded= $template_field['is_graded'];

	$update = $sdbh->prepare("INSERT INTO template_fields values ('".
           $template_id."','".$topic_id."','1','','".$text_en."','".
           $is_graded."')");
	$update->execute();

	$update = $sdbh->prepare("INSERT INTO template_fields values ('".
           $template_id."','".$topic_id."','2','','".$text_kh."','".
           $is_graded."')");
	$update->execute();
   }

   //rename text_en and get rid of text_kh
   $shift = $sdbh->prepare("ALTER TABLE template_fields CHANGE text_kh text VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci");
   $shift->execute();

   $shift = $sdbh->prepare("ALTER TABLE template_fields DROP COLUMN text_en");
   $shift->execute();

   function connectELDB(){
      include("data.php");
      $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$ELDatabaseName;
      $dbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");
      $dbh->query('SET NAMES utf8');
      return($dbh);
   }
?>
