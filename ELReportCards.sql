-- MySQL dump 10.13  Distrib 5.5.32, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: el_reportcards
-- ------------------------------------------------------
-- Server version	5.5.32-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `el_comments`
--

DROP TABLE IF EXISTS `el_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `el_comments` (
  `template_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `template_id` (`template_id`,`student_id`,`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `el_comments`
--

LOCK TABLES `el_comments` WRITE;
/*!40000 ALTER TABLE `el_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `el_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `el_grades`
--

DROP TABLE IF EXISTS `el_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `el_grades` (
  `template_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `term` varchar(2) NOT NULL,
  `type` varchar(1) NOT NULL,
  `value` varchar(2) NOT NULL,
  UNIQUE KEY `template_id` (`template_id`,`topic_id`,`student_id`,`term`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `el_grades`
--

LOCK TABLES `el_grades` WRITE;
/*!40000 ALTER TABLE `el_grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `el_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `language_id` int(2) DEFAULT NULL,
  `description` varchar(32) DEFAULT NULL,
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English'),(2,'Khmer');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_fields`
--

DROP TABLE IF EXISTS `template_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_fields` (
  `template_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `language_id` int(2) NOT NULL,
  `text` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_graded` tinyint(1) NOT NULL,
  UNIQUE KEY `template_id` (`template_id`,`topic_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_fields`
--

LOCK TABLES `template_fields` WRITE;
/*!40000 ALTER TABLE `template_fields` DISABLE KEYS */;
INSERT INTO `template_fields` VALUES (1,0,1,'English Language Arts',0),(1,0,2,'អក្សរសាស្ត្រអង់គ្លេស',0),(1,1,1,'Speaking and Listening',1),(1,1,2,'ការនិយាយនិងការស្តាប់',1),(1,2,1,'Reading',1),(1,2,2,'ការអាន',1),(1,3,1,'Writing (including spelling)',1),(1,3,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(1,4,1,'Khmer Language Arts',0),(1,4,2,'អក្សរសាស្ត្រខ្មែរ',0),(1,5,1,'Speaking and Listening',1),(1,5,2,'ការនិយាយនិងការស្តាប់',1),(1,6,1,'Reading',1),(1,6,2,'ការអាន',1),(1,7,1,'Writing (including spelling)',1),(1,7,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(1,8,1,'Mathematics',0),(1,8,2,'គណិតវិទ្យា',0),(1,9,1,'Numbers',1),(1,9,2,'លេខ',1),(1,10,1,'Shape, Space and Measure',1),(1,10,2,'រូបរាង,ចន្លោះ និងរង្វាស់ប្រវែង',1),(1,11,1,'Data Handling',1),(1,11,2,'ការប្រើទិន្នន័យ',1),(1,12,1,'Problem Solving',1),(1,12,2,'ការដោះស្រាយលំហាត់បញ្ហា',1),(1,13,1,'',0),(1,13,2,'',0),(1,14,1,'Topic (Science, Social Studies, Humanities, Art)',1),(1,14,2,'ប្រធានបទ (វិទ្យាសាស្ត្រ,សិក្សាសង្គម,មនុស្សសាស្ត្រ,សិល្បៈ)',1),(1,15,1,'Performing Arts',1),(1,15,2,'ស្នាដៃសិល្បៈ',1),(1,16,1,'Physical Education',1),(1,16,2,'ការសិក្សាពីរូបរាងកាយ',1),(1,17,1,'Study and Learning Skills',0),(1,17,2,'ជំនាញសិក្សានិងការរៀនសូត្រ',0),(1,18,1,'Works independently',1),(1,18,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯង',1),(1,19,1,'Plays well in groups',1),(1,19,2,'កិច្ចការល្អជាក្រុម',1),(1,20,1,'Plays cooperatively with others',1),(1,20,2,'ការសហគ្នាក្នុងការលេងជាមួយអ្នកដ៏ទៃ',1),(1,21,1,'Follows directions',1),(1,21,2,'ស្តាប់ការណែនាំបង្រៀន',1),(1,22,1,'Organizes and looks after belongings',1),(1,22,2,'ការរៀបចំនិងថែរក្សារបស់​ផ្ទាល់ខ្លួន',1),(1,23,1,'Perseveres in problem solving',1),(1,23,2,'ព្យាយាមក្នុងការដោះស្រាយបញ្ហា',1),(1,24,1,'Makes good use of time',1),(1,24,2,'ប្រើប្រាស់ពេលវេលាបានល្អ',1),(1,25,1,'Seeks help when needed',1),(1,25,2,'ស្វែងរកជំនួយនៅពេលត្រូវការ',1),(1,26,1,'Completes classwork',1),(1,26,2,'បញ្ចប់កិច្ចការក្នុងថ្នាក់',1),(1,27,1,'Works neatly and carefully',1),(1,27,2,'កិច្ចការស្អាតនិងប្រុងប្រយ័ត្ន',1),(2,0,1,'Language Development',0),(2,0,2,'ការអភិវឌ្ឈន៍ភាសាសាស្ត្រ ',0),(2,1,1,'Knows names of things in topic time in English',1),(2,1,2,'ដឹងពីឈ្មោះវត្ថុក្នុងប្រធានបទជាភាសាអង់គ្លេស',1),(2,2,1,'Knows names of things in topic in Khmer',1),(2,2,2,'ដឹងពីឈ្មោះវត្ថុក្នុងប្រធានបទជាភាសាខ្មែរ',1),(2,3,1,'Follows Simple instructions',1),(2,3,2,'ធ្វើតាមការណែនាំបង្រៀនសាមញ្ញ',1),(2,4,1,'Recognizes own name',1),(2,4,2,'ស្គាល់ឈ្មោះរបស់ខ្លួន',1),(2,5,1,'Volunteers responses to discussion',1),(2,5,2,'ស្ម័គ្រចិត្តឆ្លើយតបក្នុងការពិភាគ្សា',1),(2,6,1,'Listens to stories',1),(2,6,2,'ស្តាប់ការនិទានរឿង',1),(2,7,1,'Actively listens to adults and classmates',1),(2,7,2,'សកម្មភាពស្តាប់អ្នកវ័យជំទង់និង​មិត្តរួមថ្នាក់',1),(2,8,1,'Holds books correctly and generally takes an interest in books',1),(2,8,2,'កាន់សៀវភៅបានត្រឹមត្រូវនិងមានចំណាប់អារម្មណ៍មួយលើសៀវភៅ',1),(2,9,1,'Holds a pencil correctly',1),(2,9,2,'កាន់ខ្មៅដៃបានត្រឹមត្រូវ',1),(2,10,1,'Can complete puzzles',1),(2,10,2,'អាចបញ្ចប់ល្បែងដាក់បំពេញ',1),(2,11,1,'Mathematical Development',0),(2,11,2,'ការអភិវឌ្ឈន៍ផ្នែកគណិតវិទ្យា',0),(2,12,1,'Recognizes basic shapes',1),(2,12,2,'ស្គាល់រូបរាងជាមូលដ្ឋាន',1),(2,13,1,'Counts reliably in English (1-20)',1),(2,13,2,'អាចរាប់លេខជាភាសាអង់គ្លេស(ពីលេខ១ដល់លេខ២០)',1),(2,14,1,'Counts reliably in Khmer (1-20)',1),(2,14,2,'អាចរាប់លេខជាភាសាខ្មែរ(ពីលេខ១ដល់លេខ២០)',1),(2,15,1,'Recognizes numbers',1),(2,15,2,'ស្គាល់ពីចំនួន',1),(2,16,1,'Is able to sort objects',1),(2,16,2,'អាចចាត់ថ្នាក់វត្ថុ',1),(2,17,1,'Sorts according to size',1),(2,17,2,'អាចចាត់ថ្នាក់ទៅតាមទំហំ',1),(2,18,1,'Joins in number rhymes, songs and games',1),(2,18,2,'ចូលរួមរាប់លេខទៅតាមពាក្យជួន ចំរៀង និងល្បែង',1),(2,19,1,'Knowledge and understanding of the world',0),(2,19,2,'ចំណេះដឹងនិងការយល់ពីពិភពលោក',0),(2,20,1,'Knows the names for the members of the family',1),(2,20,2,'ដឹងឈ្មោះរបស់សមាជិកក្នុងគ្រួសារ',1),(2,21,1,'Can name the five senses',1),(2,21,2,'អាចប្រាប់ឈ្មោះញាណទាំង៥',1),(2,22,1,'Can identify features of the different seasons',1),(2,22,2,'អាចសំគាល់លក្ខណៈរបស់រដូវផ្សេងៗគ្នា',1),(2,23,1,'Identifies different transport',1),(2,23,2,'សំគាល់មធ្យោបាយដឹកជញ្ជូនផ្សេងៗគ្នា',1),(2,24,1,'Knows the different features of birds and fish',1),(2,24,2,'ដឹងពីលក្ខណៈផ្សេងៗគ្នារបស់សត្វបក្សីនិងសត្វត្រី',1),(2,25,1,'Physical Development',0),(2,25,2,'ការអភិវឌ្ឈន៍ខាងរូបរាងកាយ',0),(2,26,1,'Has well developed fine motor skills',1),(2,26,2,'មានការអភិវឌ្ឈន៍ល្អលើជំនាញប្រើដៃ',1),(2,27,1,'Enjoys physical activities',1),(2,27,2,'ចូលចិត្តសកម្មភាពធ្វើចលនារូបរាងកាយ',1),(2,28,1,'Has well developed gross motor skills',1),(2,28,2,'មានការអភិវឌ្ឈន៍លើជំនាញរូបរាយកាយ',1),(2,29,1,'Creative Development',0),(2,29,2,'ការអភិវឌ្ឍន៍ការឆ្នៃប្រឌិត',0),(2,30,1,'Can identify basic colours',1),(2,30,2,'អាចសំគាល់ពណ៌មូលដ្ឋាន',1),(2,31,1,'Joins in with music and movement activities',1),(2,31,2,'ចូលរួមជាមួយតន្ត្រីនិងសកម្មភាពចលនា',1),(2,32,1,'Likes to do art and craft activities',1),(2,32,2,'ចូលចិត្តធ្វើសកម្មភាពសិល្បៈនិងសិល្បៈរូបភាព',1),(2,33,1,'Life Skills / Personal Development',0),(2,33,2,'បទពិសោធន៍ជីវិតនិងការអភិវឌ្ឈន៍ផ្ទាល់ខ្លួន',0),(2,34,1,'Listens to my teacher',1),(2,34,2,'ស្តាប់គ្រូរបស់ខ្លួន',1),(2,35,1,'Can work independently and concentrate until my tasks are finished',1),(2,35,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯងនិងផ្តោតការយកចិត្តទុកដាក់រហូតដល់កិច្ចការរបស់ខ្លួនបានបញ្ចប់',1),(2,36,1,'Can use my own ideas',1),(2,36,2,'អាចប្រើគំនិតផ្ទាល់របស់ខ្លួន',1),(2,37,1,'Understands and follow rules and routines',1),(2,37,2,'យល់និងធ្វើតាមច្បាប់និងកាលវិភាគ',1),(2,38,1,'Can share and take turns',1),(2,38,2,'អាចចែករំលែកនិងដឹងពីវេន',1),(2,39,1,'Tries new tasks',1),(2,39,2,'សាកល្បងធ្វើកិច្ចការថ្មី',1),(3,0,1,'Language Development',0),(3,0,2,'ការអភិវឌ្ឈន៍ភាសាសាស្ត្រ ',0),(3,1,1,'Knows names of things in topic time in English',1),(3,1,2,'ដឹងពីឈ្មោះវត្ថុក្នុងប្រធានបទជាភាសាអង់គ្លេស',1),(3,2,1,'Knows names of things in topic in Khmer',1),(3,2,2,'ដឹងពីឈ្មោះវត្ថុក្នុងប្រធានបទជាភាសាខ្មែរ',1),(3,3,1,'Follows Simple instructions',1),(3,3,2,'ធ្វើតាមការណែនាំបង្រៀនសាមញ្ញ',1),(3,4,1,'Recognizes own name',1),(3,4,2,'ស្គាល់ឈ្មោះរបស់ខ្លួន',1),(3,5,1,'Volunteers responses to discussion',1),(3,5,2,'ស្ម័គ្រចិត្តឆ្លើយតបក្នុងការពិភាគ្សា',1),(3,6,1,'Listens to stories',1),(3,6,2,'ស្តាប់ការនិទានរឿង',1),(3,7,1,'Actively listens to adults and classmates',1),(3,7,2,'សកម្មភាពស្តាប់អ្នកវ័យជំទង់និង​មិត្តរួមថ្នាក់',1),(3,8,1,'Holds books correctly and generally takes an interest in books',1),(3,8,2,'កាន់សៀវភៅបានត្រឹមត្រូវនិងមានចំណាប់អារម្មណ៍មួយលើសៀវភៅ',1),(3,9,1,'Holds a pencil correctly',1),(3,9,2,'កាន់ខ្មៅដៃបានត្រឹមត្រូវ',1),(3,10,1,'Can complete puzzles',1),(3,10,2,'អាចបញ្ចប់ល្បែងដាក់បំពេញ',1),(3,11,1,'Mathematical Development',0),(3,11,2,'ការអភិវឌ្ឈន៍ផ្នែកគណិតវិទ្យា',0),(3,12,1,'Recognizes basic shapes',1),(3,12,2,'ស្គាល់រូបរាងជាមូលដ្ឋាន',1),(3,13,1,'Counts reliably in English (1-10)',1),(3,13,2,'អាចរាប់លេខជាភាសាអង់គ្លេស(ពីលេខ១ដល់លេខ១០)',1),(3,14,1,'Counts reliably in Khmer (1-10)',1),(3,14,2,'អាចរាប់លេខជាភាសាខ្មែរ(ពីលេខ១ដល់លេខ១០)',1),(3,15,1,'Recognizes numbers',1),(3,15,2,'ស្គាល់ពីចំនួន',1),(3,16,1,'Is able to sort objects',1),(3,16,2,'អាចចាត់ថ្នាក់វត្ថុ',1),(3,17,1,'Sorts according to size',1),(3,17,2,'អាចចាត់ថ្នាក់ទៅតាមទំហំ',1),(3,18,1,'Joins in number rhymes, songs and games',1),(3,18,2,'ចូលរួមរាប់លេខទៅតាមពាក្យជួន ចំរៀង និងល្បែង',1),(3,19,1,'Knowledge and understanding of the world',0),(3,19,2,'ចំណេះដឹងនិងការយល់ពីពិភពលោក',0),(3,20,1,'Can identify different parts of the body',1),(3,20,2,'អាចសំគាល់ផ្នែកផ្សេងៗរបស់រូបរាងកាយ',1),(3,21,1,'Can name different animals',1),(3,21,2,'អាចប្រាប់ប្រភេទសត្វផ្សេងៗគ្នា',1),(3,22,1,'Can name different fruits and vegetables',1),(3,22,2,'អាចប្រាប់ប្រភេទផ្លែឈើនិងបន្លែផ្សេងៗគ្នា',1),(3,23,1,'Identifies different vehicles',1),(3,23,2,'សំគាល់យានជំនិះផ្សេងៗគ្នា',1),(3,24,1,'Physical Development',0),(3,24,2,'ការអភិវឌ្ឈន៍ខាងរូបរាងកាយ',0),(3,25,1,'Has well developed fine motor skills',1),(3,25,2,'មានការអភិវឌ្ឈន៍ល្អលើជំនាញប្រើដៃ',1),(3,26,1,'Enjoys physical activities',1),(3,26,2,'ចូលចិត្តសកម្មភាពធ្វើចលនារូបរាងកាយ',1),(3,27,1,'Has well developed gross motor skills',1),(3,27,2,'មានការអភិវឌ្ឈន៍លើជំនាញរូបរាយកាយ',1),(3,28,1,'Creative Development',0),(3,28,2,'ការអភិវឌ្ឍន៍ការឆ្នៃប្រឌិត',0),(3,29,1,'Can identify basic colours',1),(3,29,2,'អាចសំគាល់ពណ៌មូលដ្ឋាន',1),(3,30,1,'Joins in with music and movement activities',1),(3,30,2,'ចូលរួមជាមួយតន្ត្រីនិងសកម្មភាពចលនា',1),(3,31,1,'Likes to do art and craft activities',1),(3,31,2,'ចូលចិត្តធ្វើសកម្មភាពសិល្បៈនិងសិល្បៈរូបភាព',1),(3,32,1,'Life Skills / Personal Development',0),(3,32,2,'បទពិសោធន៍ជីវិតនិងការអភិវឌ្ឈន៍ផ្ទាល់ខ្លួន',0),(3,33,1,'Listens to my teacher',1),(3,33,2,'ស្តាប់គ្រូរបស់ខ្លួន',1),(3,34,1,'Can work independently and concentrate until my tasks are finished',1),(3,34,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯងនិងផ្តោតការយកចិត្តទុកដាក់រហូតដល់កិច្ចការរបស់ខ្លួនបានបញ្ចប់',1),(3,35,1,'Can use my own ideas',1),(3,35,2,'អាចប្រើគំនិតផ្ទាល់របស់ខ្លួន',1),(3,36,1,'Understands and follow rules and routines',1),(3,36,2,'យល់និងធ្វើតាមច្បាប់និងកាលវិភាគ',1),(3,37,1,'Can share and take turns',1),(3,37,2,'អាចចែករំលែកនិងដឹងពីវេន',1),(3,38,1,'Tries new tasks',1),(3,38,2,'សាកល្បងធ្វើកិច្ចការថ្មី',1),(4,0,1,'Language Development',0),(4,0,2,'ការអភិវឌ្ឈន៍ភាសាសាស្ត្រ ',0),(4,1,1,'Understands and uses new words',1),(4,1,2,'យល់និងប្រើពាក្យថ្មី',1),(4,2,1,'Recognize, read and write name',1),(4,2,2,'ការស្គាល់ ការអាន  និងការសរសេរឈ្មាះ',1),(4,3,1,'Beginning to share ideas',1),(4,3,2,'ចាប់ផ្តើមចែករំលែកគំនិត',1),(4,4,1,'Uses appropriate spelling in written work',1),(4,4,2,'ប្រើការប្រកបត្រឹមត្រូវក្នុងកិច្ចការសរសេរ',1),(4,5,1,'Writes letters correctly',1),(4,5,2,'សរសេរអក្សរបានត្រឹមត្រូវ',1),(4,6,1,'Mastering phonics',1),(4,6,2,'ពូកែខាងបញ្ជេញសំលេង',1),(4,7,1,'Actively listens to adults and classmates',1),(4,7,2,'សកម្មភាពស្តាប់អ្នកវ័យជំទង់និង​មិត្តរួមថ្នាក់',1),(4,8,1,'Mathematical Development',0),(4,8,2,'ការអភិវឌ្ឈន៍ផ្នែកគណិតវិទ្យា',0),(4,9,1,'Number and Number Sense',1),(4,9,2,'លេខ និងការដឹងដោយញាណពីលេខ',1),(4,10,1,'Able to identify and use positional words',1),(4,10,2,'អាចសំគាល់ពាក្យនិងអាចប្រើពាក្យដាក់ត្រូវកន្លែង',1),(4,11,1,'Measurement',1),(4,11,2,'រង្វាស់ប្រវែង',1),(4,12,1,'Understands Patterns',1),(4,12,2,'ការយល់ដឹងពីការដាក់ជាគំរូ',1),(4,13,2,'អាចប្រាប់ពីពេលវេលា',1),(4,14,1,'Beginning to understand addition/subtraction',1),(4,14,2,'ការចាប់ផ្តើមយល់ពីវិធីបូកលេខ/ដកលេខ',1),(4,15,1,'Beginning to understand money',1),(4,15,2,'ការចាប់ផ្តើមស្គាល់ពីសន្លឹកប្រាក់',1),(4,16,1,'Khmer Language Arts',0),(4,16,2,'អក្សរសាស្ត្រខ្មែរ',0),(4,17,1,'Learning alphabet sounds',1),(4,17,2,'ការរៀនពីការបញ្ជេញសំលេងតាមលំដាប់អក្សរ',1),(4,18,1,'Understands and uses new words',1),(4,18,2,'យល់និងប្រើពាក្យថ្មី',1),(4,19,1,'Beginning to share ideas',1),(4,19,2,'ចាប់ផ្តើមចែករំលែកគំនិត',1),(4,20,1,'Actively listens to adults and classmates',1),(4,20,2,'សកម្មភាពស្តាប់អ្នកវ័យជំទង់និង​មិត្តរួមថ្នាក់',1),(4,21,1,'Topic',0),(4,21,2,'ប្រធានបទ',0),(4,22,1,'Can discuss about current topic',1),(4,22,2,'អាចពិភាគ្សាអំពីប្រធានបទទើបរៀនថ្មីៗ',1),(4,23,1,'Understands vocabulary used in current topic',1),(4,23,2,'យល់ពាក្យគន្លឹះដែលបានប្រើក្នុងប្រធានបទទើបរៀនថ្មីៗ',1),(4,24,1,'Physical Development',0),(4,24,2,'ការអភិវឌ្ឈន៍ខាងរូបរាងកាយ',0),(4,25,1,'Demonstrates fine motor control and coordination',1),(4,25,2,'បង្ហាញពីជំនាញប្រើដៃនិងប្រើត្រូវរបៀប',1),(4,26,1,'Moves in confidence in a variety of ways',1),(4,26,2,'ធ្វើចលនាយ៉ាងជឿជាក់ក្នុងសកម្មភាពផ្សេងៗគ្នាមួយ',1),(4,27,1,'Creative Development (Music and Art)',0),(4,27,2,'ការអភិវឌ្ឈន៍ភាពឆ្នៃប្រឌិត (តន្ត្រីនិងសិល្បៈ)',0),(4,28,1,'Enjoys music lessons',1),(4,28,2,'ចូលចិត្តមេរៀនតន្ត្រី',1),(4,29,1,'Explores colour and textures in creative activities',1),(4,29,2,'បង្ហាញការប្រើពណ៌និងវាយនភាពក្នុងសកម្មភាពឆ្នៃប្រឌិត',1),(4,30,1,'Life Skills / Personal Development',0),(4,30,2,'បទពិសោធន៍ជីវិតនិងការអភិវឌ្ឈន៍ផ្ទាល់ខ្លួន',0),(4,31,1,'Can express needs & feelings in appropriate ways',1),(4,31,2,'អាចប្រាប់ពីតំរូវការនិងអារម្មណ៍តាមរបៀបដែលត្រឹមត្រូវ',1),(4,32,1,'Understands the need for good behaviour',1),(4,32,2,'យល់ពីតំរូវការក្នុងអត្តចរិកដ៏ល្អ',1),(4,33,1,'Shows interest in activities',1),(4,33,2,'បង្ហាញចំណាប់អារម្មណ៍ក្នុងសកម្មភាពសិក្សា',1),(4,34,1,'Works independently',0),(4,34,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯង',0),(4,35,1,'Works well in groups',1),(4,35,2,'ធ្វើកិច្ចការក្នុងក្រុមបានល្អ',1),(4,36,1,'Organized and looks after personal and school belongings',1),(4,36,2,'ការរៀបចំនិងថែរក្សារបស់​ផ្ទាល់ខ្លួននិងរបស់សាលា',1),(4,37,1,'Seeks help when needed',1),(4,37,2,'ស្វែងរកជំនួយនៅពេលត្រូវការ',1),(5,0,1,'English Language Arts',0),(5,0,2,'អក្សរសាស្ត្រអង់គ្លេស',0),(5,1,1,'Speaking and Listening',1),(5,1,2,'ការនិយាយនិងការស្តាប់',1),(5,2,1,'Reading',1),(5,2,2,'ការអាន',1),(5,3,1,'Writing (including spelling)',1),(5,3,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(5,4,1,'Khmer Language Arts',0),(5,4,2,'អក្សរសាស្ត្រខ្មែរ',0),(5,5,1,'Speaking and Listening',1),(5,5,2,'ការនិយាយនិងការស្តាប់',1),(5,6,1,'Reading',1),(5,6,2,'ការអាន',1),(5,7,1,'Writing (including spelling)',1),(5,7,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(5,8,1,'Mathematics',0),(5,8,2,'គណិតវិទ្យា',0),(5,9,1,'Numbers',1),(5,9,2,'លេខ',1),(5,10,1,'Shape, Space and Measure',1),(5,10,2,'រូបរាង,ចន្លោះ និងរង្វាស់ប្រវែង',1),(5,11,1,'Data Handling',1),(5,11,2,'ការប្រើទិន្នន័យ',1),(5,12,1,'Problem Solving',1),(5,12,2,'ការដោះស្រាយលំហាត់បញ្ហា',1),(5,13,1,'',0),(5,13,2,'',0),(5,14,1,'Topic (Science, Social Studies, Humanities, Art)',1),(5,14,2,'ប្រធានបទ (វិទ្យាសាស្ត្រ,សិក្សាសង្គម,មនុស្សសាស្ត្រ,សិល្បៈ)',1),(5,15,1,'Performing Arts',1),(5,15,2,'ស្នាដៃសិល្បៈ',1),(5,16,1,'Physical Education',1),(5,16,2,'ការសិក្សាពីរូបរាងកាយ',1),(5,17,1,'Study and Learning Skills',0),(5,17,2,'ជំនាញសិក្សានិងការរៀនសូត្រ',0),(5,18,1,'Works independently',1),(5,18,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯង',1),(5,19,1,'Plays well in groups',1),(5,19,2,'កិច្ចការល្អជាក្រុម',1),(5,20,1,'Plays cooperatively with others',1),(5,20,2,'ការសហគ្នាក្នុងការលេងជាមួយអ្នកដ៏ទៃ',1),(5,21,1,'Follows directions',1),(5,21,2,'ស្តាប់ការណែនាំបង្រៀន',1),(5,22,1,'Organizes and looks after belongings',1),(5,22,2,'ការរៀបចំនិងថែរក្សារបស់​ផ្ទាល់ខ្លួន',1),(5,23,1,'Perseveres in problem solving',1),(5,23,2,'ព្យាយាមក្នុងការដោះស្រាយបញ្ហា',1),(5,24,1,'Makes good use of time',1),(5,24,2,'ប្រើប្រាស់ពេលវេលាបានល្អ',1),(5,25,1,'Seeks help when needed',1),(5,25,2,'ស្វែងរកជំនួយនៅពេលត្រូវការ',1),(5,26,1,'Completes classwork',1),(5,26,2,'បញ្ចប់កិច្ចការក្នុងថ្នាក់',1),(5,27,1,'Works neatly and carefully',1),(5,27,2,'កិច្ចការស្អាតនិងប្រុងប្រយ័ត្ន',1),(6,0,1,'English Language Arts',0),(6,0,2,'អក្សរសាស្ត្រអង់គ្លេស',0),(6,1,1,'Speaking and Listening',1),(6,1,2,'ការនិយាយនិងការស្តាប់',1),(6,2,1,'Reading',1),(6,2,2,'ការអាន',1),(6,3,1,'Writing (including spelling)',1),(6,3,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(6,4,1,'Khmer Language Arts',0),(6,4,2,'អក្សរសាស្ត្រខ្មែរ',0),(6,5,1,'Speaking and Listening',1),(6,5,2,'ការនិយាយនិងការស្តាប់',1),(6,6,1,'Reading',1),(6,6,2,'ការអាន',1),(6,7,1,'Writing (including spelling)',1),(6,7,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(6,8,1,'Mathematics',0),(6,8,2,'គណិតវិទ្យា',0),(6,9,1,'Numbers',1),(6,9,2,'លេខ',1),(6,10,1,'Shape, Space and Measure',1),(6,10,2,'រូបរាង,ចន្លោះ និងរង្វាស់ប្រវែង',1),(6,11,1,'Data Handling',1),(6,11,2,'ការប្រើទិន្នន័យ',1),(6,12,1,'Problem Solving',1),(6,12,2,'ការដោះស្រាយលំហាត់បញ្ហា',1),(6,13,1,'',0),(6,13,2,'',0),(6,14,1,'Topic (Science, Social Studies, Humanities, Art)',1),(6,14,2,'ប្រធានបទ (វិទ្យាសាស្ត្រ,សិក្សាសង្គម,មនុស្សសាស្ត្រ,សិល្បៈ)',1),(6,15,1,'Performing Arts',1),(6,15,2,'ស្នាដៃសិល្បៈ',1),(6,16,1,'Physical Education',1),(6,16,2,'ការសិក្សាពីរូបរាងកាយ',1),(6,17,1,'Study and Learning Skills',0),(6,17,2,'ជំនាញសិក្សានិងការរៀនសូត្រ',0),(6,18,1,'Works independently',1),(6,18,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯង',1),(6,19,1,'Plays well in groups',1),(6,19,2,'កិច្ចការល្អជាក្រុម',1),(6,20,1,'Plays cooperatively with others',1),(6,20,2,'ការសហគ្នាក្នុងការលេងជាមួយអ្នកដ៏ទៃ',1),(6,21,1,'Follows directions',1),(6,21,2,'ស្តាប់ការណែនាំបង្រៀន',1),(6,22,1,'Organizes and looks after belongings',1),(6,22,2,'ការរៀបចំនិងថែរក្សារបស់​ផ្ទាល់ខ្លួន',1),(6,23,1,'Perseveres in problem solving',1),(6,23,2,'ព្យាយាមក្នុងការដោះស្រាយបញ្ហា',1),(6,24,1,'Makes good use of time',1),(6,24,2,'ប្រើប្រាស់ពេលវេលាបានល្អ',1),(6,25,1,'Seeks help when needed',1),(6,25,2,'ស្វែងរកជំនួយនៅពេលត្រូវការ',1),(6,26,1,'Completes classwork',1),(6,26,2,'បញ្ចប់កិច្ចការក្នុងថ្នាក់',1),(6,27,1,'Works neatly and carefully',1),(6,27,2,'កិច្ចការស្អាតនិងប្រុងប្រយ័ត្ន',1),(7,0,1,'English Language Arts',0),(7,0,2,'អក្សរសាស្ត្រអង់គ្លេស',0),(7,1,1,'Speaking and Listening',1),(7,1,2,'ការនិយាយនិងការស្តាប់',1),(7,2,1,'Reading',1),(7,2,2,'ការអាន',1),(7,3,1,'Writing (including spelling)',1),(7,3,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(7,4,1,'Khmer Language Arts',0),(7,4,2,'អក្សរសាស្ត្រខ្មែរ',0),(7,5,1,'Speaking and Listening',1),(7,5,2,'ការនិយាយនិងការស្តាប់',1),(7,6,1,'Reading',1),(7,6,2,'ការអាន',1),(7,7,1,'Writing (including spelling)',1),(7,7,2,'ការសរសេរ(រួមទាំងការប្រកបពាក្យ)',1),(7,8,1,'Mathematics',0),(7,8,2,'គណិតវិទ្យា',0),(7,9,1,'Numbers',1),(7,9,2,'លេខ',1),(7,10,1,'Shape, Space and Measure',1),(7,10,2,'រូបរាង,ចន្លោះ និងរង្វាស់ប្រវែង',1),(7,11,1,'Data Handling',1),(7,11,2,'ការប្រើទិន្នន័យ',1),(7,12,1,'Problem Solving',1),(7,12,2,'ការដោះស្រាយលំហាត់បញ្ហា',1),(7,13,1,'',0),(7,13,2,'',0),(7,14,1,'Topic (Science, Social Studies, Humanities, Art)',1),(7,14,2,'ប្រធានបទ (វិទ្យាសាស្ត្រ,សិក្សាសង្គម,មនុស្សសាស្ត្រ,សិល្បៈ)',1),(7,15,1,'Performing Arts',1),(7,15,2,'ស្នាដៃសិល្បៈ',1),(7,16,1,'Physical Education',1),(7,16,2,'ការសិក្សាពីរូបរាងកាយ',1),(7,17,1,'Study and Learning Skills',0),(7,17,2,'ជំនាញសិក្សានិងការរៀនសូត្រ',0),(7,18,1,'Works independently',1),(7,18,2,'ធ្វើកិច្ចការបានដោយខ្លួនឯង',1),(7,19,1,'Plays well in groups',1),(7,19,2,'កិច្ចការល្អជាក្រុម',1),(7,20,1,'Plays cooperatively with others',1),(7,20,2,'ការសហគ្នាក្នុងការលេងជាមួយអ្នកដ៏ទៃ',1),(7,21,1,'Follows directions',1),(7,21,2,'ស្តាប់ការណែនាំបង្រៀន',1),(7,22,1,'Organizes and looks after belongings',1),(7,22,2,'ការរៀបចំនិងថែរក្សារបស់​ផ្ទាល់ខ្លួន',1),(7,23,1,'Perseveres in problem solving',1),(7,23,2,'ព្យាយាមក្នុងការដោះស្រាយបញ្ហា',1),(7,24,1,'Makes good use of time',1),(7,24,2,'ប្រើប្រាស់ពេលវេលាបានល្អ',1),(7,25,1,'Seeks help when needed',1),(7,25,2,'ស្វែងរកជំនួយនៅពេលត្រូវការ',1),(7,26,1,'Completes classwork',1),(7,26,2,'បញ្ចប់កិច្ចការក្នុងថ្នាក់',1),(7,27,1,'Works neatly and carefully',1),(7,27,2,'កិច្ចការស្អាតនិងប្រុងប្រយ័ត្ន',1);
/*!40000 ALTER TABLE `template_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `template_name` varchar(256) NOT NULL,
  `columns` int(1) NOT NULL,
  `key` tinyint(1) NOT NULL,
  `height_limit` int(2) NOT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES (1,2,'Grade 2',4,1,17),(2,2,'Kindergarten 2',3,0,19),(3,2,'Kindergarten 1',3,0,19),(4,2,'Kindergarten 3',3,0,19),(5,2,'Grade 1',4,1,17),(6,2,'Grade 3',4,1,17),(7,2,'Grade 4',4,1,17);
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-09-02 16:26:56
