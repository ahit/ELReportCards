<?php
session_destroy(); //kill default student selection
session_start();
//connect to DB
$school_id = 2;
$syear = 2013;

include("data.php");
$dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$ELDatabaseName;
$dbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");
$dbh->query('SET NAMES utf8');

$dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$DatabaseName;
$sdbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");

$templates = array();
$query = $dbh->prepare("SELECT * from templates where school_id=2 order by template_name ASC");
$query->execute();
$templates_result = $query->fetchAll(PDO::FETCH_ASSOC);
foreach($templates_result as $val){
	$template_id = $val['template_id'];
	$templates[$template_id] = $val['template_name'];
}


//grab staff names for list
$query = $sdbh->prepare("SELECT staff_id, first_name, last_name from staff WHERE syear = $syear and current_school_id=$school_id and profile ='teacher' and is_disable IS NULL and profile_id=2
		order by last_name");
$query->execute();
$teachers_result = $query->fetchAll(PDO::FETCH_ASSOC);
$teachers = array();
foreach($teachers_result as $val){
	$name = $val['last_name'].", ".$val['first_name'];
	$nicename = $val['first_name']." ".$val['last_name'];
	$teachers[$nicename] = $name;
	$teachers['id'] = $val['staff_id'];
}

$query = $sdbh->prepare("SELECT staff_id, first_name, last_name from staff WHERE syear = $syear and current_school_id=$school_id and profile_id ='5' and is_disable IS NULL
		order by last_name");
$query->execute();
$teachers_kh_result = $query->fetchAll(PDO::FETCH_ASSOC);
$teachers_kh = array();
foreach($teachers_kh_result as $val){
	$name = $val['last_name'].", ".$val['first_name'];
	$nicename = $val['first_name']." ".$val['last_name'];
	$teachers_kh[$nicename] = $name;
	$teachers_kh['id'] = $val['staff_id'];
}
?>
<html>
	<body>
	<h1>Teacher View (Single Report Card, Editable)</h1>
		<form name="input" action="teacherview.php" method="post">

			<select name ="teacher_id">
				<?php foreach($teachers_result as $teacher) print("<option value = \"".$teacher['staff_id']."\">".$teacher['last_name'].", ".$teacher['first_name']."</option>")?>
			</select>
			<select name ="template_id">
				<?php foreach($templates as $template_id =>$template) print("<option value =\"".$template_id."\">".$template."</option>")?>
			</select>
			<input type="submit" value="Submit">
		</form>
	<h1> Admin/Printing View (Full Class of Report Cards, non editable)</h1>
		<form name="input" action="adminview.php" method="post">

			<select name ="teacher_id">
				<?php foreach($teachers_result as $teacher) print("<option value = \"".$teacher['staff_id']."\">".$teacher['last_name'].", ".$teacher['first_name']."</option>")?>
			</select>
			<select name ="teacher_kh_id">
                <option value = "0">No Khmer Teacher</option>
				<?php foreach($teachers_kh_result as $teacher) print("<option value = \"".$teacher['staff_id']."\">".$teacher['last_name'].", ".$teacher['first_name']."</option>")?>
			</select>
			<select name ="template_id">
				<?php foreach($templates as $template_id =>$template) print("<option value =\"".$template_id."\">".$template."</option>")?>
			</select>


			<input type="submit" value="Submit">
		</form>
 	<p><em>updated 11 Nov 2013</em>
		<ul>
			<li>AHIS Specific customizations</li>
		</ul>
	</p>
                
 	<p><em>updated 11 Oct 2013</em>
		<ul>
			<li>not showing 'current' marking period in attendance</li>
		</ul>
	</p>
 	<p><em>updated 10 Oct 2013</em>
                <ul>
                        <li>only printing effort schema on KG3, KG4 and KG report cards</li>
                        <li>many, many bug fixes and much cleaner code. Yay!</li>
                </ul>
                <p><strong><em>questions? comments? email it@asianhope.org</em></strong>
                <p>if you're particularly interested in the day-to-day changes, you can follow this (and other projects) on <a href = "https://github.com/lkozloff?tab=activity">github</a></p>

	</body>
</html>
