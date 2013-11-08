<?php
session_start();

$template_id = $_SESSION['template_id'];
$gradeschema = $_SESSION['gradeschema'];

//hack to let the list be alphabetized.
$collate = explode(".",$_SESSION['sid']);
$sid = $collate[1];

$id = $_REQUEST['id'];
$value = $_REQUEST['value'];

//blahrgh
include("data.php");
$dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$ELDatabaseName;
$dbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");
$dbh->query('SET NAMES utf8');

if(strcmp($id, "sid")==0){
	$_SESSION['sid'] = $value;
	//$value = "just entered SID ".$value;
}

else if(strlen($id)>2){
	$term = substr($id,0,2); //F1,F2,F3,F4... more?
	//$type = substr($id,2,1);
	$type = null; //formerly Grade or Effort or Comment - no longer needed
	$topic_id = substr($id,2); //Topic ID (number, or E/K) 
	
	$sql = "INSERT INTO el_grades (template_id, topic_id, student_id, term, type, value)
			VALUES ('$template_id', '$topic_id', '$sid', '$term', '$type', '$value')
			ON DUPLICATE KEY UPDATE value='$value'";

	if((strcmp($value,".") == 0) || (strcmp($value," ")==0)) 
		$sql = "DELETE FROM el_grades where template_id='$template_id' AND topic_id='$topic_id' AND term='$term' and student_id='$sid'";
	
	
	$query = $dbh->prepare($sql);
	$query->execute() or die();
	
	
	if(strcmp($value,"Ch") == 0) $value = "✔'";
	else $value = $gradeschema[$value];
	
}
else{
	$comment_id = substr($id,1);
	//process comment to get rid of bad things like single quotes and make line breaks htmlified.
	$cleanvalue = str_replace("\\n","<br>",$dbh->quote($value));  
	// we're getting a comment!
		$sql = "INSERT INTO el_comments (template_id, student_id, comment_id, comment)
				VALUES ('$template_id', '$sid', '$comment_id', $cleanvalue)
				ON DUPLICATE KEY UPDATE comment=$cleanvalue";
		$query = $dbh->prepare($sql);
		$query->execute();
		
			
		
}




print("$value");
//print($_REQUEST['value']);
?>
