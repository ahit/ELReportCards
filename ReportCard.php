<?php
class ReportCard{
   //Things we need to construct a ReportCard
   private $syear;
   private $sid;
   private $school_id;
   private $template_id;
   private $teacher_id;
   private $teacher_kh_id;
   private $teacher_name;
   private $teacher_kh_name;

  //using $school_id we'll pull the following data
   private $logo="img/AHISLogo.png";
   private $school_title="";
   private $subtitle="a ministry of Asian Hope";
   private $location="Phnom Penh, Kingdom of Cambodia";
   private $doctitle="2013-2014 Report Card"; //use $syear to make this
   private $phone_number="+855-23-885-170";
   private $website="asianhopeschool.org";

   private $f1title = "Grade";
   private $f2title = "Effort";
   private $f3title = "Grade";
   private $f4title = "Effort";

   private $c1title = "English Subjects - S1";
   private $c2title = "Khmer Subjects - S1";
   private $c3title = "English Subjects - S2";
   private $c4title = "Khmer Subjects - S2";

   //total school days by marking period (array keyed with marking period short name)

   //total school days by marking period (array keyed with marking period short name)
   private $sdays;

   //days absent/tardy by marking period (array keyed with marking period short name)
   private $da;
   private $dt;

   private $sname; //Full student name (Surname, Firstname)
   private $grade; //normal name of grade, pulled from template_id (e.g. "Grade 2")

   //comments (duh)
   private $comment1;
   private $comment2;
   private $comment3;
   private $comment4;

   //Date the report card was generated
   private $date;

   private $HEIGHTLIMIT = 21;
   private $COLUMNS = 4;
   private $KEY = 1;

   //special schema for 3 column layouts
   private $schema_3 = Array(
         'Ch'=>'✔',
         '.'=>' ',
         'selected'=>'✔'
         );

   private $grade_schema; //eventual array of grade values and their ids.
   private $grade_schema_id; //an array of a few elements linking to schema ids in reportcard_grades

