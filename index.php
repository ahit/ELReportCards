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
    $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$ELDatabaseName;
    $dbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");
    $dbh->query('SET NAMES utf8');

    $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$DatabaseName;
    $sdbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");

    //select permission for user
    $query = $sdbh->prepare("SELECT profile from staff where username='".$_SESSION['username']."' and password=MD5('".$_POST['password']."') and is_disable is NULL");
    
    //disable password protection for testing - naughty!
    #$query = $sdbh->prepare("SELECT profile from staff where username='".$_SESSION['username']."'");
    
    $query->execute();
    $authenticated_user = $query->fetchAll(PDO::FETCH_ASSOC);
   
    //if they have no permissions, figure out why
    if(!count($authenticated_user)){
        //disabled or they don't exist?

        $query = $sdbh->prepare("SELECT failed_login,is_disable from staff where username='".$_SESSION['username']."'");
        $query->execute();
        $failed_user = $query->fetchAll(PDO::FETCH_ASSOC);

        //if there is a user with that username, set failed log in and disable user
        if(count($failed_user)){
            //increment failure count
            $query = $sdbh->prepare("UPDATE staff set failed_login=".($failed_user[0]['failed_login']+1)." WHERE username='".$_SESSION['username']."'");
            $query->execute();

            //if post increment it is >3 disable
            if($failed_user[0]['failed_login']>=2){
                $query = $sdbh->prepare("UPDATE staff set is_disable='Y' WHERE username='".$_SESSION['username']."'");
                $query->execute();
                $_SESSION['username'] = null;
                print("Account is disabled. Please see the front office staff.");
                die();
                    
            }
                
        }

        //display error because they don't exist or they weren't disabled
        $_SESSION['username'] = null;
        print("Sorry - looks like that wasn't quite right. <a href='index.php'>Try again?</a>");
        //die();
    }
    //otherwise, set their permissions
    $_SESSION['profile'] = $authenticated_user[0]['profile'];


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
        $query = $sdbh->prepare("
                SELECT pos_staff.staff_id, pos_staff.first_name, pos_staff.last_name from

                (SELECT * FROM `staff_school_relationship` WHERE syear=$syear AND school_id=$school_id) as cur_staff,
                (SELECT staff_id, first_name, last_name from staff WHERE staff.profile_id='2' AND is_disable IS NULL) as pos_staff

                WHERE
                pos_staff.staff_id = cur_staff.staff_id

                ORDER BY pos_staff.last_name ASC
                                ");
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

    $query = $sdbh->prepare("
            SELECT pos_staff.staff_id, pos_staff.first_name, pos_staff.last_name from

            (SELECT * FROM `staff_school_relationship` WHERE syear=$syear AND school_id=$school_id) as cur_staff,
            (SELECT staff_id, first_name, last_name from staff WHERE staff.profile_id='5' AND is_disable IS NULL) as pos_staff

            WHERE
            pos_staff.staff_id = cur_staff.staff_id

            ORDER BY pos_staff.last_name ASC
                    ");
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
