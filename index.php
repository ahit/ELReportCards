<?php
session_start();
//get it all set up again
$school_id = 1;
$syear = 2016;

$_SESSION['school_id'] = $school_id;
$_SESSION['syear'] = $syear;

if(isset($_POST['username'])){
        //process login
        $_SESSION['username'] = $_POST['username'];
}
if(!isset($_SESSION['username'])){
        ?>
        <html>
		<form name="input" action="index.php" method="post">
                    <label for "username">Username</label>
                    <input type="text" name="username"/>

                    <label for "Password">Password</label>
                    <input type="password" name="password"/>
                    
                    <input type="submit" name="submit"/>
        </html>
        <?php
}
else{

    //connect to DB
    include("data.php");
    include("../functions/Password.php");
    $dsn = $ELDatabaseType.":host=".$ELDatabaseServer.";dbname=".$ELDatabaseName;
    $dbh = new PDO($dsn, "$ELDatabaseUsername", "$ELDatabasePassword");
    $dbh->query('SET NAMES utf8');

    $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$DatabaseName;
    $sdbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");

    //select permission for user
    $sql = "SELECT profile,password from staff where username='".$_SESSION['username']."' and failed_login<=3";
    $query = $sdbh->prepare($sql);
    
    //disable password protection for testing - naughty!
    #$query = $sdbh->prepare("SELECT profile from staff where username='".$_SESSION['username']."'");
    
    $query->execute();
    if(!(isset($_SESSION['authenticated_user']))){
       $authenticated_user = $query->fetchAll(PDO::FETCH_ASSOC);
       $crypted = $authenticated_user[0]['password'];
       $plain = $_POST['password'];
       if(match_password($crypted, $plain)){
            $_SESSION['authenticated_user'] = $authenticated_user;
            //otherwise, set their permissions
            $_SESSION['profile'] = $authenticated_user[0]['profile'];
       } 
       else{
            $_SESSION['authenticated_user'] = null;
            //disabled or they don't exist?

            $query = $sdbh->prepare("SELECT failed_login from staff where username='".$_SESSION['username']."'");
            $query->execute();
            $failed_user = $query->fetchAll(PDO::FETCH_ASSOC);

             //if there is a user with that username, set failed log in and disable user
             if(count($failed_user)){
                 //increment failure count
                 $query = $sdbh->prepare("UPDATE staff set failed_login=".($failed_user[0]['failed_login']+1)." WHERE username='".$_SESSION['username']."'");
                 $query->execute();

                //if post increment it is >3 disable
                if($failed_user[0]['failed_login']>=2){
                    $_SESSION['username'] = null;
                    print("Account is disabled. Please see the front office staff.");
                    die();
                    
                }
                
            }

            //display error because they don't exist or they weren't disabled
            $_SESSION['username'] = null;
            print("Sorry - looks like that wasn't quite right. <a href='index.php'>Try again?</a>");
            die();
       }
    }

    else{
       $authenticated_user = $_SESSION['authenticated_user'];
    }
   


    $templates = array();
    $query = $dbh->prepare("SELECT * from templates where school_id=$school_id order by template_name ASC");
    $query->execute();
    $templates_result = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach($templates_result as $val){
            $template_id = $val['template_id'];
            $templates[$template_id] = $val['template_name'];
    }


    //grab staff names for list if admin
    if($_SESSION['profile'] == 'admin'){
        $sql = "
		SELECT staff_id, first_name, last_name, username from staff 
		where 
		   syear=(select MAX(syear) from staff) 
		   and 
		   current_school_id=$school_id
		   and
		   profile_id in (2)
                   and
                   username like 'lis%'
		ORDER BY staff_id ASC
	";

        $query = $sdbh->prepare($sql);
    }
    else{
        $query = $sdbh->prepare("SELECT staff_id, first_name, last_name from staff where username='".$_SESSION['username']."'");
    }
    $query->execute();
    $teachers_result = $query->fetchAll(PDO::FETCH_ASSOC);
    $teachers = array();
    foreach($teachers_result as $val){
            $name = $val['last_name'].", ".$val['first_name'];
            $nicename = $val['first_name']." ".$val['last_name'];
            $teachers[$nicename] = $name;
            $teachers['id'] = $val['staff_id'];
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
                            <select name ="template_id">
                                    <?php foreach($templates as $template_id =>$template) print("<option value =\"".$template_id."\">".$template."</option>")?>
                            </select>


                            <input type="submit" value="Submit">
                    </form>
            <p><em>updated 11 Jan 2017</em>
                    <ul>
                            <li>Added password protection - late in coming!</li>
                    </ul>
            </p>

            <p><em>updated 28 Feb 2014</em>
                    <ul>
                            <li>updated queries to match current version of OpenSIS (5.3)</li>
                    </ul>
            </p>
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
    <?php
}
?>
