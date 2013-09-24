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
  private $logo="img/LISLogo.png";
  private $school_title="Logos International School";
  private $subtitle="a ministry of Asian Hope";
  private $location="Phnom Penh, Kingdom of Cambodia";
  private $doctitle="2013-2014 Report Card"; //use $syear to make this
  private $phone_number="XXX-XXX-XXXX";
  private $website="logoscambodia.org";

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

	//grading schema - if these are updated, need to redo headers and key
	private $schema_3 = Array(
			'Ch'=>'✔',
			'.'=>' ',
			'selected'=>'✔'
			);
	private $schema_4e = Array(
			'E'=>'E','G'=>'G','S'=>'S','N'=>'N','U'=>'U','NA'=>'NA','.'=>'UG', 'selected'=>'E'
			);
	private $schema_4g = Array(
			'4'=>'4','3'=>'3','2'=>'2','1'=>'1','NA'=>'NA','.'=>'UG', 'selected'=>'4'
			);
	private $schema_logosg = Array(
			'...'=>'---- Achievement ----',
			'A+'=>'A+',
			'A'=>'A',
			'A-'=>'A-',
			'B+'=>'B+',
			'B'=>'B',
			'B-'=>'B-',
			'C+'=>'C+',
			'C'=>'C',
			'C-'=>'C-',
			'D+'=>'D+',
			'D'=>'D',
			'D-'=>'D-',
			'F'=>'F',
			'NA'=>'NA',
			'.'=>'UG',
			'..'=>'---- Effort ----',
			'E'=>'E',
			'G'=>'G',
			'S'=>'S',
			'N'=>'N','U'=>'U',
			'NA'=>'NA',
			'.'=>'UG',
			'selected'=>'A'
			);
	private $schema_logose = Array(
			'E'=>'E','G'=>'G','S'=>'S','N'=>'N','U'=>'U','NA'=>'NA','.'=>'UG', 'selected'=>'E'
			);
    private $effort_schema;
	  private $grade_schema;
	  private $language_id;