   private $language_id;
   private $alt_language_id;

function __construct($syear="2013", $sid=null, $template_id="2", $teacher_id="209", $teacher_kh_id="209",$school_id="2"){

    $this->school_id = $school_id;
      $this->syear=$syear;
      $this->sid=$sid;
      $this->template_id=$template_id;
      $this->teacher_id=$teacher_id;
      $this->teacher_kh_id=$teacher_kh_id;
      $this->date = date("d M Y",time());

      $this->language_id=1; //S1-English
      $this->alt_language_id=2; //S1-Khmer

      $dbh = $this->connectELDB();
      $sdbh =$this->connectOpenSIS();

      // Good stuff starts here!

      //Find out if there are custom comment headings
      $query = $dbh->prepare("SELECT * from comment_fields where template_id ='$template_id'");
      $query->execute();
      $res = $query->fetchAll();
      if(count($res)>0){
        foreach($res as $ctitle){
            switch($ctitle['comment_id']){
                case 1:
                    $this->c1title=$ctitle['comment_text'];
                    break;
                case 2:
                    $this->c2title=$ctitle['comment_text'];
                    break;
                case 3:
                    $this->c3title=$ctitle['comment_text'];
                    break;
                case 4:
                    $this->c4title=$ctitle['comment_text'];
                    break;
                default:
                    break;
            }
        }
      }
      
      //generate actual student name
      if($sid!=null){
	 $query = $sdbh->prepare("SELECT first_name, last_name, common_name from students where student_id = '$sid'");
         $query->execute();
         $val = $query->fetch();
         $this->sname = $val['last_name'].", ".$val['first_name'];      
                if(isset($val['common_name'])) $this->sname.=" '".$val['common_name']."'";
      }
      else $this->sname = "Please Select a Student";

      //generate actual teacher name
      $query = $sdbh->prepare("SELECT first_name, last_name from staff where staff_id = '$teacher_id'");
      $query->execute();
      $val = $query->fetch();
      $this->teacher_name = $val['last_name'].", ".$val['first_name'];

      //generate actual teacher_kh name
        if($teacher_kh_id == 0) $this->teacher_kh_name = null;
        else{
         $query = $sdbh->prepare("SELECT first_name, last_name from staff where staff_id = '$teacher_kh_id'");
         $query->execute();
         $val = $query->fetch();
         $this->teacher_kh_name = $val['last_name'].", ".$val['first_name'];
        }

      //Pull the grade name from the template ID and store it in the session
      $sql = "SELECT * from templates WHERE template_id = '$template_id'";
      $query = $dbh->prepare($sql);
      $query->execute();
      $template = $query->fetch();
      $this->grade = $template['template_name'];
      $this->HEIGHTLIMIT = $template['height_limit'];
      $this->COLUMNS = $template['columns'];
      $this->KEY = $template['key'];

      if($this->COLUMNS == 4){
         $this->grade_schema= $this->getGradeArray($template_id);
      }
      elseif($this->COLUMNS == 3){
         $this->grade_schema = $this->schema_3;
      }
      //where do s1 and s2 begin? (will have to re-evaluate for a Logos version where we go by quarter(?))
      $query = $sdbh->prepare("SELECT * FROM marking_periods where school_id=$school_id AND syear = $syear AND parent_id >0");
      $query->execute();
      $mp_result = $query->fetchAll(PDO::FETCH_ASSOC);
      $i = 1;

      //this runs once per marking period -
      foreach($mp_result as $val){
         $sdate = $val['start_date'];
         $edate = $val['end_date'];
         $short_name = $val['short_name'];

         //get total number of days per marking period from attendance calendar (all the way to the end of the year)
         $q = $sdbh->prepare("SELECT COUNT(*) as count from attendance_calendar where syear=$syear AND school_id=$school_id
            AND school_date>='".$sdate."' AND school_date<='".$edate."'");
         $q->execute();
         $res = $q->fetch();

         //get total number of days present for selected student by
         $qda = $sdbh->prepare("
               SELECT count(attendance_period.school_date) as count from attendance_period,
               (SELECT id from attendance_codes where syear=$syear AND school_id =$school_id AND title LIKE \"present\")
               as present_id
               WHERE
               attendance_period.attendance_code = present_id.id AND student_id = $sid AND school_date>='"
               .$sdate."' AND school_date<='".$edate."'");
         $qda->execute();
         $dares = $qda->fetch();

         //get total number of days tardy
         $qdt = $sdbh->prepare("
               SELECT count(attendance_period.school_date) as count from attendance_period,
               (SELECT id from attendance_codes where syear=$syear AND school_id =$school_id AND title LIKE \"late\")
               as late_id
               WHERE
               attendance_period.attendance_code = late_id.id AND student_id = $sid AND school_date>='"
               .$sdate."' AND school_date<='".$edate."'");
         $qdt->execute();
         $dtres = $qdt->fetch();

         //get the total number of unknown days
         $q = $sdbh->prepare("SELECT COUNT(*) as count from attendance_calendar where syear=$syear AND school_id=$school_id
            AND school_date>='".date("Y-m-d",strtotime("Tomorrow"))."' AND school_date<='".$edate."'");

         $q->execute() or die();
         $dures = $q->fetch();


         $sdays[$short_name] = $res['count'];

        //that is, don't show attendance too far in advance 
	if(strtotime("+6 Weeks")>=strtotime($edate)){

         /*
          * days absent are the total days - days present - days tardy
          * (that is: all attendance codes that aren't 'present' or 'late')
          */
         $da[$short_name] = $res['count'] - $dares['count'] - $dtres['count'] - $dures['count'];
         if($da[$short_name]<0) $da[$short_name]=0;

         $dt[$short_name] = $dtres['count'];

         //not strictly necessary
         $du[$short_name] = $dures['count'];
	}
	else{
		$da[$short_name] = "-";
		$dt[$short_name] = "-";
         	$du[$short_name] = $dures['count'];
	}

      }

      //hand the arrays off
      $this->sdays = $sdays;
      $this->dt = $dt;
      $this->da = $da;


   }

   /*
    * Outputs the report card, but leaves most of the styling to an external stylesheet (nominally.. maybe not so much right now)
    */
   function toHTML(){
      $dbh = $this->connectELDB();
      $sdbh = $this->connectOpenSIS();

     ?>
    <div id="A4">
      <div id="A5" class="center"><?php
        //Print the comments on the left hand side
        $this->printComments(); ?>
      </div>
      <div id="A5" class="center"><?php
         $this->printTitle(); ?>
      </div>
     </div>
      <!-- --------------------------------------------Break Here------------------------------------------------------ -->
     <div id="A4">
       <div id="A5" class="left">
    <?php
            print("<table border=1 style=\"width:100%;margin:0px;\">");

            $this->printHeader();

            $topic_id = 0;
            foreach($dbh->query("SELECT * from template_fields WHERE template_id='".$this->template_id."'AND language_id='".$this->language_id."' ORDER BY topic_id") as $row) {

		$topic_id = $row['topic_id'];
               //does this happen? Move on to the right side
               if($topic_id==$this->HEIGHTLIMIT){
                  print("</table>"); //these breaks move the left table up in line with the right, may have to change

                  //this is Faith's fault! We'll have to figure out a way to store these in the DB and pull them over.
                  print("</div><div id=\"A5\" class=\"right\"><table border=1 style=\"width:100%;margin:0px;\">");
                  $this->printHeader();
               }

               $this->printRow($row);
            }

            print("</table>");
            print("<table style=\"width: 100%; position: relative; left:-35px;\"><tr><td>");$this->printGradeTable(1);print("</td>");
            print("<td>");$this->printGradeTable(2);print("</td></tr></table>");
         print("</div></div>");

   }


   /*****************************
    ***** Helper Functions ******
   *****************************/
   //who is enrolled? returns an array with the currently selected student marked as such
   function getEnrolledStudents(){
      $sdbh = $this->connectOpenSIS();
      $syear = $this->syear;
      $grade = $this->grade;
      $sname = $this->sname;
      $teacher_id = $this->teacher_id;

      //grab student names to populate list - magic number school
      $query = $sdbh->prepare("SELECT
            enrolled_students.sid,
            enrolled_students.fname,
            enrolled_students.sname,
            course.course_title

            FROM

            (SELECT students.first_name as fname, students.last_name as sname,
             students.student_id as sid, students.birthdate as DOB,students.is_disable,
             schedule.course_period_id as course_period_id
             FROM students, schedule
             WHERE
             schedule.student_id = students.student_id AND schedule.syear = $this->syear AND schedule.school_id=$this->school_id
             AND students.is_disable IS NULL AND (schedule.end_date IS NULL 
		OR schedule.end_date>'".date('Y-m-d')."'))
             as enrolled_students,

            (SELECT course_title, course_period_id FROM course_details 
		WHERE teacher_id = $this->teacher_id
		AND course_title LIKE '%".$grade."%'
	    ) as course


            WHERE
            enrolled_students.course_period_id = course.course_period_id

            ORDER BY sname ASC");


            $query->execute();
            $students_result = $query->fetchAll(PDO::FETCH_ASSOC);
            $students = array();
            foreach($students_result as $val){
            $name = $val['sname'].", ".$val['fname'];
            $tempsid = $val['sid'];
            $collate = $name.".".$tempsid;
            $students[$collate] = $name;
   }
   $students['selected'] = $sname;

   return $students;
   }

   function getEnrolledStudentsSchema(){
      return str_replace("\"","'",json_encode($this->getEnrolledStudents()));
   }

   function getGradeSchema(){
      return str_replace("\"","'",json_encode($this->grade_schema));
   }

   //prints the grading header (grade/semester and so on) - expects an open table
   function printHeader(){
      if($this->COLUMNS == 3){
         ?>
         <tr><td class = "sectiontitlecenter"></td>
         <td class = "sectiontitlecenterfixed" style="min-width:4.5em;">Yes<br/>ធ្វើបាន</td>
         <td class = "sectiontitlecenterfixed" style="min-width:4.5em;">Developing<br/>កំពុងធ្វើ</td>
         <td class = "sectiontitlecenterfixed" style="min-width:4.5em;">Not Yet<br/>មិនទាន់ធ្វើ</td></tr>
         <?php
      }
      else if($this->COLUMNS ==4){
         ?>
         <tr><td class="sectiontitle"><b>Grading Period</b></td><td class="sectiontitlecenter" colspan=2>S1</td><td class="sectiontitlecenter" colspan=2>S2</td></tr><tr>
         <td class = "sectiontitlecenter"></td>
         <td class = "sectiontitlecenter"><?php print($this->f1title);?></td>
	 <td class = "sectiontitlecenter"><?php print($this->f2title);?></td>
	 <td class = "sectiontitlecenter"><?php print($this->f3title);?></td>
	 <td class = "sectiontitlecenter"><?php print($this->f4title);?></td></tr>
         <?php
      }
   }


   //prints a table row - either grades or a section header
   private function printRow($row){
      $dbh = $this->connectELDB();
      $template_id = $this->template_id;
      $sid = $this->sid;
      $topic_id = $row['topic_id'];

      $alt_lang = $this->alt_language_id;

      //fetch alt_language text
      $text_q = $dbh->prepare("SELECT * from template_fields where topic_id='".$row['topic_id']."' and template_id='".$template_id."' and language_id='".$alt_lang."'");
      $text_q->execute();
      $alt_lang=$text_q->fetch();

      if($row['is_graded']){
         //pull the grades from the DB
         $sql = "SELECT * from el_grades WHERE template_id = '$template_id' AND student_id = '$sid' AND topic_id ='$topic_id'";
         $query = $dbh->prepare($sql);
         $query->execute();
         $grades = $query->fetchAll();

         //default values if things don't exist
		$f1 = ".";
		$f2 = ".";
		$f3 = ".";
		$f4 = ".";

         foreach($grades as $grade){
            switch ($grade){
               //case S1E
               case (strcasecmp($grade['term'],"F1") == 0):
                  $f1 = $grade['value'];
                  break;
                  // case S1G
               case (strcasecmp($grade['term'],"F2") == 0):
                  $f2 = $grade['value'];
                  break;
                  // case S2E
               case (strcasecmp($grade['term'],"F3") == 0):
                  $f3 = $grade['value'];
                  break;
                  // case S2G
               case (strcasecmp($grade['term'],"F4") == 0):
                  $f4 = $grade['value'];
                  break;
            }

         }
         print("<tr><td align=\"right\" width=80% class = \"rowtitle\">".$row['text']."<br>".$alt_lang['text']."</td>

               <td align = \"center\"  width=5% class=\"editGrade\" id=\"F1".$row['topic_id']."\">".$this->grade_schema[$f1]."</td>
               <td align = \"center\"  width=5% class=\"editGrade\" class=\"editGrade\" id=\"F2".$row['topic_id']."\">".$this->grade_schema[$f2]."</td>
               <td align = \"center\"  width=5% class=\"editGrade\"class=\"editGrade\" id=\"F3".$row['topic_id']."\">".$this->grade_schema[$f3]."</td>"
         );
         if($this->COLUMNS == 4)
            print("<td align = \"center\" width=5% class=\"editGrade\"class=\"editGrade\" id=\"F4".$row['topic_id']."\">".$this->grade_schema[$f4]."</td>"
         );

      }
      else{
         print("
         <tr><td colspan = \"5\" class = \"sectiontitle\" width=80%>".$row['text']."<br>".$alt_lang['text']."</td></tr>
         ");
      }
   }

   // prints out the comments on the back of the report card (self contained)
    function printComments(){
      $dbh = $this->connectELDB();

      $template_id = $this->template_id;
      $sid = $this->sid;

      $sql = "SELECT * from el_comments WHERE template_id = '$template_id' AND student_id = '$sid'";
      //print($sql);
      $query = $dbh->prepare($sql);
      $query->execute();
      $comments = $query->fetchAll();
      $truecomments = Array(
            "1"=>"",
            "2"=>"",
            "3"=>"",
            "4"=>"");
      foreach($comments as $comment){
         $truecomments[$comment['comment_id']] = $comment['comment'];
      }

    ?>
      <p class="comment_title">General Comments</p>
      <p class="comment_title" ><?php print($this->c1title);?></p>
      <div class="commentblock" id="C1"><?php print $truecomments['1'];?></div>
      <p class="comment_title"><?php print($this->c2title);?></p>
      <div class="commentblock" id="C2"><?php print $truecomments['2'];?></div>
      <p class="comment_title"><?php print($this->c3title);?></p>
      <div class="commentblock" id="C3"><?php print $truecomments['3'];?></div>
      <p class="comment_title"><?php print($this->c4title);?></p>
      <div class="commentblock" id="C4"><?php print $truecomments['4'];?></div>


    <?php

   }

   // prints all the important stuff on the front page of the report card (self contained)
    function printTitle(){
      ?>

      <p class="logo"><img style = "height:3.5cm" src = "<?php print $this->logo;?>"></p>
      <p class="school_title"><?php print $this->school_title;?></p>
      <p class="subtitle"><?php print $this->subtitle;?></p>
      <p class="location"><?php print $this->location;?></p>
      <p class="doc_title"><?php print $this->doctitle;?></p>
      <p class="phone_number"><?php print $this->phone_number;?></p>
      <p class="website"><?php print $this->website;?></p>

      <table>
         <tr><td><b>Student</b></td><td class="right"><?php print $this->sname;?></td></tr>
         <tr><td><b>Grade</b></td><td class="right"><?php print $this->grade;?></td></tr>
         <tr><td><b>Classroom Teacher</b></td><td class="right"><?php print $this->teacher_name;?></td></tr>

      <?php
        if(!is_null($this->teacher_kh_name)){
          ?>
            <tr><td><b>Khmer Teacher</b></td><td class = "right"><?php print $this->teacher_kh_name;?></td></tr><?php
        } ?>

       <tr><td><b>Date</b></td><td class="right"><?php print $this->date; ?></td></tr>
        </table>


      <table border=1>
               <tr>   <td align = "right" style = "width: 60%"><b>Semester</b></td>
                <td align = "center" style = "width:10%">1</td>
                <td align = "center"  style = "width:10%">2</td>
               </tr>
               <tr>   <td align = "center" colspan="5" style ="font-size:normal;"><b>Attendance</b></td></tr>
               <tr>   <td align = "right"><b>Number of School Days</b></td>
                  <td align = "center"><?php print $this->sdays['SEM1'];?></td>
                  <td align = "center"><?php print $this->sdays['SEM2'];?></td>
               </tr>
               <tr>   <td align = "right"><b>Days Absent</b></td>
                  <td align = "center"><?php print $this->da['SEM1'];?></td>
                  <td align = "center"><?php print $this->da['SEM2']; ?></td>
               </tr>
               <tr>   <td align = "right"><b>Days Tardy</b></td>
                  <td align = "center"><?php print $this->dt['SEM1'];?></td>
                  <td align = "center"><?php print $this->dt['SEM2'];?></td>
               </tr>
            </table>

        <table>
         <tr><td><b>Classroom Teacher's Signature</b></td>   <td class="right">&nbsp;&nbsp;___________________________</td></tr>

      <?php if(!is_null($this->teacher_kh_name)){
        ?><tr><td><b>Khmer Teacher's Signature</b></td><td class = "right">&nbsp;&nbsp;___________________________</td></tr><?php
            }
      ?>
         <tr><td><b>Principal's Signature</b></td>         <td class="right">&nbsp;&nbsp;___________________________</td></tr>
      </table>

      <?php
   }

   function getGrade(){
      return $this->grade;
   }

   function getTeacherName(){
      return $this->teacher_name;
   }

   //return the number of records a student has in the DB
   function hasData($sid){
      $dbh = $this->connectELDB();
      $template_id = $this->template_id;
      $language_id= $this->language_id;

      $sql = "SELECT columns from templates WHERE template_id = '$template_id'";
      $query = $dbh->prepare($sql);
      $query->execute();
      $res = $query->fetch();
      $columns = $res['columns'];

      //
      $count = 0;

      //special case if there are only 3 columns
      if($columns == 3){
         $sql = "SELECT count(*) as count from el_grades WHERE template_id = '$template_id' AND student_id = '$sid' AND value NOT LIKE '.'";
         $query = $dbh->prepare($sql);
         $query->execute();
         $res = $query->fetch();
         $count = $res['count'];
      }
      //otherwise just pull the grades and count those
      else{
         $sql = "SELECT count(*) as count from el_grades WHERE template_id = '$template_id' AND student_id = '$sid'";
         $query = $dbh->prepare($sql);
         $query->execute();
         $res = $query->fetch();
         $count = $res['count'];
      }

      $sql = "SELECT count(*) as count from template_fields WHERE template_id = '$template_id' AND is_graded = 1 and language_id='$language_id'";
      $query = $dbh->prepare($sql);
      $query->execute();
      $res = $query->fetch();
      $total = $res['count'];

      $percent = ($count/$total)*100;
      return $percent;

   }

   //returns an array with acceptable grading values
   function getGradeArray($template_id){

      $dbh = $this->connectELDB();
      $gquery = $dbh->prepare("SELECT * from template_grading WHERE template_id=$template_id");
      $gquery->execute();
      $schemas = $gquery->fetchAll();
      $ret = Array();

     foreach($schemas as $schema){
	$grade_scale_id = $schema['reportcard_grade_scale_id'];
	$sdbh = $this->connectOpenSIS();
    	$q = $sdbh->prepare("SELECT title, id FROM report_card_grades WHERE grade_scale_id=$grade_scale_id order by grade_scale_id asc");
    	$q->execute();
    	$res = $q->fetchAll();

  	$tq = $sdbh->prepare("SELECT title FROM report_card_grade_scales WHERE id=$schema_id");
        $tq->execute();
        $title = $tq->fetch();
        $title = $title['title'];

	

	//slightly naive, but the nature of the table should guarantee no colissions.
	foreach($res as $val){
		$key = $val['id'];
		$ret[$key]=$val['title'];
	}
      }
	
	//include 'no value' key	
	$ret['.']=".";

	return $ret;
    }

   //prints out the grades, comments and title for the row number given 
    function printGradeTable($row){
      $sdbh = $this->connectOpenSIS();
      $dbh = $this->connectELDB();
      $template_id=$this->template_id;

      $gquery = $dbh->prepare("SELECT * from template_grading WHERE template_id=$template_id");
      $gquery->execute();

      for($i=0;$i<$row;$i++){ $s=$gquery->fetch(); $schema_id=$s['reportcard_grade_scale_id'];}

      $q = $sdbh->prepare("SELECT * FROM report_card_grades WHERE grade_scale_id=$schema_id AND comment NOT LIKE ''");
      $q->execute();
      $res = $q->fetchAll();

      $tq = $sdbh->prepare("SELECT title FROM report_card_grade_scales WHERE id=$schema_id");
      $tq->execute();
      $title = $tq->fetch();
      $title = $title['title'];
      //begin the table and print the title
      if($title){
      ?>
         <table border=1 style="width:90%;">
            <tr class = "sectiontitlecenter">
               <td colspan="2"><?php print $title?></td>
            </tr>
      <?php
      foreach($res as $row){
         //$row['title'] $row['comment']
         ?>
            <tr class = "row"><td style="width:3%" align="center"><?php print($row['title']);?></td>
               <td><?php print($row['comment']);?></td>
            </tr>
         <?php
      }

   ?>      </table><?php
   } //end if($title)

   }
   //these are terrible.
   private function connectOpenSIS(){
      include("data.php");
      $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$DatabaseName;
      return(new PDO($dsn, "$DatabaseUsername", "$DatabasePassword"));
   }
   private function connectELDB(){
      include("data.php");
      $dsn = $DatabaseType.":host=".$DatabaseServer.";dbname=".$ELDatabaseName;
      $dbh = new PDO($dsn, "$DatabaseUsername", "$DatabasePassword");
      $dbh->query('SET NAMES utf8');
      return($dbh);
   }
}
?>
