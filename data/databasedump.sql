-- MySQL dump 10.11
--
-- Host: localhost    Database: coll
-- ------------------------------------------------------
-- Server version	5.0.95

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
-- Table structure for table `Answers`
--

DROP TABLE IF EXISTS `Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Answers` (
  `answerId` int(11) NOT NULL auto_increment,
  `answerNo` int(11) default NULL,
  `questionId` int(11) default NULL,
  `userId` int(11) default NULL,
  PRIMARY KEY  (`answerId`),
  KEY `questionId` (`questionId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=201 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Answers`
--

LOCK TABLES `Answers` WRITE;
/*!40000 ALTER TABLE `Answers` DISABLE KEYS */;
INSERT INTO `Answers` VALUES (25,3,1,3),(26,4,2,3),(27,4,3,3),(28,5,4,3),(29,5,5,3),(30,2,6,3),(31,3,7,3),(32,2,8,3),(48,4,8,6),(47,4,7,6),(46,4,6,6),(45,4,5,6),(44,4,4,6),(43,4,3,6),(42,4,2,6),(41,3,1,6),(49,4,1,8),(50,4,2,8),(51,4,3,8),(52,5,4,8),(53,3,5,8),(54,2,6,8),(55,3,7,8),(56,4,8,8),(104,3,8,9),(103,3,7,9),(102,3,6,9),(101,4,5,9),(100,4,4,9),(99,5,3,9),(98,4,2,9),(97,4,1,9),(65,4,1,10),(66,4,2,10),(67,3,3,10),(68,5,4,10),(69,5,5,10),(70,2,6,10),(71,4,7,10),(72,3,8,10),(73,4,1,12),(74,3,2,12),(75,3,3,12),(76,5,4,12),(77,5,5,12),(78,4,6,12),(79,5,7,12),(80,4,8,12),(81,3,1,13),(82,4,2,13),(83,3,3,13),(84,5,4,13),(85,5,5,13),(86,2,6,13),(87,2,7,13),(88,2,8,13),(89,4,1,14),(90,3,2,14),(91,4,3,14),(92,5,4,14),(93,4,5,14),(94,2,6,14),(95,1,7,14),(96,3,8,14),(105,3,1,15),(106,4,2,15),(107,4,3,15),(108,5,4,15),(109,5,5,15),(110,2,6,15),(111,3,7,15),(112,2,8,15),(113,4,8,16),(114,4,7,16),(115,4,6,16),(116,4,5,16),(117,4,4,16),(118,4,3,16),(119,4,2,16),(120,3,1,16),(121,4,1,17),(122,4,2,17),(123,4,3,17),(124,5,4,17),(125,3,5,17),(126,2,6,17),(127,3,7,17),(128,4,8,17),(129,3,8,18),(130,3,7,18),(131,3,6,18),(132,4,5,18),(133,4,4,18),(134,5,3,18),(135,4,2,18),(136,4,1,18),(137,3,1,28),(138,4,2,28),(139,4,3,28),(140,5,4,28),(141,5,5,28),(142,2,6,28),(143,3,7,28),(144,2,8,28),(145,4,8,20),(146,4,7,20),(147,4,6,20),(148,4,5,20),(149,4,4,20),(150,4,3,20),(151,4,2,20),(152,3,1,20),(153,5,1,21),(154,3,2,21),(155,3,3,21),(156,4,4,21),(157,3,5,21),(158,4,6,21),(159,3,7,21),(160,3,8,21),(161,4,8,22),(162,3,7,22),(163,4,6,22),(164,4,5,22),(165,4,4,22),(166,5,3,22),(167,4,2,22),(168,3,1,22),(169,4,1,23),(170,4,2,23),(171,4,3,23),(172,5,4,23),(173,3,5,23),(174,2,6,23),(175,3,7,23),(176,4,8,23),(177,3,8,24),(178,3,7,24),(179,3,6,24),(180,4,5,24),(181,4,4,24),(182,5,3,24),(183,4,2,24),(184,4,1,24),(185,3,1,25),(186,4,2,25),(187,4,3,25),(188,5,4,25),(189,5,5,25),(190,2,6,25),(191,3,7,25),(192,2,8,25),(193,4,8,27),(194,4,7,27),(195,4,6,27),(196,4,5,27),(197,4,4,27),(198,4,3,27),(199,4,2,27),(200,3,1,27);
/*!40000 ALTER TABLE `Answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Questions`
--

DROP TABLE IF EXISTS `Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Questions` (
  `questionId` int(11) NOT NULL auto_increment,
  `questionTxt` text,
  PRIMARY KEY  (`questionId`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Questions`
--

LOCK TABLES `Questions` WRITE;
/*!40000 ALTER TABLE `Questions` DISABLE KEYS */;
INSERT INTO `Questions` VALUES (1,'In general, I found that the quality of the results returned were superior to my normal\nsearch engine of choice.'),(2,'In general when using the meta search engine, I found that the quality of the aggregated\nresults returned were of better quality when compared to the non- aggregated results.'),(3,'In general when using the meta search engine, I found that the quality of the results\nreturned when using query expansion were of better quality when compared to the results\nreturned when query expansion is not used.'),(4,'I found the interface very easy to use. \n'),(5,'I liked how the results were presented. \n'),(6,'The speed of the meta engine is comparable to that of my typical engine of choice.'),(7,'If given the option, I would you make this my default search engine? '),(8,'I found it useful when the final results were clustered.');
/*!40000 ALTER TABLE `Questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `userId` int(11) NOT NULL auto_increment,
  `lastName` varchar(50) default NULL,
  `firstName` varchar(50) default NULL,
  `emailAddress` varchar(255) default NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (3,'Lysaght','Donogh','donogh.lysaght@donton.com'),(6,'Goulding','Conor','cogoulding@gmail.com'),(9,'ward','brian','brian@principal.ie'),(8,'Mulholland','John','johnboygbh@hotmail.com'),(10,'Moorehouse','Pat','patmoorehouse.irl@gmail.com'),(12,'Cameron','Andrew','ay.cameron@yahoo.ca'),(13,'Lamb','Andrew','andrewlamb1081@yahoo.co.uk'),(14,'Brunoni','Federica','federica.brunoni@studenti.unipr.it'),(15,'Yousuf','Bilal','yousufbl@scss.tcd.ie'),(16,'McGlinn','Kris','kmcglinn@gmail.com'),(17,'Hampson','Cormac','cormac.hampson@cs.tcd.ie'),(18,'Brown','Adam','ABrown22@slb.com'),(28,'Coll','James','james.evin.coll@gmail.com'),(20,'Ossig','Theresa','theresa.ossig@nefkom.net'),(21,'Bertsch','Rahel','rahel.bertsch@gmx.de'),(22,'Willkomm','Maleen','maryloo90@web.de'),(23,'Bassler','Ina','ina-bassler@web.de'),(24,'Segura','Nisamar','nisamar.segura@gmail.com'),(25,'Tizon','Virginia','virgi_thebest_12@icloud.com'),(27,'Ulla','Asad','asadulla.sami@yahoo.ie');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-20 12:41:00
