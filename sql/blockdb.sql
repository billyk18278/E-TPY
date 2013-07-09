CREATE DATABASE  IF NOT EXISTS `block` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `block`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: block
-- ------------------------------------------------------
-- Server version	5.1.44-community

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
-- Table structure for table `employer`
--

DROP TABLE IF EXISTS `employer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employer` (
  `E_ID` int(11) NOT NULL AUTO_INCREMENT,
  `E_NAME` varchar(255) DEFAULT NULL,
  `E_ADDR` varchar(255) DEFAULT NULL,
  `E_DOY` varchar(45) NOT NULL,
  `E_AFM` varchar(15) NOT NULL,
  `E_OCCUPATION` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`E_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employer`
--

LOCK TABLES `employer` WRITE;
/*!40000 ALTER TABLE `employer` DISABLE KEYS */;
INSERT INTO `employer` VALUES (1,'Ειδικός Λογαριασμός Κονδυλίων Έρευνας Α.Π.Θ.','3ης Σεπτεμβρίου πανεπιστημιούπολη Θεσσαλονίκη','Δ\' Θεσσαλονίκης','090049627','Ειδικός Λογαριασμός'),(2,'Ερευνητικό κέντρο Ε.Κ.Ε.Τ.Α.','6ο χμλ. οδού Χαριλάου-Θέρμης Θέρμη','Ζ\' Θεσσαλονίκης','099785242','ΝΠΙΔ');
/*!40000 ALTER TABLE `employer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `income`
--

DROP TABLE IF EXISTS `income`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `income` (
  `I_ID` int(11) NOT NULL AUTO_INCREMENT,
  `I_VAT` tinyint(4) DEFAULT '23',
  `I_VALUE` double unsigned DEFAULT NULL,
  `I_TAX` tinyint(4) DEFAULT '20',
  `I_DETAILS` varchar(255) DEFAULT NULL,
  `I_P_PIN` int(10) unsigned NOT NULL,
  `I_E_ID` int(11) NOT NULL,
  `I_DATE` datetime DEFAULT NULL,
  `I_AA` int(10) unsigned DEFAULT NULL,
  `I_TIMESTAMP` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `I_SESSIONID` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`I_ID`),
  KEY `fk_income_person_idx` (`I_P_PIN`),
  KEY `fk_income_employer1_idx` (`I_E_ID`),
  CONSTRAINT `fk_income_employer1` FOREIGN KEY (`I_E_ID`) REFERENCES `employer` (`E_ID`),
  CONSTRAINT `fk_income_person` FOREIGN KEY (`I_P_PIN`) REFERENCES `person` (`P_PIN`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `income`
--

LOCK TABLES `income` WRITE;
/*!40000 ALTER TABLE `income` DISABLE KEYS */;
/*!40000 ALTER TABLE `income` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `P_PIN` int(10) unsigned NOT NULL,
  `P_PASS` varchar(45) DEFAULT NULL,
  `P_NAME` varchar(45) DEFAULT NULL,
  `P_STAMP` mediumblob,
  PRIMARY KEY (`P_PIN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES (0,NULL,'Ανώνυμος Χρήστης',NULL),(1111,'1','Βασίλειος Κιλίντζης',NULL);
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-09 10:58:27
