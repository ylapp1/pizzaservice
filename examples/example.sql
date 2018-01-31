-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: pizza_service
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

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
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zip_id` int(11) NOT NULL,
  `city_name_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_U_1` (`zip_id`,`city_name_id`),
  KEY `cities_FI_2` (`city_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city_names`
--

DROP TABLE IF EXISTS `city_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `city_names_U_1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city_names`
--

LOCK TABLES `city_names` WRITE;
/*!40000 ALTER TABLE `city_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `city_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_U_1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `street_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_addresses_U_1` (`country_id`,`city_id`,`street_id`),
  KEY `customer_addresses_FI_2` (`city_id`),
  KEY `customer_addresses_FI_3` (`street_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_addresses`
--

LOCK TABLES `customer_addresses` WRITE;
/*!40000 ALTER TABLE `customer_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_names`
--

DROP TABLE IF EXISTS `customer_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name_id` int(11) NOT NULL,
  `last_name_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_names_U_1` (`first_name_id`,`last_name_id`),
  KEY `customer_names_FI_2` (`last_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_names`
--

LOCK TABLES `customer_names` WRITE;
/*!40000 ALTER TABLE `customer_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name_id` int(11) NOT NULL,
  `customer_address_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_U_1` (`customer_name_id`,`customer_address_id`),
  KEY `customers_FI_2` (`customer_address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `first_names`
--

DROP TABLE IF EXISTS `first_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `first_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `first_names_U_1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `first_names`
--

LOCK TABLES `first_names` WRITE;
/*!40000 ALTER TABLE `first_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `first_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `house_numbers`
--

DROP TABLE IF EXISTS `house_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `house_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `house_numbers_U_1` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_numbers`
--

LOCK TABLES `house_numbers` WRITE;
/*!40000 ALTER TABLE `house_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `house_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (1),(2),(3),(4),(5),(6),(7),(8),(9),(10);
/*!40000 ALTER TABLE `ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredients_translation`
--

DROP TABLE IF EXISTS `ingredients_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingredients_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ingredient_id` int(11) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ingredients_translation_U_1` (`language_code`,`ingredient_name`),
  UNIQUE KEY `ingredients_translation_U_2` (`ingredient_id`,`language_code`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients_translation`
--

LOCK TABLES `ingredients_translation` WRITE;
/*!40000 ALTER TABLE `ingredients_translation` DISABLE KEYS */;
INSERT INTO `ingredients_translation` VALUES (1,1,'de','Teig'),(2,1,'it','Pasta'),(3,2,'de','Tomatensauce'),(4,2,'it','Salsa al pomodoro'),(5,3,'de','Salami'),(6,3,'it','Salame'),(7,4,'de','Schinken'),(8,4,'it','Prosciutto'),(9,5,'de','Käse'),(10,5,'it','Formaggio'),(11,6,'de','Thunfisch'),(12,6,'it','Tonno'),(13,7,'de','Zwiebeln'),(14,7,'it','Cipolle'),(15,8,'de','Pilze'),(16,8,'it','Funghi'),(17,9,'de','Paprika'),(18,9,'it','Peperone'),(19,10,'de','Ananas'),(20,10,'it','Ananas');
/*!40000 ALTER TABLE `ingredients_translation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `code` varchar(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `languages_U_1` (`code`),
  UNIQUE KEY `languages_U_2` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `last_names`
--

DROP TABLE IF EXISTS `last_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `last_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `last_names_U_1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `last_names`
--

LOCK TABLES `last_names` WRITE;
/*!40000 ALTER TABLE `last_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `last_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_pizzas`
--

DROP TABLE IF EXISTS `order_pizzas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_pizzas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pizza_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_pizzas_FI_1` (`order_id`),
  KEY `order_pizzas_FI_2` (`pizza_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_pizzas`
--

LOCK TABLES `order_pizzas` WRITE;
/*!40000 ALTER TABLE `order_pizzas` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_pizzas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_FI_1` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pizza_ingredients`
--

DROP TABLE IF EXISTS `pizza_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pizza_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pizza_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `grams` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pizza_ingredients_U_1` (`pizza_id`,`ingredient_id`),
  KEY `pizza_ingredients_FI_2` (`ingredient_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pizza_ingredients`
--

LOCK TABLES `pizza_ingredients` WRITE;
/*!40000 ALTER TABLE `pizza_ingredients` DISABLE KEYS */;
INSERT INTO `pizza_ingredients` VALUES (1,1,2,40),(2,1,6,100),(3,1,7,45),(4,1,5,70),(5,1,1,100),(6,2,5,50),(7,2,3,60),(8,2,1,100),(9,2,2,40),(10,3,5,50),(11,3,4,60),(12,3,1,100),(13,3,2,40),(14,4,10,70),(15,4,5,50),(16,4,4,40),(17,4,1,100),(18,4,2,40);
/*!40000 ALTER TABLE `pizza_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pizzas`
--

DROP TABLE IF EXISTS `pizzas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pizzas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `order_code` varchar(20) NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pizzas_U_1` (`name`),
  UNIQUE KEY `pizzas_U_2` (`order_code`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pizzas`
--

LOCK TABLES `pizzas` WRITE;
/*!40000 ALTER TABLE `pizzas` DISABLE KEYS */;
INSERT INTO `pizzas` VALUES (1,'Tonno','1',7),(2,'Salami','2',7),(3,'Schinken','3',7),(4,'Hawaii','4',8);
/*!40000 ALTER TABLE `pizzas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `street_names`
--

DROP TABLE IF EXISTS `street_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `street_names` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `street_names_U_1` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `street_names`
--

LOCK TABLES `street_names` WRITE;
/*!40000 ALTER TABLE `street_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `street_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streets`
--

DROP TABLE IF EXISTS `streets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street_name_id` int(11) NOT NULL,
  `house_number_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `streets_U_1` (`street_name_id`,`house_number_id`),
  KEY `streets_FI_2` (`house_number_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streets`
--

LOCK TABLES `streets` WRITE;
/*!40000 ALTER TABLE `streets` DISABLE KEYS */;
/*!40000 ALTER TABLE `streets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zips`
--

DROP TABLE IF EXISTS `zips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zips_U_1` (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zips`
--

LOCK TABLES `zips` WRITE;
/*!40000 ALTER TABLE `zips` DISABLE KEYS */;
/*!40000 ALTER TABLE `zips` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-31  8:57:30