function __construct($syear="2013", $sid=null, $template_id="2", $teacher_id="209", $teacher_kh_id="209",$school_id="1"){

    $this->school_id = $school_id;
		$this->syear=$syear;
		$this->sid=$sid;
		$this->template_id=$template_id;
		$this->teacher_id=$teacher_id;
		$this->teacher_kh_id=$teacher_kh_id;
		$this->date = date("d M Y",time());

		$this->language_id=1; //English

		$dbh = $this->connectELDB();
		$sdbh =$this->connectOpenSIS();

		// Good stuff starts here!

		//generate actual student name
		if($sid!=null){
			$query = $sdbh->prepare("SELECT first_name, last_name from students where student_id = '$sid'");
			$query->execute();
			$val = $query->fetch();
			$this->sname = $val['last_name'].", ".$val['first_name'];
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
			$this->effort_schema = $this->schema_logose;
			$this->grade_schema = $this->schema_logosg;
		}
		elseif($this->COLUMNS == 3){
			$this->effort_schema = $this->schema_3;
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

			$q->execute();
			$dures = $q->fetch();


			//load them up
			$sdays[$short_name] = $res['count'];

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

					//does this happen? Move on to the right side
					if($topic_id==$this->HEIGHTLIMIT){
						print("</table>"); //these breaks move the left table up in line with the right, may have to change

						//this is Faith's fault! We'll have to figure out a way to store these in the DB and pull them over.
						$this->printGradeTable(53);
						print("</div><div id=\"A5\" class=\"right\"><table border=1 style=\"width:100%;margin:0px;\">");
						$this->printHeader();
					}

					$this->printRow($row,$topic_id);
					$topic_id++;
				}

				print("</table>");
				$this->printGradeTable(54);
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
				 AND students.is_disable IS NULL AND schedule.end_date IS NULL)
				 as enrolled_students,

				(SELECT course_title, course_period_id FROM course_details WHERE teacher_id = $this->teacher_id) as course


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
	function getEffortSchema(){
		return str_replace("\"","'",json_encode($this->effort_schema));
	}


	//prints the grading header (effort/grade/semester and so on) - expects an open table
	function printHeader(){
		if($this->COLUMNS == 3){
			?>
			<tr><td class = "sectiontitlecenter"></td><td class = "sectiontitlecenterfixed">Yes<br/>ធ្វើបាន</td><td class = "sectiontitlecenterfixed">Developing<br/>កំពុងធ្វើ</td><td class = "sectiontitlecenterfixed">Not Yet<br/>មិនទាន់ធ្វើ</td>
			<?php
		}
		else if($this->COLUMNS ==4){
			?>
			<tr><td class="sectiontitle"><b>Grading Period</b></td>
			<td class = "sectiontitlecenter">Q1</td><td class = "sectiontitlecenter">Q2</td><td class = "sectiontitlecenter">Q3</td><td class = "sectiontitlecenter">Q4</td></tr>
			<?php
		}
	}


	//prints a table row - either grades or a section header
	private function printRow($row,$topic_id){
		$dbh = $this->connectELDB();
		$template_id = $this->template_id;
		$sid = $this->sid;

		$alt_lang = 0; //2 is Khmer, 0 is no alt_lang

		//fetch alt_language text (hard coded for khmer currently)
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
			$truegrades = Array(
					"S1E"=>".",
					"S1G"=>".",
					"S2E"=>".",
					"S2G"=>"."
			);

			foreach($grades as $grade){
				if(strcmp($grade['value'],"Ch") == 0) $grade['value'] = "<img src = \"img\check.png\">";
				switch ($grade){
					//case S1E
					case (strcasecmp($grade['term'],"S1") == 0 && strcasecmp($grade['type'], "E") == 0):
						$truegrades['S1E'] = $grade['value'];
						break;
						// case S1G
					case (strcasecmp($grade['term'],"S1") == 0 && strcasecmp($grade['type'], "G") == 0):
						$truegrades['S1G'] = $grade['value'];
						break;
						// case S2E
					case (strcasecmp($grade['term'],"S2") == 0 && strcasecmp($grade['type'], "E") == 0):
						$truegrades['S2E'] = $grade['value'];
						break;
						// case S2G
					case (strcasecmp($grade['term'],"S2") == 0 && strcasecmp($grade['type'], "G") == 0):
						$truegrades['S2G'] = $grade['value'];
						break;
				}

			}
			print("<tr><td align=\"right\" width=80% class = \"rowtitle\">".$row['text']."<br>".$alt_lang['text']."</td>

					<td align = \"center\"  width=5% class=\"editGrade\" id=\"S1G".$row['topic_id']."\">".$truegrades['S1G']."</td>
					<td align = \"center\"  width=5% class=\"editEffort\" class=\"editGrade\" id=\"S1E".$row['topic_id']."\">".$truegrades['S1E']."</td>
					<td align = \"center\"  width=5% class=\"editGrade\"class=\"editGrade\" id=\"S2G".$row['topic_id']."\">".$truegrades['S2G']."</td>"
			);
			if($this->COLUMNS == 4)
				print("<td align = \"center\" width=5% class=\"editEffort\"class=\"editGrade\" id=\"S2E".$row['topic_id']."\">".$truegrades['S2E']."</td>"
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
      <p class="comment_title" >Q1</p>
      <div class="commentblock" id="C1"><?php print $truecomments['1'];?></div>
      <p class="comment_title">Q2</p>
      <div class="commentblock" id="C2"><?php print $truecomments['2'];?></div>
      <p class="comment_title">Q2</p>
      <div class="commentblock" id="C3"><?php print $truecomments['3'];?></div>
      <p class="comment_title">Q2</p>
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
					<tr>	<td align = "right" style = "width: 60%"><b>Quarter</b></td>
                <td align = "center" style = "width:10%">1</td>
                <td align = "center"  style = "width:10%">2</td>
                <td align = "center"  style = "width:10%">3</td>
                <td align = "center"  style = "width:10%">4</td>
					</tr>
					<tr>	<td align = "center" colspan="5" style ="font-size:normal;"><b>Attendance</b></td></tr>
					<tr>	<td align = "right"><b>Number of School Days</b></td>
						<td align = "center"><?php print $this->sdays['Q1'];?></td>
						<td align = "center"><?php print $this->sdays['Q2'];?></td>
						<td align = "center"><?php print $this->sdays['Q3'];?></td>
						<td align = "center"><?php print $this->sdays['Q4'];?></td>
					</tr>
					<tr>	<td align = "right"><b>Days Absent</b></td>
						<td align = "center"><?php print $this->da['Q1'];?></td>
						<td align = "center"><?php print $this->da['Q2']; ?></td>
						<td align = "center"><?php print $this->da['Q3'];?></td>
						<td align = "center"><?php print $this->da['Q4']; ?></td>
					</tr>
					<tr>	<td align = "right"><b>Days Tardy</b></td>
						<td align = "center"><?php print $this->dt['Q1'];?></td>
						<td align = "center"><?php print $this->dt['Q2'];?></td>
						<td align = "center"><?php print $this->dt['Q3'];?></td>
						<td align = "center"><?php print $this->dt['Q4'];?></td>
					</tr>
				</table>

        <table>
			<tr><td><b>Classroom Teacher's Signature</b></td>	<td class="right">&nbsp;&nbsp;___________________________</td></tr>

      <?php if(!is_null($this->teacher_kh_name)){
        ?><tr><td><b>Khmer Teacher's Signature</b></td><td class = "right">_____________________</td></tr><?php
            }
      ?>
			<tr><td><b>Principal's Signature</b></td>			<td class="right">&nbsp;&nbsp;___________________________</td></tr>
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
			$sql = "SELECT count(*) as count from el_grades WHERE template_id = '$template_id' AND student_id = '$sid' AND type = 'E' AND value NOT LIKE '.'";
			$query = $dbh->prepare($sql);
			$query->execute();
			$res = $query->fetch();
			$count = $res['count'];

			$sql = "SELECT count(*) as count from el_grades WHERE template_id = '$template_id' AND student_id = '$sid' AND type = 'G' AND value NOT LIKE '.'";
			$query = $dbh->prepare($sql);
			$query->execute();
			$res = $query->fetch();
			$count += $res['count'];

			$count /= 2;
		}

		$sql = "SELECT count(*) as count from template_fields WHERE template_id = '$template_id' AND is_graded = 1";
		$query = $dbh->prepare($sql);
		$query->execute();
		$res = $query->fetch();
		$total = $res['count'];

		$percent = ($count/$total)*100;
		return $percent;

	}

	//prints out the grades, comments and title of a given ID in report_card_grades
	 function printGradeTable($schema_id){
		$dbh = $this->connectOpenSIS();
		$q = $dbh->prepare("SELECT * FROM report_card_grades WHERE grade_scale_id=$schema_id");
		$q->execute();
		$res = $q->fetchAll();

		$tq = $dbh->prepare("SELECT title FROM report_card_grade_scales WHERE id=$schema_id");
		$tq->execute();
		$title = $tq->fetch();
		$title = $title['title'];
		//begin the table and print the title
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

	?>		</table><?php

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
