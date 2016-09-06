-- MySQL dump 10.13  Distrib 5.6.28, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: sjb_light_db
-- ------------------------------------------------------
-- Server version	5.6.28-0ubuntu0.15.04.1

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
-- Table structure for table `administrator`
--

DROP TABLE IF EXISTS `administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrator` (
  `sid` int(1) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrator`
--

LOCK TABLES `administrator` WRITE;
/*!40000 ALTER TABLE `administrator` DISABLE KEYS */;
INSERT INTO `administrator` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3');
/*!40000 ALTER TABLE `administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) NOT NULL,
  `jobseeker_id` int(11) NOT NULL,
  `comments` text NOT NULL,
  `date` datetime NOT NULL,
  `resume` varchar(255) NOT NULL,
  `file` text,
  `mime_type` varchar(255) NOT NULL,
  `file_id` text NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`,`jobseeker_id`),
  KEY `jobseeker_id` (`jobseeker_id`),
  KEY `date` (`date`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES (1,14,1,'Please consider my candidature for this job.','2012-11-07 15:35:28','15','test_resume_4_2_3.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application_0e277fdc866ebfdc849b68125c9defe9',NULL,NULL),(2,13,1,'Interested in this job.','2012-11-07 15:36:01','15','','','',NULL,NULL);
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` longtext,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `date` (`date`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog`
--

LOCK TABLES `blog` WRITE;
/*!40000 ALTER TABLE `blog` DISABLE KEYS */;
INSERT INTO `blog` VALUES (1,'2016-04-07','Hello World!','Test blog post',0,'','','');
/*!40000 ALTER TABLE `blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `browse`
--

DROP TABLE IF EXISTS `browse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `browse` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_uri` varchar(255) NOT NULL,
  `parameters` text,
  `data` mediumtext,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `browse`
--

LOCK TABLES `browse` WRITE;
/*!40000 ALTER TABLE `browse` DISABLE KEYS */;
INSERT INTO `browse` VALUES (7,'/categories/','a:2:{s:11:\"level1Field\";s:11:\"JobCategory\";s:15:\"listing_type_id\";s:3:\"Job\";}','a:9:{i:373;i:2;i:343;i:5;i:344;i:5;i:350;s:1:\"1\";i:360;s:1:\"1\";i:370;s:1:\"1\";i:359;s:1:\"1\";i:367;s:1:\"1\";i:346;s:1:\"1\";}'),(8,'/cities/','a:3:{s:11:\"level1Field\";s:4:\"City\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','a:5:{s:9:\"Cambridge\";i:3;s:10:\"Sacramento\";i:2;s:7:\"Durango\";i:2;s:13:\"San Francisco\";s:1:\"1\";s:7:\"Atlanta\";s:1:\"1\";}'),(9,'/category/','a:2:{s:11:\"level1Field\";s:11:\"JobCategory\";s:15:\"listing_type_id\";s:3:\"Job\";}','a:9:{i:373;i:2;i:343;i:5;i:344;i:5;i:350;s:1:\"1\";i:360;s:1:\"1\";i:370;s:1:\"1\";i:359;s:1:\"1\";i:367;s:1:\"1\";i:346;s:1:\"1\";}'),(10,'/states/','a:3:{s:11:\"level1Field\";s:5:\"State\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','a:4:{s:13:\"Massachusetts\";i:3;s:10:\"California\";i:3;s:8:\"Colorado\";i:2;s:7:\"Georgia\";s:1:\"1\";}'),(12,'/countries/','a:3:{s:11:\"level1Field\";s:7:\"Country\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','a:1:{s:13:\"United States\";i:9;}');
/*!40000 ALTER TABLE `browse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_packages`
--

DROP TABLE IF EXISTS `contract_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) DEFAULT NULL,
  `contract_id` int(10) unsigned DEFAULT NULL,
  `fields` text,
  PRIMARY KEY (`id`),
  KEY `class_name` (`class_name`),
  KEY `contract_id` (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_packages`
--

LOCK TABLES `contract_packages` WRITE;
/*!40000 ALTER TABLE `contract_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contracts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_sid` int(10) NOT NULL,
  `product_sid` int(11) NOT NULL DEFAULT '0',
  `creation_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `price` float unsigned DEFAULT NULL,
  `serialized_extra_info` text,
  `gateway_id` varchar(255) DEFAULT NULL,
  `invoice_id` varchar(255) DEFAULT NULL,
  `number_of_postings` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','pending') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `user_sid` (`user_sid`),
  KEY `expired_date` (`expired_date`),
  KEY `creation_date` (`creation_date`),
  KEY `gateway_id` (`gateway_id`),
  KEY `product_sid` (`product_sid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `cc` varchar(100) DEFAULT NULL,
  `subject` varchar(254) DEFAULT NULL,
  `text` text,
  `hidden` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`sid`),
  KEY `group` (`group`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (13,'Resume Posting Confirmation','listing','','Your resume has been posted','Hello {$listing.user.FullName},<br /><br />Your resume was successfully posted at {$siteUrl}.<br /><br />You can edit your resume in <a href=\"{$siteUrl}/my-listings/resume/\">&quot;My Account&quot;</a> section.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(14,'Job Posting Confirmation','listing','','Your job has been posted','Hello {$user.FullName},<br /><br />Your job <a href=\"{$siteUrl}{$listing|listing_url}\">{$listing.Title}</a> was successfully posted at {$siteUrl}.<br /><br />You can edit, delete or make your job hidden in <a href=\"{$siteUrl}/my-listings/job/\">&quot;My Account&quot;</a> section.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(17,'Job Expiration Notice','listing','','Your job has been expired','Hello {$listing.user.FullName},<br /><br />Your job &quot;{$listing.Title}&quot; has just been expired.<br /><br />To re-activate it, please go to <a href=\"{$siteUrl}/my-listings/job/\">Job Postings</a> section in &quot;My Account&quot;.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(18,'Resume Expiration Notice','listing','','Your resume has been expired','Hello {$listing.user.FullName},<br /><br />Your resume has just been expired.<br /><br />To re-activate it, please go to My Resumes section in <a href=\"{$siteUrl}/my-listings/resume/\">&quot;My Account&quot;</a>.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(22,'Product Expiration Notice','product','','Product Expiration Notice','Hello {$user.FullName},<br /><br />Your subscription to &quot;{$product.caption}&quot; has just expired.<br /><br />To purchase another available product, please visit our <a href=\"{$siteUrl}{if $user.group.id == \'JobSeeker\'}/jobseeker-products/{elseif $user.group.id == \'Employer\'}/employer-products/{else}/products/{/if}\">Pricing page</a>.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(24,'Product Purchase Confirmation','product','','Payment received','Hello {$user.FullName},<br /><br />You have successfully purchased &quot;{$product.caption}&quot; on {$GLOBALS.settings.site_title}.<br /><br />Payment details:<br /><br />&quot;{$product.caption}&quot; x 1 = {currencyFormat amount=$product.price}<br />-----<br />Sub total: {currencyFormat amount=$invoice.sub_total}<br />{if $invoice.tax}Taxes: {currencyFormat amount=$invoice.tax}<br />{/if}Grand total: {currencyFormat amount=$invoice.total}<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(25,'Product Purchase Confirmation for Admin','product','','New payment received','Hello,<br /><br />On {$invoice.date|date} the user {$user.FullName} has successfully purchased &quot;{$product.caption}&quot; on {$GLOBALS.settings.site_title}.<br /><br />Payment details:<br /><br />&quot;{$product.caption}&quot; x 1 = {currencyFormat amount=$product.price}<br />-----<br />Sub total: {currencyFormat amount=$invoice.sub_total}<br />{if $invoice.tax}Taxes: {currencyFormat amount=$invoice.tax}<br />{/if}Grand total: {currencyFormat amount=$invoice.total}<br /><br />To view payment details, please click <a href=\"{$GLOBALS.admin_site_url}/view-invoice/?sid={$invoice.id}\">here</a>.\r\n',0),(30,'Contact Form Email','other','','Message from {$name}','This message was sent via contact form on {$siteUrl}\r\n<br/><br/>\r\n|| name: {$name}<br/>\r\n|| email: {$email}<br/>\r\n|| comments: {$comments}',1),(33,'Application Email to Employer','other','','Application for {$listing.Title}','Hello {$user.FullName},<br /><br />You received a new application to your job posting &quot;{$listing.Title}&quot; from the following applicant:<br /><br />Name: {$applicant_request.name}<br />Email: {$applicant_request.email}<br />Cover Letter:<br />{$applicant_request.comments}<br /><br /><a href=\"{$siteUrl}/system/applications/view/\">View all applications</a><br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(36,'Password Recovery','other','','Password recovery for {$GLOBALS.settings.site_title}','Hello {$user.FullName},<br /><br />Someone has requested to change the password for your account at {$siteUrl}. If it was not you, please ignore this message.<br /><br />Otherwise, to change your password you can by following the link below:<br /><br /><a href=\"{$siteUrl}/change-password/?username={$user.username}&amp;verification_key={$user.verification_key}\">Change your password</a><br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(48,'Employer Welcome Email','user','','Welcome to {$GLOBALS.settings.site_title}!','Hello {$user.FullName},<br /><br />Thank you for registering with {$siteUrl}.<br /><br />You will be pleasantly surprised when you see how easy it is to post jobs, search for resumes and track applications with us.<br /><br />We are happy you&#39;ve chosen to be a part of our community and we hope you enjoy your stay.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(49,'Job Seeker Welcome Email','user','','Welcome to {$GLOBALS.settings.site_title}!','Hello {$user.FullName},<br /><br />Thank you for registering with {$siteUrl}.<br /><br />You will be pleasantly surprised when you see how easy it is create a resume, search for jobs and create job alerts with us.<br /><br />We are happy you&#39;ve chosen to be a part of our community and we hope you enjoy your stay.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0),(203,'Job Alert','alerts','','New jobs at {$GLOBALS.settings.site_title}','Hi there,<br /><br />Here are the latest jobs that match your job alert request:<p>{foreach from=$listings item=listing}</p>\r\n<p><strong><a href=\"{$siteUrl}{$listing|listing_url}\">{$listing.Title}</a></strong><br />{$listing|location}<br />{$listing.user.CompanyName}</p>\r\n<p>{/foreach}</p>If you do not want to receive any email alerts from now on please unsubscribe using the link below:<br /><a href=\"{$siteUrl}/guest-alerts/unsubscribe/?key={$key}\">Unsubscribe</a>.<br /><br />Thanks,<br />The {$GLOBALS.settings.site_title} team',0);
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facebook`
--

DROP TABLE IF EXISTS `facebook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facebook` (
  `facebook_id` varchar(255) NOT NULL,
  `access` text NOT NULL,
  `profile_info` text NOT NULL,
  UNIQUE KEY `linkedin_id` (`facebook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facebook`
--

LOCK TABLES `facebook` WRITE;
/*!40000 ALTER TABLE `facebook` DISABLE KEYS */;
INSERT INTO `facebook` VALUES ('100002383902681','s:185:\"CAAAAGfu7Vf8BAHFWRpctf7PJD2dsZBySlj4nuiUNt7hBclmIpkK6jqRuselvZBBWQJ6kxZAp139QVtwZANzHZCYbtIh1bpIiT5yl3dJ8EdgdLQq9XwoI5fAEqEq0uzF9TYl5VIzAorZC7vOBRUE6t1Yyt3t5ZBZCpcnSIhFyErg0GcdyzTXtplN3\";','C:11:\"ArrayObject\":298:{x:i:2;a:7:{s:2:\"id\";s:15:\"100002383902681\";s:4:\"link\";s:55:\"https://www.facebook.com/profile.php?id=100002383902681\";s:8:\"birthday\";s:10:\"05/22/1983\";s:5:\"email\";s:22:\"test@smartjobboard.com\";s:8:\"timezone\";i:6;s:6:\"locale\";s:5:\"en_US\";s:12:\"updated_time\";s:24:\"2013-05-13T06:55:54+0000\";};m:a:0:{}}');
/*!40000 ALTER TABLE `facebook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google`
--

DROP TABLE IF EXISTS `google`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google` (
  `google_id` varchar(255) NOT NULL,
  `profile_info` text NOT NULL,
  UNIQUE KEY `linkedin_id` (`google_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google`
--

LOCK TABLES `google` WRITE;
/*!40000 ALTER TABLE `google` DISABLE KEYS */;
/*!40000 ALTER TABLE `google` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_alerts`
--

DROP TABLE IF EXISTS `guest_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_alerts` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `data` text,
  `last_send` datetime DEFAULT NULL,
  `email_frequency` enum('daily','weekly','monthly') NOT NULL DEFAULT 'daily',
  `subscription_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `alert_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `last_send` (`last_send`),
  KEY `email_frequency` (`email_frequency`),
  KEY `email` (`email`),
  KEY `alert_key` (`alert_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_alerts`
--

LOCK TABLES `guest_alerts` WRITE;
/*!40000 ALTER TABLE `guest_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `guest_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_sid` int(10) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `sub_total` double DEFAULT '0',
  `total` double DEFAULT '0',
  `serialized_items_info` text,
  `serialized_tax_info` text,
  `status` varchar(255) DEFAULT NULL,
  `include_tax` tinyint(4) DEFAULT NULL,
  `callback_data` text,
  `product_sid` varchar(255) DEFAULT NULL,
  `status_paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `linkedin`
--

DROP TABLE IF EXISTS `linkedin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linkedin` (
  `linkedin_id` varchar(255) NOT NULL,
  `access` text NOT NULL,
  `profile_info` text NOT NULL,
  UNIQUE KEY `linkedin_id` (`linkedin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linkedin`
--

LOCK TABLES `linkedin` WRITE;
/*!40000 ALTER TABLE `linkedin` DISABLE KEYS */;
INSERT INTO `linkedin` VALUES ('DeUJ4US97K','O:23:\"Zend_Oauth_Token_Access\":1:{s:10:\"\0*\0_params\";a:4:{s:11:\"oauth_token\";s:36:\"6887d20a-3421-4273-be9f-8bad061ab685\";s:18:\"oauth_token_secret\";s:36:\"9e9bb627-7d14-403b-9962-ff0295c35794\";s:16:\"oauth_expires_in\";s:1:\"0\";s:30:\"oauth_authorization_expires_in\";s:1:\"0\";}}','s:1323:\"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<person>\n  <id>DeUJ4US97K</id>\n  <first-name>Ben</first-name>\n  <main-address>My Address</main-address>\n  <last-name>Akylbekov</last-name>\n  <headline>Product Manager at SJB</headline>\n  <date-of-birth>\n    <year>1983</year>\n    <month>5</month>\n    <day>22</day>\n  </date-of-birth>\n  <industry>Telecommunications</industry>\n  <positions total=\"1\">\n    <position>\n      <id>284918504</id>\n      <title>Product Manager</title>\n      <summary>Product management</summary>\n      <start-date>\n        <year>2010</year>\n        <month>7</month>\n      </start-date>\n      <is-current>true</is-current>\n      <company>\n        <id>222774</id>\n        <name>SmartJobBoard</name>\n        <size>11-50 employees</size>\n        <type>Privately Held</type>\n        <industry>Internet</industry>\n      </company>\n    </position>\n  </positions>\n  <educations total=\"0\"/>\n  <phone-numbers total=\"1\">\n    <phone-number>\n      <phone-type>home</phone-type>\n      <phone-number>0552033677</phone-number>\n    </phone-number>\n  </phone-numbers>\n  <twitter-accounts total=\"0\"/>\n  <public-profile-url>http://www.linkedin.com/pub/ben-akylbekov/52/31/513</public-profile-url>\n  <location>\n    <name>Kyrgyzstan</name>\n    <country>\n      <code>kg</code>\n    </country>\n  </location>\n</person>\n\";');
/*!40000 ALTER TABLE `linkedin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_complex_fields`
--

DROP TABLE IF EXISTS `listing_complex_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listing_complex_fields` (
  `sid` int(10) unsigned NOT NULL,
  `field_sid` int(10) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `order` int(10) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `maximum` float DEFAULT NULL,
  `minimum` float DEFAULT NULL,
  `maxlength` varchar(255) DEFAULT NULL,
  `signs_num` float DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `instructions` text,
  `choiceLimit` int(11) DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `field_sid` (`field_sid`),
  KEY `id` (`id`),
  KEY `order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_complex_fields`
--

LOCK TABLES `listing_complex_fields` WRITE;
/*!40000 ALTER TABLE `listing_complex_fields` DISABLE KEYS */;
INSERT INTO `listing_complex_fields` VALUES (1275915876,274,'WE_From',11,'From','date',0,NULL,NULL,NULL,NULL,NULL,'',0),(1275915985,274,'WE_To',14,'To','date',0,NULL,NULL,NULL,NULL,NULL,'',0),(1275916042,274,'WE_JobTitle',6,'Position','string',0,NULL,NULL,'256',NULL,NULL,'',0),(1275916199,273,'ED_To',8,'To','date',0,NULL,NULL,NULL,NULL,NULL,'',0),(1275916289,273,'ED_DegreeSpecialty',4,'Degree or Specialty','string',0,NULL,NULL,'256',NULL,NULL,'',0),(1276265223,273,'ED_From',6,'From','date',0,NULL,NULL,NULL,NULL,NULL,'',0),(1282192099,273,'ED_UniversityInstitution',5,'University or Institution','string',0,NULL,NULL,'256',NULL,NULL,'',0),(1282192477,274,'WE_Description',15,'Description','text',0,NULL,NULL,'',NULL,'text.tpl','',0),(1453279463,274,'WE_Company',8,'Company','string',0,NULL,NULL,NULL,NULL,NULL,'',0);
/*!40000 ALTER TABLE `listing_complex_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_feeds`
--

DROP TABLE IF EXISTS `listing_feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listing_feeds` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `template` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `mime_type` varchar(255) NOT NULL DEFAULT 'application/rss+xml',
  `id` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_feeds`
--

LOCK TABLES `listing_feeds` WRITE;
/*!40000 ALTER TABLE `listing_feeds` DISABLE KEYS */;
INSERT INTO `listing_feeds` VALUES (1,'SimplyHired','feed_simplyhired.tpl','Using this feed you can submit your jobs to SimplyHired.com.<br/> Use this link <a href=\"http://www.simplyhired.com/a/add-jobs/feed\" target=\"_blank\">http://www.simplyhired.com/a/add-jobs/feed</a> for instruction on how to get started.','text/xml; charset=utf-8','simplyhired',3),(2,'Indeed','feed_indeed.tpl','Using this feed you can submit your jobs to Indeed.com.<br/> Use this link <a target=\"_blank\" href=\"http://www.indeed.com/hire?indpubnum=6053709130975284\">http://www.indeed.com/hire?indpubnum=6053709130975284</a> for instruction on how to get started.','text/xml; charset=utf-8','indeed',2),(3,'Latest Jobs (RSS)','feed_rss.tpl','','application/rss+xml; charset=utf-8','rss',1),(4,'Trovit','feed_trovit.tpl','Using this feed you can submit your jobs to job.trovit.com.<br/> Use this link <a href=\"http://about.trovit.com/your-ads-on-trovit/us/\" target=\"_blank\">http://about.trovit.com/your-ads-on-trovit/us/</a> for instruction on how to get started.','text/xml; charset=utf-8','trovit',4);
/*!40000 ALTER TABLE `listing_feeds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_field_list`
--

DROP TABLE IF EXISTS `listing_field_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listing_field_list` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_sid` int(10) unsigned DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `field_sid` (`field_sid`),
  KEY `order` (`order`),
  KEY `value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=673 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_field_list`
--

LOCK TABLES `listing_field_list` WRITE;
/*!40000 ALTER TABLE `listing_field_list` DISABLE KEYS */;
INSERT INTO `listing_field_list` VALUES (76,199,1,'Full time'),(77,199,2,'Part time'),(78,199,3,'Contractor'),(79,199,4,'Intern'),(80,199,5,'Seasonal'),(87,213,1,'full time'),(88,213,2,'part time'),(89,213,3,'contractor'),(90,213,4,'intern'),(91,213,5,'seasonal'),(343,198,1,'Accounting'),(344,198,2,'Admin-Clerical'),(345,198,3,'Automotive'),(346,198,4,'Banking'),(347,198,5,'Biotech'),(348,198,6,'Business Development'),(349,198,7,'Construction'),(350,198,8,'Consultant'),(351,198,9,'Customer Service'),(352,198,10,'Design'),(353,198,11,'Distribution-Shipping'),(354,198,12,'Education'),(355,198,13,'Engineering'),(356,198,14,'Entry Level'),(357,198,15,'Executive'),(358,198,16,'Facilities'),(359,198,17,'Finance'),(360,198,18,'Franchise'),(361,198,19,'General Business'),(362,198,20,'General Labor'),(363,198,21,'Government'),(364,198,22,'Grocery'),(365,198,23,'Health Care'),(366,198,24,'Hospitality-Hotel'),(367,198,25,'Human Resources'),(368,198,26,'Information Technology'),(369,198,27,'Installation-Maint-Repair'),(370,198,28,'Insurance'),(371,198,29,'Inventory'),(372,198,30,'Legal'),(373,198,31,'Management'),(374,198,32,'Manufacturing'),(375,198,33,'Marketing'),(376,198,34,'Media-Journalism'),(377,198,35,'Nonprofit-Social Services'),(378,198,36,'Nurse'),(379,198,37,'Other'),(380,198,38,'Pharmaceutical'),(381,198,39,'Professional Services'),(382,198,40,'Purchasing-Procurement'),(383,198,41,'QA-Quality Control'),(384,198,42,'Real Estate'),(385,198,43,'Research'),(386,198,44,'Restaurant-Food Service'),(387,198,45,'Retail'),(388,198,46,'Sales'),(389,198,47,'Science'),(390,198,48,'Skilled Labor'),(391,198,49,'Strategy-Planning'),(392,198,50,'Supply Chain'),(393,198,51,'Telecommunications'),(394,198,52,'Training'),(395,198,53,'Transportation'),(396,198,54,'Veterinary Services'),(397,198,55,'Warehouse'),(398,215,1,'Alabama'),(399,215,2,'Alaska'),(400,215,3,'Alberta'),(401,215,4,'Arizona'),(402,215,5,'Arkansas'),(403,215,6,'British Columbia'),(404,215,7,'California'),(405,215,8,'Colorado'),(406,215,9,'Connecticut'),(407,215,10,'Delaware'),(408,215,11,'District of Columbia'),(409,215,12,'Florida'),(410,215,13,'Georgia'),(411,215,14,'Guam'),(412,215,15,'Hawaii'),(413,215,16,'Idaho'),(414,215,17,'Illinois'),(415,215,18,'Indiana'),(416,215,19,'Iowa'),(417,215,20,'Kansas'),(418,215,21,'Kentucky'),(419,215,22,'Louisiana'),(420,215,23,'Maine'),(421,215,24,'Manitoba'),(422,215,25,'Maryland'),(423,215,26,'Massachusetts'),(424,215,27,'Michigan'),(425,215,28,'Minnesota'),(426,215,29,'Mississippi'),(427,215,30,'Missouri'),(428,215,31,'Montana'),(429,215,32,'Nebraska'),(430,215,33,'Nevada'),(431,215,34,'New Hampshire'),(432,215,35,'New Jersey'),(433,215,36,'New Mexico'),(434,215,37,'New York'),(435,215,38,'North Carolina'),(436,215,39,'North Dakota'),(437,215,40,'Ohio'),(438,215,41,'Oklahoma'),(439,215,42,'Ontario'),(440,215,43,'Oregon'),(441,215,44,'Pennsylvania'),(442,215,45,'Puerto Rico'),(443,215,46,'Rhode Island'),(444,215,47,'South Carolina'),(445,215,48,'South Dakota'),(446,215,49,'Tennessee'),(447,215,50,'Texas'),(448,215,51,'Utah'),(449,215,52,'Vermont'),(450,215,53,'Virgin Islands'),(451,215,54,'Virginia'),(452,215,55,'Washington'),(453,215,56,'West Virginia'),(454,215,57,'Wisconsin'),(455,215,58,'Wyoming'),(456,215,59,'Outside US'),(545,1275915876,1,'1980'),(546,1275915876,2,'1981'),(547,1275915876,3,'1982'),(548,1275915876,4,'1983'),(549,1275915876,5,'1984'),(550,1275915876,6,'1985'),(551,1275915876,7,'1986'),(552,1275915876,8,'1987'),(553,1275915876,9,'1988'),(554,1275915876,10,'1989'),(555,1275915876,11,'1990'),(556,1275915876,12,'1991'),(557,1275915876,13,'1992'),(558,1275915876,14,'1993'),(559,1275915876,15,'1994'),(560,1275915876,16,'1995'),(561,1275915876,17,'1996'),(562,1275915876,18,'1997'),(563,1275915876,19,'1998'),(564,1275915876,20,'1999'),(565,1275915876,21,'2000'),(566,1275915876,22,'2001'),(567,1275915876,23,'2002'),(568,1275915876,24,'2003'),(569,1275915876,25,'2004'),(570,1275915876,26,'2005'),(571,1275915876,27,'2006'),(572,1275915876,28,'2007'),(573,1275915876,29,'2008'),(574,1275915876,30,'2009'),(575,1275915876,31,'2010'),(576,1275915985,1,'1980'),(577,1275915985,2,'1981'),(578,1275915985,3,'1982'),(579,1275915985,4,'1983'),(580,1275915985,5,'1984'),(581,1275915985,6,'1985'),(582,1275915985,7,'1986'),(583,1275915985,8,'1987'),(584,1275915985,9,'1988'),(585,1275915985,10,'1989'),(586,1275915985,11,'1990'),(587,1275915985,12,'1991'),(588,1275915985,13,'1992'),(589,1275915985,14,'1993'),(590,1275915985,15,'1994'),(591,1275915985,16,'1995'),(592,1275915985,17,'1996'),(593,1275915985,18,'1997'),(594,1275915985,19,'1998'),(595,1275915985,20,'1999'),(596,1275915985,21,'2000'),(597,1275915985,22,'2001'),(598,1275915985,23,'2002'),(599,1275915985,24,'2003'),(600,1275915985,25,'2004'),(601,1275915985,26,'2005'),(602,1275915985,27,'2006'),(603,1275915985,28,'2007'),(604,1275915985,29,'2008'),(605,1275915985,30,'2009'),(606,1275915985,31,'2010'),(607,1275916199,1,'1980'),(608,1275916199,2,'1981'),(609,1275916199,3,'1982'),(610,1275916199,4,'1983'),(611,1275916199,5,'1984'),(612,1275916199,6,'1985'),(613,1275916199,7,'1986'),(614,1275916199,8,'1987'),(615,1275916199,9,'1988'),(616,1275916199,10,'1989'),(617,1275916199,11,'1990'),(618,1275916199,12,'1991'),(619,1275916199,13,'1992'),(620,1275916199,14,'1993'),(621,1275916199,15,'1994'),(622,1275916199,16,'1995'),(623,1275916199,17,'1996'),(624,1275916199,18,'1997'),(625,1275916199,19,'1998'),(626,1275916199,20,'1999'),(627,1275916199,21,'2000'),(628,1275916199,22,'2001'),(629,1275916199,23,'2002'),(630,1275916199,24,'2003'),(631,1275916199,25,'2004'),(632,1275916199,26,'2005'),(633,1275916199,27,'2006'),(634,1275916199,28,'2007'),(635,1275916199,29,'2008'),(636,1275916199,30,'2009'),(637,1275916199,31,'2010'),(667,330,1,'test'),(668,330,2,'test1');
/*!40000 ALTER TABLE `listing_field_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_fields`
--

DROP TABLE IF EXISTS `listing_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listing_fields` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(255) DEFAULT NULL,
  `listing_type_sid` int(10) unsigned NOT NULL DEFAULT '0',
  `order` int(10) unsigned DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `instructions` text,
  `maxlength` varchar(255) DEFAULT '',
  `width` int(5) DEFAULT '0',
  `height` int(5) DEFAULT '0',
  `second_width` int(5) DEFAULT '0',
  `second_height` int(5) DEFAULT '0',
  `sort_by_alphabet` tinyint(1) NOT NULL DEFAULT '0',
  `template` varchar(255) DEFAULT NULL,
  `minimum` float DEFAULT '0',
  `maximum` float DEFAULT '0',
  `signs_num` int(10) DEFAULT '0',
  `display_as_select_boxes` tinyint(1) NOT NULL DEFAULT '0',
  `level_1` varchar(255) DEFAULT NULL,
  `level_2` varchar(255) DEFAULT NULL,
  `level_3` varchar(255) DEFAULT NULL,
  `level_4` varchar(255) DEFAULT NULL,
  `choiceLimit` int(11) DEFAULT '0',
  `add_parameter` varchar(20) DEFAULT NULL,
  `parent_sid` int(10) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `display_as` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `listing_type_sid` (`listing_type_sid`),
  KEY `order` (`order`),
  KEY `ufi` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=368 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_fields`
--

LOCK TABLES `listing_fields` WRITE;
/*!40000 ALTER TABLE `listing_fields` DISABLE KEYS */;
INSERT INTO `listing_fields` VALUES (197,'Title',6,1,'Job Title','string','',1,'','256',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(198,'JobCategory',0,2,'Categories','multilist','',1,'',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,5,NULL,NULL,0,'multilist'),(199,'EmploymentType',0,2,'Job Type','list','',1,'',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,5,NULL,NULL,0,'multilist'),(203,'JobDescription',6,7,'Job Description','text',NULL,0,'','99999',NULL,NULL,NULL,NULL,0,'text.tpl',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(214,'Country',0,4,'Country','string','',0,'',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,359,0,'country_name'),(215,'State',0,5,'State','string','',0,'',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,359,0,'state_name'),(217,'ZipCode',0,16,'Zip Code','string','',0,'Please enter your Zip Code.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,359,0,NULL),(218,'Resume',7,14,'Upload Resume','file',NULL,0,'',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(224,'Skills',7,7,'Personal Summary','text',NULL,0,'','',NULL,NULL,NULL,NULL,0,'text.tpl',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(234,'ApplicationSettings',6,10,'How to Apply','string',NULL,0,NULL,'256',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(273,'Education',7,12,'Education','complex',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(274,'WorkExperience',7,13,'Work Experience','complex',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(319,'access_type',7,16,'Let Employers Find My Resume','list','everyone',0,'',NULL,NULL,NULL,NULL,NULL,0,'let_employers_find_my_resume.tpl',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(344,'City',0,14,'City','string','',0,'','0',0,0,0,0,0,NULL,0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,359,0,NULL),(359,'Location',0,17,'Location','location',NULL,0,'','',0,0,0,0,0,NULL,0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(360,'expiration_date',6,13,'Expiration Date','date',NULL,0,'','',0,0,0,0,0,NULL,0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(362,'Photo',7,18,'Photo','picture',NULL,0,'','',250,250,0,0,0,NULL,0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(363,'Phone',7,19,'Phone','string',NULL,0,NULL,'',0,0,0,0,0,NULL,0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(364,'GooglePlace',0,17,'Location','google_place',NULL,0,NULL,'',0,0,0,0,0,'',0,0,0,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL),(365,'Latitude',0,17,'Latitude','string','',0,'',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,359,0,NULL),(366,'Longitude',0,18,'Longitude','string','',0,'',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,359,0,NULL),(367,'Title',7,1,'Desired Job Title','string','',1,'','256',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `listing_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listing_types`
--

DROP TABLE IF EXISTS `listing_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listing_types` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `show_brief_or_detailed` tinyint(1) NOT NULL DEFAULT '0',
  `waitApprove` tinyint(1) NOT NULL DEFAULT '0',
  `email_alert` int(5) DEFAULT NULL,
  `guest_alert_email` int(5) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `ufi` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing_types`
--

LOCK TABLES `listing_types` WRITE;
/*!40000 ALTER TABLE `listing_types` DISABLE KEYS */;
INSERT INTO `listing_types` VALUES (6,'Job','Job',1,0,28,203),(7,'Resume','Resume',1,0,29,202);
/*!40000 ALTER TABLE `listing_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings`
--

DROP TABLE IF EXISTS `listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listings` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listing_type_sid` int(10) unsigned NOT NULL DEFAULT '0',
  `user_sid` int(10) unsigned DEFAULT NULL,
  `product_info` text,
  `active` tinyint(4) unsigned DEFAULT '0',
  `keywords` longtext,
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `featured_last_showed` datetime DEFAULT NULL,
  `access_type` enum('everyone','no_one') NOT NULL DEFAULT 'everyone',
  `access_list` text,
  `contract_id` int(10) NOT NULL DEFAULT '0',
  `data_source` int(10) unsigned DEFAULT NULL,
  `external_id` varchar(1000) DEFAULT NULL,
  `email_frequency` enum('daily','weekly','monthly') NOT NULL DEFAULT 'daily',
  `Title` varchar(255) DEFAULT NULL,
  `JobCategory` text,
  `Location_Country` text,
  `Location_State` text,
  `Location_ZipCode` varchar(255) DEFAULT NULL,
  `EmploymentType` text,
  `JobDescription` longtext,
  `Skills` longtext,
  `Resume` varchar(255) DEFAULT NULL,
  `Location_City` varchar(255) DEFAULT NULL,
  `preview` tinyint(1) DEFAULT '0',
  `featured_expiration` datetime DEFAULT NULL,
  `Location` varchar(500) DEFAULT NULL,
  `complex` longtext,
  `checkouted` tinyint(1) NOT NULL DEFAULT '1',
  `Photo` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `GooglePlace` varchar(255) DEFAULT NULL,
  `Location_Latitude` double DEFAULT NULL,
  `Location_Longitude` double DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `listing_type_sid` (`listing_type_sid`),
  KEY `user_sid` (`user_sid`),
  KEY `active` (`active`),
  KEY `featured` (`featured`),
  KEY `expiration_date` (`expiration_date`),
  KEY `featured_last_showed` (`featured_last_showed`),
  KEY `access_type` (`access_type`),
  KEY `activation_date` (`activation_date`),
  KEY `contract_id` (`contract_id`),
  KEY `views` (`views`),
  KEY `Title` (`Title`),
  KEY `Resume` (`Resume`),
  KEY `ZipCode` (`Location_ZipCode`),
  KEY `City` (`Location_City`),
  KEY `preview` (`preview`),
  KEY `dash_act_lt` (`listing_type_sid`,`activation_date`),
  KEY `dash_act_act_lt` (`active`,`listing_type_sid`,`activation_date`),
  KEY `id_Photo` (`Photo`),
  KEY `id_Phone` (`Phone`),
  KEY `id_Location` (`GooglePlace`),
  KEY `Location_Latitude` (`Location_Latitude`),
  KEY `Location_Longitude` (`Location_Longitude`),
  FULLTEXT KEY `keywords` (`keywords`),
  FULLTEXT KEY `Location` (`Location`),
  FULLTEXT KEY `GooglePlaceF` (`GooglePlace`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings`
--

LOCK TABLES `listings` WRITE;
/*!40000 ALTER TABLE `listings` DISABLE KEYS */;
INSERT INTO `listings` VALUES (13,6,8,'a:13:{s:5:\"price\";s:1:\"0\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"15\";s:18:\"number_of_listings\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:8:\"featured\";s:1:\"0\";s:13:\"renewal_price\";s:2:\"20\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:0:\"\";s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"100\";s:33:\"upgrade_to_priority_listing_price\";s:2:\"50\";s:11:\"product_sid\";s:1:\"7\";}',1,'Insurance agent [Job Sample] Part time Consultant Franchise Insurance <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Insurance agent will sell insurance policies to individuals and companies by understanding their needs, requirements and financial conditions. A candidate need to have very good communication and negotiation skills and must be hardworking and meet deadlines and targets.</p> <div>&nbsp;</div> 2013-03-18 United States California Sacramento 94205 38.47999954223633 -121.44000244140625 Sacramento, CA 94205, United States',0,5,'2013-01-24 09:13:17','2023-03-18 00:00:00',NULL,'everyone','',2,NULL,NULL,'daily','Insurance agent [Job Sample]','350,360,370','United States','California','94205','77','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Insurance agent will sell insurance policies to individuals and companies by understanding their needs, requirements and financial conditions. A candidate need to have very good communication and negotiation skills and must be hardworking and meet deadlines and targets.</p>\r\n<div>&nbsp;</div>',NULL,NULL,'Sacramento',0,NULL,'United States California Sacramento 94205 38.47999954223633 -121.44000244140625',NULL,1,NULL,NULL,'Sacramento, CA 94205, United States',38.47999954223633,-121.44000244140625),(14,6,8,'a:13:{s:5:\"price\";s:1:\"0\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"15\";s:18:\"number_of_listings\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:8:\"featured\";s:1:\"0\";s:13:\"renewal_price\";s:2:\"20\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:0:\"\";s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"100\";s:33:\"upgrade_to_priority_listing_price\";s:2:\"50\";s:11:\"product_sid\";s:1:\"7\";}',1,'Featured Credit Analyst [Job Sample] Full time Accounting Admin-Clerical Banking <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Credit Analyst should analyze financial information and assess the risks of credit offer to individuals and businesses.</p> 2013-03-18 United States California Sacramento 38.58157189999999 -121.49439960000001 Sacramento, CA, United States',1,3,'2013-01-24 09:13:17','2023-03-18 00:00:00','2016-03-18 17:13:37','everyone','',2,NULL,NULL,'daily','Credit Analyst [Job Sample]','343,344,346','United States','California','','76','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Credit Analyst should analyze financial information and assess the risks of credit offer to individuals and businesses.</p>',NULL,NULL,'Sacramento',0,NULL,'United States California Sacramento 38.58157189999999 -121.49439960000001',NULL,1,NULL,NULL,'Sacramento, CA, United States',38.58157189999999,-121.49439960000001),(15,7,1,'a:13:{s:5:\"price\";s:1:\"0\";s:16:\"listing_type_sid\";s:1:\"7\";s:17:\"expiration_period\";s:1:\"0\";s:18:\"number_of_listings\";s:0:\"\";s:16:\"listing_duration\";s:4:\"7300\";s:8:\"featured\";s:1:\"0\";s:13:\"renewal_price\";s:0:\"\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:2:\"10\";s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:0:\"\";s:33:\"upgrade_to_priority_listing_price\";s:2:\"30\";s:11:\"product_sid\";s:1:\"8\";}',1,'Featured Frontend developer Full time Engineering <strong>This is not a real Resume. Please, don&#39;t try to hire me. </strong><br /><br />Senior FrontEnd Developer (AngularJS/2, Ionic/Cordova, JavaScript, Node.js, TypeScript, ) Masters in Software Engineering Bachelor in Software Engineering Stanford University Massachusetts Institute of Technology 2011-01-01 2006-01-01 2014-01-01 2010-01-01 Senior Frontend Developer Junior Frontend Developer Yahoo Inc. Blink Inc. 2015-01-01 2014-01-01 1969-12-31 2015-01-01 AngularJS/2, Ionic/Cordova, JavaScript, Node.js, TypeScript Frontend:&nbsp;<br />AngularJS, Javascript, JQuery, Typescript,&nbsp; Everyone New York, NY, United States John Smith [Resume Sample] United States New York New York 40.7127837 -74.00594130000002',1,4,'2013-01-24 09:13:17','2023-03-18 00:00:00',NULL,'everyone','',3,NULL,NULL,'daily','Frontend developer','355','United States','New York','','76',NULL,'<strong>This is not a real Resume. Please, don&#39;t try to hire me. </strong><br /><br />Senior FrontEnd Developer (AngularJS/2, Ionic/Cordova, JavaScript, Node.js, TypeScript, )','Resume_15','New York',0,NULL,'United States New_ York New_ York 40.7127837 -74.00594130000002','a:2:{s:9:\"Education\";a:4:{s:18:\"ED_DegreeSpecialty\";a:2:{i:1;s:31:\"Masters in Software Engineering\";i:2;s:32:\"Bachelor in Software Engineering\";}s:24:\"ED_UniversityInstitution\";a:2:{i:1;s:19:\"Stanford University\";i:2;s:37:\"Massachusetts Institute of Technology\";}s:7:\"ED_From\";a:2:{i:1;s:11:\"2011-01-01 \";i:2;s:11:\"2006-01-01 \";}s:5:\"ED_To\";a:2:{i:1;s:11:\"2014-01-01 \";i:2;s:11:\"2010-01-01 \";}}s:14:\"WorkExperience\";a:5:{s:11:\"WE_JobTitle\";a:2:{i:1;s:25:\"Senior Frontend Developer\";i:2;s:25:\"Junior Frontend Developer\";}s:10:\"WE_Company\";a:2:{i:1;s:10:\"Yahoo Inc.\";i:2;s:10:\"Blink Inc.\";}s:7:\"WE_From\";a:2:{i:1;s:11:\"2015-01-01 \";i:2;s:11:\"2014-01-01 \";}s:5:\"WE_To\";a:2:{i:1;N;i:2;s:11:\"2015-01-01 \";}s:14:\"WE_Description\";a:2:{i:1;s:59:\"AngularJS/2, Ionic/Cordova, JavaScript, Node.js, TypeScript\";i:2;s:69:\"Frontend:&nbsp;<br />AngularJS, Javascript, JQuery, Typescript,&nbsp;\";}}}',1,'Photo_15','','New York, NY, United States',40.7127837,-74.00594130000002),(19,6,6,'a:15:{s:12:\"pricing_type\";s:5:\"fixed\";s:5:\"price\";s:3:\"500\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:1:\"5\";s:13:\"renewal_price\";s:3:\"100\";s:8:\"featured\";s:1:\"1\";s:8:\"priority\";s:1:\"1\";s:18:\"number_of_pictures\";s:1:\"5\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:1:{i:1;s:0:\"\";}s:17:\"listings_range_to\";a:1:{i:1;s:0:\"\";}s:14:\"price_per_unit\";a:1:{i:1;s:0:\"\";}s:25:\"renewal_price_per_listing\";a:1:{i:1;s:0:\"\";}}s:5:\"video\";s:1:\"1\";s:33:\"upgrade_to_featured_listing_price\";s:0:\"\";s:33:\"upgrade_to_priority_listing_price\";s:0:\"\";s:11:\"product_sid\";s:1:\"2\";}',1,'Featured HR Specialist [Job Sample] Full time Human Resources <strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We are looking for an HR/ Office Manager. This is an opportunity to be a key part of growing the team throughout all departments. 2013-02-08 United States Colorado Durango 37.27528 -107.88006669999999 Durango, CO, United States',1,11,'2013-01-24 09:30:10','2023-02-08 00:00:00','2016-03-18 17:13:37','everyone',NULL,0,NULL,NULL,'daily','HR Specialist [Job Sample]','367','United States','Colorado','','76','<strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We are looking for an HR/ Office Manager. This is an opportunity to be a key part of growing the team throughout all departments.',NULL,NULL,'Durango',0,NULL,'United States Colorado Durango 37.27528 -107.88006669999999',NULL,1,NULL,NULL,'Durango, CO, United States',37.27528,-107.88006669999999),(20,6,5,'a:15:{s:12:\"pricing_type\";s:12:\"volume_based\";s:5:\"price\";s:0:\"\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:0:\"\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:0:\"\";s:13:\"renewal_price\";s:0:\"\";s:8:\"featured\";s:1:\"0\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:1:\"3\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:5:{i:1;s:1:\"1\";i:2;s:2:\"11\";i:3;s:2:\"21\";i:4;s:2:\"31\";i:5;s:2:\"41\";}s:17:\"listings_range_to\";a:5:{i:1;s:2:\"10\";i:2;s:2:\"20\";i:3;s:2:\"30\";i:4;s:2:\"40\";i:5;s:2:\"50\";}s:14:\"price_per_unit\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}s:25:\"renewal_price_per_listing\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}}s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"150\";s:33:\"upgrade_to_priority_listing_price\";s:3:\"100\";s:11:\"product_sid\";s:1:\"1\";}',1,'Services Senior Manager [Job Sample] Full time Accounting Admin-Clerical <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Advisory Services professionals strive to help clients improve business performance, comply with regulatory requirements, and achieve sustainable value over time. Our approach incorporates the balancing of risk and control considerations with the more traditional performance and cost factors. Advisory professionals are also able to provide clients with relevant industry and functional skills where they need them most. We are currently seeking a Senior Associate to join us in our Los Angeles office, focused on the Aerospace &amp; Defense industry.</p> 2013-02-23 United States Colorado Durango 81303 37.12459945678711 -107.92900085449219 Durango, CO 81303, United States',0,2,'2013-01-24 09:48:12','2023-02-23 00:00:00',NULL,'everyone','',0,NULL,NULL,'daily','Services Senior Manager [Job Sample]','343,344','United States','Colorado','81303','76','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Advisory Services professionals strive to help clients improve business performance, comply with regulatory requirements, and achieve sustainable value over time. Our approach incorporates the balancing of risk and control considerations with the more traditional performance and cost factors. Advisory professionals are also able to provide clients with relevant industry and functional skills where they need them most. We are currently seeking a Senior Associate to join us in our Los Angeles office, focused on the Aerospace &amp; Defense industry.</p>',NULL,NULL,'Durango',0,NULL,'United States Colorado Durango 81303 37.12459945678711 -107.92900085449219',NULL,1,NULL,NULL,'Durango, CO 81303, United States',37.12459945678711,-107.92900085449219),(21,6,5,'a:15:{s:12:\"pricing_type\";s:12:\"volume_based\";s:5:\"price\";s:0:\"\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:0:\"\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:0:\"\";s:13:\"renewal_price\";s:0:\"\";s:8:\"featured\";s:1:\"0\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:1:\"3\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:5:{i:1;s:1:\"1\";i:2;s:2:\"11\";i:3;s:2:\"21\";i:4;s:2:\"31\";i:5;s:2:\"41\";}s:17:\"listings_range_to\";a:5:{i:1;s:2:\"10\";i:2;s:2:\"20\";i:3;s:2:\"30\";i:4;s:2:\"40\";i:5;s:2:\"50\";}s:14:\"price_per_unit\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}s:25:\"renewal_price_per_listing\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}}s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"150\";s:33:\"upgrade_to_priority_listing_price\";s:3:\"100\";s:11:\"product_sid\";s:1:\"1\";}',1,'Laboratory Manager [Job Sample] Full time Admin-Clerical Management <strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Laboratory Manager needed - Critical Access Hospital near Lubbock is seeking a qualified person to supervise and manage the hospital-based laboratory. 2013-02-23 United States Georgia Atlanta 33.7489954 -84.3879824 Atlanta, GA, United States',0,1,'2013-01-24 09:52:28','2023-02-23 00:00:00',NULL,'everyone',NULL,0,NULL,NULL,'daily','Laboratory Manager [Job Sample]','344,373','United States','Georgia','','76','<strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Laboratory Manager needed - Critical Access Hospital near Lubbock is seeking a qualified person to supervise and manage the hospital-based laboratory.',NULL,NULL,'Atlanta',0,NULL,'United States Georgia Atlanta 33.7489954 -84.3879824',NULL,1,NULL,NULL,'Atlanta, GA, United States',33.7489954,-84.3879824),(22,6,4,'a:13:{s:5:\"price\";s:3:\"250\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"30\";s:18:\"number_of_listings\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:8:\"featured\";s:1:\"0\";s:13:\"renewal_price\";s:2:\"20\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:0:\"\";s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"100\";s:33:\"upgrade_to_priority_listing_price\";s:2:\"50\";s:11:\"product_sid\";s:1:\"4\";}',1,'Accountant [Job Sample] Full time Accounting Finance <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />What we need:</p> <p>Excellent interpersonal and communication skills.&nbsp; Ability to effectively communicate orally and in writing with all levels within the organization, as well as external parties.<br />Motivated, self-directed and results-driven approach to work, also takes ownership of assigned tasks.<br />The ability to meet deadlines.<br />Capable of establishing effective working relationships that promote teamwork and collaboration.<br />Willingness and ability to resolve issues that arise in the performance of the job duties.<br />Able to work in a dynamic and changing work environment.<br />Proficient with MS Office Suite required<br />Oracle experience preferred but not required.<br />Bachelors degree in Accounting/Finance<br />Refined products and/or pipelines experience preferred but not required.</p> 2013-02-23 United States California San Francisco 37.7749295 -122.41941550000001 San Francisco, CA, United States',0,1,'2013-01-24 09:57:07','2023-02-23 00:00:00',NULL,'everyone',NULL,0,NULL,NULL,'daily','Accountant [Job Sample]','343,359','United States','California','','76','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />What we need:</p>\r\n<p>Excellent interpersonal and communication skills.&nbsp; Ability to effectively communicate orally and in writing with all levels within the organization, as well as external parties.<br />Motivated, self-directed and results-driven approach to work, also takes ownership of assigned tasks.<br />The ability to meet deadlines.<br />Capable of establishing effective working relationships that promote teamwork and collaboration.<br />Willingness and ability to resolve issues that arise in the performance of the job duties.<br />Able to work in a dynamic and changing work environment.<br />Proficient with MS Office Suite required<br />Oracle experience preferred but not required.<br />Bachelors degree in Accounting/Finance<br />Refined products and/or pipelines experience preferred but not required.</p>',NULL,NULL,'San Francisco',0,NULL,'United States California San_ Francisco 37.7749295 -122.41941550000001',NULL,1,NULL,NULL,'San Francisco, CA, United States',37.7749295,-122.41941550000001),(23,6,3,'a:15:{s:12:\"pricing_type\";s:5:\"fixed\";s:5:\"price\";s:3:\"500\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:1:\"5\";s:13:\"renewal_price\";s:3:\"100\";s:8:\"featured\";s:1:\"1\";s:8:\"priority\";s:1:\"1\";s:18:\"number_of_pictures\";s:1:\"5\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:1:{i:1;s:0:\"\";}s:17:\"listings_range_to\";a:1:{i:1;s:0:\"\";}s:14:\"price_per_unit\";a:1:{i:1;s:0:\"\";}s:25:\"renewal_price_per_listing\";a:1:{i:1;s:0:\"\";}}s:5:\"video\";s:1:\"1\";s:33:\"upgrade_to_featured_listing_price\";s:0:\"\";s:33:\"upgrade_to_priority_listing_price\";s:0:\"\";s:11:\"product_sid\";s:1:\"2\";}',1,'Featured Project Manager [Job Sample] Full time Management <strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Project manager&nbsp;is responsible for&nbsp;the planning, execution, closing of a project and&nbsp;accomplishing the stated project objectives 2013-02-08 United States Massachusetts Cambridge 42.3736158 -71.1097335 Cambridge, MA, United States',1,8,'2013-01-24 10:02:00','2023-02-08 00:00:00','2016-03-18 17:13:37','everyone',NULL,0,NULL,NULL,'daily','Project Manager [Job Sample]','373','United States','Massachusetts','','76','<strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />Project manager&nbsp;is responsible for&nbsp;the planning, execution, closing of a project and&nbsp;accomplishing the stated project objectives',NULL,NULL,'Cambridge',0,NULL,'United States Massachusetts Cambridge 42.3736158 -71.1097335',NULL,1,NULL,NULL,'Cambridge, MA, United States',42.3736158,-71.1097335),(24,6,3,'a:15:{s:12:\"pricing_type\";s:12:\"volume_based\";s:5:\"price\";s:0:\"\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:0:\"\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:0:\"\";s:13:\"renewal_price\";s:0:\"\";s:8:\"featured\";s:1:\"0\";s:8:\"priority\";s:1:\"0\";s:18:\"number_of_pictures\";s:1:\"3\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:5:{i:1;s:1:\"1\";i:2;s:2:\"11\";i:3;s:2:\"21\";i:4;s:2:\"31\";i:5;s:2:\"41\";}s:17:\"listings_range_to\";a:5:{i:1;s:2:\"10\";i:2;s:2:\"20\";i:3;s:2:\"30\";i:4;s:2:\"40\";i:5;s:2:\"50\";}s:14:\"price_per_unit\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}s:25:\"renewal_price_per_listing\";a:5:{i:1;s:2:\"20\";i:2;s:2:\"18\";i:3;s:2:\"16\";i:4;s:2:\"14\";i:5;s:2:\"12\";}}s:5:\"video\";s:1:\"0\";s:33:\"upgrade_to_featured_listing_price\";s:3:\"150\";s:33:\"upgrade_to_priority_listing_price\";s:3:\"100\";s:11:\"product_sid\";s:1:\"1\";}',1,'Office Manager [Job Sample] Full time Accounting Admin-Clerical <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We&#39;re looking for an&nbsp;able to multi task&nbsp;person to work as office receptionist in our developing company.</p> <p>The Office Receptionist would be responsible for the following duties:<br />Schedules patient appointments.<br />Enters appointment date and time into computerized scheduler.<br />Records when appointments have been filled or canceled.<br />Telephones patients to remind them of appointments.<br />Telephones patients to reschedule missed appointments.<br />Calls patient referrals to solicit services.</p> 2013-02-23 United States Massachusetts Cambridge 42.3736158 -71.1097335 Cambridge, MA, United States',0,1,'2013-01-24 10:04:52','2023-02-23 00:00:00',NULL,'everyone',NULL,0,NULL,NULL,'daily','Office Manager [Job Sample]','343,344','United States','Massachusetts','','76','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We&#39;re looking for an&nbsp;able to multi task&nbsp;person to work as office receptionist in our developing company.</p>\r\n<p>The Office Receptionist would be responsible for the following duties:<br />Schedules patient appointments.<br />Enters appointment date and time into computerized scheduler.<br />Records when appointments have been filled or canceled.<br />Telephones patients to remind them of appointments.<br />Telephones patients to reschedule missed appointments.<br />Calls patient referrals to solicit services.</p>',NULL,NULL,'Cambridge',0,NULL,'United States Massachusetts Cambridge 42.3736158 -71.1097335',NULL,1,NULL,NULL,'Cambridge, MA, United States',42.3736158,-71.1097335),(25,6,3,'a:15:{s:12:\"pricing_type\";s:5:\"fixed\";s:5:\"price\";s:3:\"500\";s:16:\"listing_type_sid\";s:1:\"6\";s:17:\"expiration_period\";s:2:\"10\";s:16:\"listing_duration\";s:4:\"7300\";s:18:\"number_of_listings\";s:1:\"5\";s:13:\"renewal_price\";s:3:\"100\";s:8:\"featured\";s:1:\"1\";s:8:\"priority\";s:1:\"1\";s:18:\"number_of_pictures\";s:1:\"5\";s:20:\"volume_based_pricing\";a:4:{s:19:\"listings_range_from\";a:1:{i:1;s:0:\"\";}s:17:\"listings_range_to\";a:1:{i:1;s:0:\"\";}s:14:\"price_per_unit\";a:1:{i:1;s:0:\"\";}s:25:\"renewal_price_per_listing\";a:1:{i:1;s:0:\"\";}}s:5:\"video\";s:1:\"1\";s:33:\"upgrade_to_featured_listing_price\";s:0:\"\";s:33:\"upgrade_to_priority_listing_price\";s:0:\"\";s:11:\"product_sid\";s:1:\"2\";}',1,'Featured Office Receptionist [Job Sample] Full time Accounting Admin-Clerical <p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We&#39;re looking for an&nbsp;able to multi task&nbsp;person to work as office receptionist in our developing company.</p> <p>The Office Receptionist would be responsible for the following duties:<br />Schedules patient appointments.<br />Enters appointment date and time into computerized scheduler.<br />Records when appointments have been filled or canceled.<br />Telephones patients to remind them of appointments.<br />Telephones patients to reschedule missed appointments.<br />Calls patient referrals to solicit services.</p> 2013-02-08 United States Massachusetts Cambridge 42.3736158 -71.1097335 Cambridge, MA, United States',1,8,'2013-01-24 10:06:16','2023-02-08 00:00:00','2016-03-18 17:13:37','everyone',NULL,0,NULL,NULL,'daily','Office Receptionist [Job Sample]','343,344','United States','Massachusetts','','76','<p><strong>This is not a real job. Please don&#39;t apply to it. </strong><br /><br />We&#39;re looking for an&nbsp;able to multi task&nbsp;person to work as office receptionist in our developing company.</p>\r\n<p>The Office Receptionist would be responsible for the following duties:<br />Schedules patient appointments.<br />Enters appointment date and time into computerized scheduler.<br />Records when appointments have been filled or canceled.<br />Telephones patients to remind them of appointments.<br />Telephones patients to reschedule missed appointments.<br />Calls patient referrals to solicit services.</p>',NULL,NULL,'Cambridge',0,NULL,'United States Massachusetts Cambridge 42.3736158 -71.1097335',NULL,1,NULL,NULL,'Cambridge, MA, United States',42.3736158,-71.1097335);
/*!40000 ALTER TABLE `listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings_active_period`
--

DROP TABLE IF EXISTS `listings_active_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listings_active_period` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listing_sid` int(10) NOT NULL,
  `number_of_days` int(10) DEFAULT '0',
  `featured_period` int(10) DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `listing_sid` (`listing_sid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings_active_period`
--

LOCK TABLES `listings_active_period` WRITE;
/*!40000 ALTER TABLE `listings_active_period` DISABLE KEYS */;
INSERT INTO `listings_active_period` VALUES (1,3,-26,0),(2,11,-38,-38),(3,16,-48,0),(4,15,-48,0),(5,14,-48,0),(6,13,-48,0),(7,10,-38,0),(8,9,-103,-103),(9,8,-103,-103),(10,12,-38,0),(11,7,-103,0);
/*!40000 ALTER TABLE `listings_active_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings_properties`
--

DROP TABLE IF EXISTS `listings_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listings_properties` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_sid` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `value` text,
  `add_parameter` varchar(255) DEFAULT NULL,
  `complex_enum` int(10) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `object_sid` (`object_sid`,`id`,`complex_enum`),
  KEY `id` (`id`),
  KEY `add_parameter` (`add_parameter`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings_properties`
--

LOCK TABLES `listings_properties` WRITE;
/*!40000 ALTER TABLE `listings_properties` DISABLE KEYS */;
INSERT INTO `listings_properties` VALUES (85,13,'ApplicationSettings','','1',NULL),(87,14,'ApplicationSettings','','1',NULL),(131,19,'ApplicationSettings','','1',NULL),(133,20,'ApplicationSettings','','1',NULL),(135,21,'ApplicationSettings','','1',NULL),(137,22,'ApplicationSettings','','1',NULL),(139,23,'ApplicationSettings','','1',NULL),(141,24,'ApplicationSettings','','1',NULL),(143,25,'ApplicationSettings','','1',NULL);
/*!40000 ALTER TABLE `listings_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `navigation_menu`
--

DROP TABLE IF EXISTS `navigation_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navigation_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navigation_menu`
--

LOCK TABLES `navigation_menu` WRITE;
/*!40000 ALTER TABLE `navigation_menu` DISABLE KEYS */;
INSERT INTO `navigation_menu` VALUES (1,'Jobs','/jobs/'),(2,'Companies','/companies/'),(3,'Post a Job','/add-listing/?listing_type_id=Job'),(4,'Resume Search','/resumes/'),(5,'Pricing','/employer-products/');
/*!40000 ALTER TABLE `navigation_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `uri` varchar(255) NOT NULL DEFAULT '',
  `pass_parameters_via_uri` int(1) DEFAULT NULL,
  `module` varchar(255) NOT NULL DEFAULT '',
  `function` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `access_type` varchar(25) NOT NULL DEFAULT '',
  `parameters` text NOT NULL,
  `keywords` text,
  `description` text NOT NULL,
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` mediumtext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNIQUE_KEY` (`uri`,`access_type`),
  KEY `module` (`module`),
  KEY `function` (`function`),
  KEY `access_type` (`access_type`)
) ENGINE=MyISAM AUTO_INCREMENT=577 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES ('/',NULL,'dashboard','view','index.tpl','','admin','','','',1,NULL),('/edit-templates/',NULL,'template_manager','edit_templates','index.tpl','','admin','','','',31,NULL),('/edit-themes/',NULL,'template_manager','theme_editor','index.tpl','','admin','','','',32,NULL),('/publications/',NULL,'publications','edit_publications','index.tpl','','admin','','','',60,NULL),('/user-pages/',NULL,'user_pages','edit_user_pages','index.tpl','','admin','','','',81,NULL),('/login/',0,'users','login','index.tpl','Sign in','user','N;','','',99,NULL),('/contact/',0,'static_content','show_static_content','../miscellaneous/contact_form_index.tpl','Contact Us','user','N;',NULL,'',111,''),('/payment-page/',NULL,'payment','payment_page','index.tpl','','user','N;','','',146,NULL),('/callback/',NULL,'payment','callback','','','user','N;','','',148,NULL),('/user-groups/',NULL,'users','user_groups','index.tpl','User Groups','admin','','','',170,NULL),('/add-user-group/',NULL,'users','add_user_group','index.tpl','Add a New User Group','admin','','','',171,NULL),('/edit-user-group/',NULL,'users','edit_user_group','index.tpl','Edit User Group','admin','','','',172,NULL),('/delete-user-group/',NULL,'users','delete_user_group','index.tpl','Delete User Group','admin','','','',173,NULL),('/',0,'classifieds','search_form','main.tpl','','user','a:2:{s:15:\"listing_type_id\";s:3:\"Job\";s:13:\"form_template\";s:16:\"quick_search.tpl\";}','','',177,NULL),('/user-profile-fields/',NULL,'users','edit_user_profile','index.tpl','','admin','','','',178,NULL),('/add-user-profile-field/',NULL,'users','add_user_profile_field','index.tpl','','admin','','','',179,NULL),('/edit-user-profile/',NULL,'users','edit_user_profile','index.tpl','','admin','','','',180,NULL),('/delete-user-profile-field/',NULL,'users','delete_user_profile_field','index.tpl','','admin','','','',181,NULL),('/users/',NULL,'users','users','index.tpl','Users','admin','','','',183,NULL),('/edit-user/',NULL,'users','edit_user','index.tpl','Edit User','admin','','','',187,NULL),('/registration/',NULL,'users','registration','index.tpl','Registration','user','N;','','',188,NULL),('/listing-fields/',NULL,'classifieds','listing_fields','index.tpl','Listing Fields','admin','','','',189,NULL),('/delete-listing-field/',NULL,'classifieds','delete_listing_field','index.tpl','Delete Listing Field','admin','','','',191,NULL),('/edit-listing-field/',NULL,'classifieds','edit_listing_field','index.tpl','Edit Listing Field','admin','','','',192,NULL),('/edit-listing-type/',NULL,'classifieds','edit_listing_type','index.tpl','Edit Listing Type','admin','','','',195,NULL),('/add-listing-type-field/',NULL,'classifieds','add_listing_type_field','index.tpl','Add Listing Type Field','admin','','','',197,NULL),('/edit-listing-type-field/',NULL,'classifieds','edit_listing_type_field','index.tpl','Edit Listing Type Field','admin','','','',198,NULL),('/delete-listing-type-field/',NULL,'classifieds','delete_listing_type_field','index.tpl','Delete Listing Type Field','admin','','','',199,NULL),('/add-listing/',NULL,'classifieds','add_listing','index.tpl','','admin','','','',200,NULL),('/add-listing/',1,'classifieds','add_listing','index.tpl','','user','N;','','',205,NULL),('/pay-for-listing/',NULL,'classifieds','pay_for_listing','index.tpl','Pay For Listing','user','','','',210,NULL),('/find-jobs/',0,'classifieds','search_form','index.tpl','Find Jobs','user','a:1:{s:15:\"listing_type_id\";s:3:\"Job\";}','','',212,NULL),('/edit-listing/',NULL,'classifieds','edit_listing','index.tpl','Edit Listing','admin','','','',214,NULL),('/my-listings/',1,'classifieds','my_listings','index.tpl','My Listings','user','N;','','',215,NULL),('/edit-list/',NULL,'classifieds','edit_list','index.tpl','Edit List','admin','','','',218,NULL),('/edit-list-item/',NULL,'classifieds','edit_list_item','index.tpl','Edit List Item','admin','','','',219,NULL),('/search-results/',0,'classifieds','search_results','','','user','N;','','',223,NULL),('/logout/',NULL,'users','logout','','','user','N;','','',224,NULL),('/edit-user-profile-field/edit-list/',NULL,'users','edit_list','index.tpl','Edit List','admin','','','',227,NULL),('/edit-user-profile-field/edit-list-item/',NULL,'users','edit_list_item','index.tpl','Edit List Item','admin','','','',228,NULL),('/edit-user-profile-field/',NULL,'users','edit_user_profile_field','index.tpl','Edit User Profile Field','admin','','','',229,NULL),('/password-recovery/',NULL,'users','password_recovery','','Password Recovery','user','N;','','',230,NULL),('/edit-listing-field/edit-list/',NULL,'classifieds','edit_list','index.tpl','Edit List','admin','','','',231,NULL),('/edit-listing-field/edit-list-item/',NULL,'classifieds','edit_list_item','index.tpl','Edit List Item','admin','','','',232,NULL),('/edit-listing-field/edit-tree/',NULL,'classifieds','edit_tree','index.tpl','Edit Tree','admin','','','',233,NULL),('/import-tree-data/',NULL,'classifieds','import_tree_data','index.tpl','Import Tree Data','admin','','','',234,NULL),('/settings/',NULL,'miscellaneous','settings','','','admin','','','',235,NULL),('/edit-profile/',NULL,'users','edit_profile','','Edit Profile','user','N;','','',236,NULL),('/adminpswd/',NULL,'miscellaneous','adminpswd','index.tpl','Admin Password','admin','','','',239,NULL),('/about/',0,'static_content','show_static_content',NULL,'About us','user','N;',NULL,'',240,'<p>You can fill this page with any relevant information.</p>'),('/ajax/',0,'miscellaneous','ajax','empty.tpl','Ajax','user','N;','','',255,NULL),('/search-resumes/',0,'classifieds','search_form','','Search Resumes','user','a:2:{s:15:\"listing_type_id\";s:6:\"Resume\";s:13:\"form_template\";s:23:\"search_form_resumes.tpl\";}','','',256,NULL),('/jobs/',0,'classifieds','search_results','display.tpl','Jobs','user','a:5:{s:21:\"default_sorting_field\";s:15:\"activation_date\";s:21:\"default_sorting_order\";s:4:\"DESC\";s:25:\"default_listings_per_page\";s:2:\"20\";s:16:\"results_template\";s:23:\"search_results_jobs.tpl\";s:15:\"listing_type_id\";s:3:\"Job\";}',NULL,'',257,NULL),('/resumes/',0,'classifieds','search_results','display.tpl','Resumes','user','a:5:{s:21:\"default_sorting_field\";s:15:\"activation_date\";s:21:\"default_sorting_order\";s:4:\"DESC\";s:25:\"default_listings_per_page\";s:2:\"20\";s:16:\"results_template\";s:26:\"search_results_resumes.tpl\";s:15:\"listing_type_id\";s:6:\"Resume\";}',NULL,'',258,NULL),('/job/',1,'classifieds','display_listing','display.tpl',NULL,'user','a:2:{s:16:\"display_template\";s:15:\"display_job.tpl\";s:15:\"listing_type_id\";s:3:\"Job\";}',NULL,'',259,NULL),('/resume/',1,'classifieds','display_listing','display.tpl','Resume','user','a:2:{s:16:\"display_template\";s:18:\"display_resume.tpl\";s:15:\"listing_type_id\";s:6:\"Resume\";}',NULL,'',260,NULL),('/categories/',1,'classifieds','browse','','','user','a:2:{s:11:\"level1Field\";s:11:\"JobCategory\";s:15:\"listing_type_id\";s:3:\"Job\";}','','',261,NULL),('/cities/',1,'classifieds','browse','','','user','a:3:{s:11:\"level1Field\";s:4:\"City\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','','',262,NULL),('/my-jobs/',0,'classifieds','my_listings','','','user','a:1:{s:15:\"listing_type_id\";s:3:\"Job\";}','','',263,NULL),('/rss/',0,'classifieds','latest_listings','empty.tpl','Rss','user','a:4:{s:11:\"items_count\";s:2:\"10\";s:12:\"listing_type\";s:3:\"Job\";s:8:\"template\";s:12:\"feed_rss.tpl\";s:9:\"mime_type\";s:19:\"application/rss+xml\";}','','test description',264,NULL),('/my-account/',0,'menu','user_menu','index.tpl','My Account','user','N;','','',265,NULL),('/apply-now/',0,'classifieds','apply_now','blank.tpl','Apply now','user','N;','','',266,NULL),('/my-job-details/',1,'classifieds','display_my_listing','display.tpl','My Job Details','user','a:1:{s:16:\"display_template\";s:15:\"display_job.tpl\";}',NULL,'',269,NULL),('/my-resume-details/',1,'classifieds','display_my_listing','display.tpl',NULL,'user','a:1:{s:16:\"display_template\";s:18:\"display_resume.tpl\";}',NULL,'',270,NULL),('/terms-of-use/',0,'static_content','show_static_content','','Terms & Conditions','user','N;',NULL,'',277,'<h4>Introduction</h4>\r\n<p style=\"text-align:start\">Please read these Terms &amp; Conditions carefully before using <u>[your site name]</u>.<br /><u>[your site name]</u> reserves the right to modify these Terms &amp; Conditions at any time.</p>\r\n<h4><br />Services Provided</h4>\r\n<p><u>[your site name]</u> provides a service to bring Job Seekers and Employers together. Job Seekers and Employers can register, create profiles/job posts and search for jobs and resumes.</p>\r\n<h4><br />Privacy Policy</h4>\r\n<p>Job Seeker personal data will be available to Employers visiting <u>[your site name]</u>. Personal data includes a Name which is mandatory, an email Address which is mandatory and a Telephone Number which is optional.</p>\r\n<p>Personal data provided by the user may be used by <u>[your site name]</u> to notify the user of any news, and or promotional offers relating only to the <u>[your site name]</u> website. The user can unsubscribe from these notifications at anytime.</p>\r\n<p style=\"text-align:start\"><u>[your site name]</u> will not disclose user personal data to any third party.</p>\r\n<h4><br />Website Use</h4>\r\n<p><u>[your site name]</u> may not be used for any of the following purposes:</p>\r\n<ol><li>To contact <u>[your site name]</u> users regarding any issue apart from the purpose of recruitment.</li><li style=\"text-align:start\">To contact <u>[your site name]</u> users to offer any services from a 3rd party company.</li><li>To post any illegal content.</li></ol>\r\n<p>The user is required to provide truthful information in their profile or job post.</p>\r\n<p>It is prohibited to use any text or images from <u>[your site name]</u> for personal or commercial use.<br />&nbsp;</p>\r\n<h4>User Information</h4>\r\n<p><u>[your site name]</u> does not hold responsibility for any untruthful and/or inaccurate information included in job posts and profiles.</p><u>[your site name]</u> reserves the right to edit or delete any information submitted by the user to the website.\r\n<h4><br />Liability</h4>\r\n<p><u>[your site name]</u> will not be responsible for any loss or damage the user may encounter from using the website.<br />&nbsp;</p>\r\n<h4>Cookies Policy</h4>\r\n<p>Our website uses cookies.</p>\r\n<p>A cookie is a file containing an identifier (a string of letters and numbers) that is sent by a web server to a web browser and is stored by the browser. The identifier is then sent back to the server each time the browser requests a page from the server.</p>\r\n<p>We use Google Analytics to analyse the use of our website.</p>\r\n<p>Our analytics service provider generates statistical and other information about website use by means of cookies.</p>\r\n<p>You can delete cookies already stored on your computer. Please visit the &#39;Help&#39; option in your browser menu to learn how to do this. Deleting cookies will have a negative impact on the usability of this website.</p>'),('/manage-breadcrumbs/',NULL,'breadcrumbs','manage_breadcrumbs','','Breadcrumbs','admin','','','',279,NULL),('/listing-feeds/',0,'classifieds','listing_feeds','empty.tpl','Listing Feeds','user','a:1:{s:14:\"count_listings\";s:2:\"20\";}','','',282,NULL),('/listing-import/',NULL,'listing_import','show_import','','','admin','','','',283,NULL),('/edit-listing-field/edit-submultilist/',NULL,'classifieds','edit_submultilist','index.tpl','Edit List item','admin','','','',301,NULL),('/edit-user-profile-field/edit-tree/',NULL,'users','edit_tree','index.tpl','Edit Tree','admin','','','',312,NULL),('/companies/',0,'classifieds','browseCompany','display.tpl','Companies','user','a:2:{s:16:\"display_template\";s:17:\"browseCompany.tpl\";s:15:\"listing_type_id\";s:3:\"Job\";}',NULL,'',314,NULL),('/task-scheduler-settings/',NULL,'miscellaneous','task_scheduler_settings','','Task Scheduler Settings','admin','','','',316,NULL),('/filters/',NULL,'miscellaneous','filters','','','admin','','','',317,NULL),('/employers-list/',0,'users','employers_list','','','user','a:1:{s:13:\"user_group_id\";s:8:\"Employer\";}','','',318,NULL),('/login-as-user/',NULL,'users','login_as_user','empty.tpl','Login as user','admin','','','',321,NULL),('/external-form/',0,'classifieds','search_form','empty.tpl','SJB','user','a:2:{s:15:\"listing_type_id\";s:3:\"Job\";s:13:\"form_template\";s:16:\"quick_search.tpl\";}','','',324,NULL),('/refine-search-settings/',NULL,'classifieds','refine_search','','','admin','','','',327,NULL),('/job-import/',0,'classifieds','job_import','index.tpl','Import','user','a:1:{s:15:\"listing_type_id\";s:3:\"Job\";}','','',333,NULL),('/jobg8_incoming/',0,'miscellaneous','jobg8_incoming','','','user','N;','','',336,NULL),('/jobg8_outgoing/',0,'miscellaneous','jobg8_outgoing','','','user','N;','','',338,NULL),('/apply-now-external/',0,'classifieds','apply_now_jobg8','','Apply Now','user','N;','','',339,NULL),('/edit-listing-field/edit-fields/edit-tree/',NULL,'classifieds','edit_complex_tree','index.tpl','Edit Tree','admin','','','',357,NULL),('/company/',1,'classifieds','search_results','display.tpl',NULL,'user','a:5:{s:21:\"default_sorting_field\";s:15:\"activation_date\";s:21:\"default_sorting_order\";s:4:\"DESC\";s:25:\"default_listings_per_page\";s:2:\"20\";s:16:\"results_template\";s:23:\"search_results_jobs.tpl\";s:15:\"listing_type_id\";s:3:\"Job\";}',NULL,'',358,NULL),('/posting-pages/',1,'classifieds','posting_pages','index.tpl','','admin','','','',361,NULL),('/import-listings/',NULL,'classifieds','import_listings','index.tpl','Import Listings','admin','','','',366,NULL),('/listing-feeds/',NULL,'classifieds','listing_feeds','index.tpl','Listing Feeds','admin','','','',368,NULL),('/import-users/',NULL,'classifieds','import_users','index.tpl','Import Users','admin','','','',372,NULL),('/listing-actions/',NULL,'classifieds','listing_actions','index.tpl','Listing Actions','admin','','','',374,NULL),('/delete-uploaded-file/',NULL,'classifieds','delete_uploaded_file','index.tpl','Delete Uploaded File','admin','','','',375,NULL),('/delete-complex-file/',NULL,'classifieds','delete_complex_file','index.tpl','Delete Complex File','admin','','','',376,NULL),('/listing-comments/',NULL,'comments','listing_comments','index.tpl','Listing Comments','admin','','','',377,NULL),('/manage-languages/',NULL,'I18N','manage_languages','index.tpl','Languages','admin','','','',380,NULL),('/manage-phrases/',NULL,'I18N','manage_phrases','index.tpl','Manage Phrases','admin','','','',381,NULL),('/edit-phrase/',NULL,'I18N','edit_phrase','index.tpl','Edit Phrase','admin','','','',383,NULL),('/add-import/',NULL,'listing_import','add_import','index.tpl','Add new import','admin','','','',386,NULL),('/show-import/',NULL,'listing_import','show_import','index.tpl','Show import','admin','','','',387,NULL),('/edit-import/',NULL,'listing_import','edit_import','index.tpl','Edit data soupce','admin','','','',388,NULL),('/run-import/',NULL,'listing_import','run_import','index.tpl','Run import from data source','admin','','','',389,NULL),('/delete-import/',NULL,'listing_import','delete_import','index.tpl','Delete data source','admin','','','',390,NULL),('/listing-import/user-fields/',NULL,'listing_import','user_fields','index.tpl','User Fields','admin','','','',391,NULL),('/maintenance-mode/',NULL,'miscellaneous','maintenance_mode','index.tpl','Maintenance Mode','user','','','',392,NULL),('/configure-gateway/',NULL,'payment','configure_gateway','index.tpl','Payment Gateway Control Panel','admin','','','',395,NULL),('/payment-page/',NULL,'payment','payment_page','index.tpl','Payment Gateway Control Panel','admin','','','',396,NULL),('/listing-rating/',NULL,'rating','listing_rating','index.tpl','Listing Rating','admin','','','',399,NULL),('/edit-css/',NULL,'template_manager','edit_css','index.tpl','Edit css files','admin','','','',402,NULL),('/send-activation-letter/',NULL,'users','send_activation_letter','index.tpl','Sending Activation Letter','admin','N;','','',405,NULL),('/change-password/',NULL,'users','change_password','index.tpl','Change Password','user','N;','','',406,NULL),('/users/delete-uploaded-file/',1,'users','delete_uploaded_file','index.tpl','Delete Uploaded File','user','N;','','',408,NULL),('/add-user/',1,'users','add_user','index.tpl','Add User','admin','N;','','',409,NULL),('/banned-ips/',NULL,'users','banned_ips','index.tpl','Banned IPs','admin','N;','','',410,NULL),('/registration-social/',0,'social','registration_social','','Social Registration','user','N;','','',413,NULL),('/social/',1,'social','social_plugins','','Social Plugins','user','N;','','',414,NULL),('/states/',1,'classifieds','browse','','','user','a:3:{s:11:\"level1Field\";s:5:\"State\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','','',416,NULL),('/export-users/',NULL,'users','export_users','index.tpl','Export Users','admin','N;','','',417,NULL),('/export-listings/',NULL,'classifieds','export_listings','index.tpl','Export Listings','admin','','','',418,NULL),('/edit-listing-field/edit-fields/',NULL,'classifieds','edit_complex_fields','index.tpl','Edit Fields','admin','','','',419,NULL),('/classifieds/delete-uploaded-file/',1,'classifieds','delete_uploaded_file','index.tpl','Delete Uploaded File','user','N;','','',433,NULL),('/edit-profile/',NULL,'sub_admins','edit_profile','','','admin','','','',439,NULL),('/task-scheduler/',NULL,'miscellaneous','task_scheduler','empty.tpl','Run Task Scheduler','user','','','',440,NULL),('/job-preview/',1,'classifieds','display_my_listing','','Job Preview','user','a:1:{s:16:\"display_template\";s:15:\"display_job.tpl\";}','','',444,NULL),('/resume-preview/',1,'classifieds','display_my_listing','','Resume Preview','user','a:1:{s:16:\"display_template\";s:18:\"display_resume.tpl\";}','','',445,NULL),('/users/delete-complex-file/',NULL,'users','delete_complex_file','index.tpl','Delete Complex File','admin','','','',446,NULL),('/users/delete-uploaded-file/',NULL,'users','delete_uploaded_file','index.tpl','Delete Uploaded File','admin','','','',447,NULL),('/countries/',1,'classifieds','browse','','','user','a:3:{s:11:\"level1Field\";s:7:\"Country\";s:15:\"listing_type_id\";s:3:\"Job\";s:6:\"parent\";s:8:\"Location\";}','','',451,NULL),('/products/',NULL,'payment','products','index.tpl','Products','admin','','','',453,NULL),('/add-product/',NULL,'payment','add_product','index.tpl','Add Product','admin','','','',454,NULL),('/edit-product/',NULL,'payment','edit_product','index.tpl','Edit Product','admin','','','',458,NULL),('/products/',0,'payment','user_products','index.tpl','Products','user','N;','','',461,NULL),('/shopping-cart/',0,'payment','shopping_cart','index.tpl','Checkout','user','N;','','',463,NULL),('/user-product/',NULL,'payment','user_product','empty.tpl','User Product','admin','a:1:{s:4:\"page\";s:12:\"user_product\";}','','',464,NULL),('/user-products/',NULL,'payment','user_product','index.tpl','User Products','admin','a:1:{s:4:\"page\";s:13:\"user_products\";}','','',465,NULL),('/add-user-product/',NULL,'payment','user_product','empty.tpl','','admin','a:1:{s:4:\"page\";s:11:\"add_product\";}','','',466,NULL),('/promotions/',NULL,'payment','manage_promotions','index.tpl','Manage Promotions','admin','','','',476,NULL),('/add-promotion-code/',NULL,'payment','manage_promotions','index.tpl','Add New Promotion Code','admin','a:1:{s:6:\"action\";s:3:\"add\";}','','',477,NULL),('/edit-promotion-code/',NULL,'payment','manage_promotions','index.tpl','Edit Promotion Code','admin','a:1:{s:6:\"action\";s:4:\"edit\";}','','',478,NULL),('/edit-email-templates/',1,'template_manager','edit_email_templates','index.tpl','','admin','','','',483,NULL),('/edit-email-templates/delete-uploaded-file/',1,'template_manager','delete_uploaded_file','','Delete Uploaded File','admin','','','',485,NULL),('/kcfinder/',1,'miscellaneous','kcfinder','','','admin','','','',499,NULL),('/employer-products/',0,'payment','user_products','','Pricing','user','a:1:{s:11:\"userGroupID\";s:8:\"Employer\";}','','',500,NULL),('/jobseeker-products/',0,'payment','user_products','','Pricing','user','a:1:{s:11:\"userGroupID\";s:9:\"JobSeeker\";}','','',501,NULL),('/update-to-new-version/',NULL,'miscellaneous','update_to_new_version','','Update to new version','admin','','','',502,NULL),('/manage-employers/',NULL,'users','users','','','admin','a:1:{s:14:\"user_group_sid\";s:2:\"41\";}','','',503,NULL),('/manage-jobseekers/',NULL,'users','users','','','admin','a:1:{s:14:\"user_group_sid\";s:2:\"36\";}','','',504,NULL),('/choose-user/',NULL,'users','choose_user','','Choose User','admin','','','',507,NULL),('/guest-alerts/create/',0,'guest_alerts','create','blank.tpl','Create Guest Alert','user','N;','','',520,NULL),('/guest-alerts/replace/',0,'guest_alerts','replace','blank.tpl','Replace Guest Alert','user','N;','','',521,NULL),('/guest-alerts/unsubscribe/',0,'guest_alerts','unsubscribe','','Unsubscribe','user','N;','','',523,NULL),('/guest-alerts/',0,'guest_alerts','manage','','Manage Guest Alerts','admin','','','',524,NULL),('/guest-alerts/export/',NULL,'guest_alerts','export','','Export Guest Alerts','admin','','','',525,NULL),('/view-invoice/',NULL,'payment','edit_invoice','','Edit Invoice','admin','N;','','',534,NULL),('/manage-invoices/',NULL,'payment','manage_invoices','','Manage Invoices','admin','N;','','',535,NULL),('/print-invoice/',NULL,'payment','edit_invoice','blank.tpl','','admin','a:1:{s:8:\"template\";s:17:\"print_invoice.tpl\";}','','',537,NULL),('/paypal-pro-fill-payment-card/',0,'payment','paypal_pro_fill_payment_card','',NULL,'user','N;',NULL,'',541,NULL),('/promotions/log/',1,'payment','promotions_log','','Promotions Log','admin','','','',550,NULL),('/statistics/promotions/',NULL,'statistics','promotions','','Promotions Statistics','admin','','','',551,NULL),('/statistics/promotions/print/',NULL,'statistics','promotions','blank.tpl','Print Promotions Statistics','admin','a:1:{s:8:\"template\";s:33:\"print_promotions_statistics.tpl\";}','','',552,NULL),('/manage-jobs/',NULL,'classifieds','manage_listings','index.tpl','Manage Jobs','admin','a:1:{s:16:\"listing_type_sid\";s:1:\"6\";}','','',556,NULL),('/manage-resumes/',NULL,'classifieds','manage_listings','index.tpl','Manage Resumes','admin','a:1:{s:16:\"listing_type_sid\";s:1:\"7\";}','','',557,NULL),('/edit-job/',1,'classifieds','edit_listing','','Edit Job','user','s:0:\"\";','','',558,NULL),('/edit-resume/',1,'classifieds','edit_listing','','Edit Resume','user','s:0:\"\";','','',560,NULL),('/manage-resume/',1,'classifieds','manage_listing','','Manage Resume','user','s:0:\"\";','','',561,NULL),('/social-media/',1,'social_media','social_media','index.tpl','','admin','a:0:{};','','',562,NULL),('/manage-users/',1,'users','users','','','admin','s:0:\"\";','','',568,NULL),('/products/employer/',NULL,'payment','products','index.tpl','Products','admin','a:1:{s:13:\"user_group_id\";s:8:\"Employer\";}','','',569,NULL),('/products/jobseeker/',NULL,'payment','products','index.tpl','Products','admin','a:1:{s:13:\"user_group_id\";s:9:\"JobSeeker\";}','','',570,NULL),('/backfilling/',NULL,'miscellaneous','plugins',NULL,'Job Backfilling','admin','',NULL,'',571,NULL),('/customize-theme/',NULL,'template_manager','customize_theme','index.tpl','','admin','','','',572,NULL),('/navigation-menu/',NULL,'template_manager','navigation_menu','index.tpl','','admin','','','',573,NULL),('/blog/',1,'blog','blog','index.tpl','Blog','user','','','',574,NULL),('/blog/',1,'blog','blog','index.tpl','Blog','admin','','','',575,NULL),('/feeds/',1,'classifieds','listing_feeds','empty.tpl','Listing Feeds','user','a:0:{}','','',576,NULL);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parsers`
--

DROP TABLE IF EXISTS `parsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `url` text NOT NULL,
  `usr_id` int(11) NOT NULL DEFAULT '0',
  `usr_name` varchar(255) DEFAULT NULL,
  `maper` text,
  `default_value` text,
  `maper_user` text,
  `default_value_user` text,
  `xml` text NOT NULL,
  `active` tinyint(3) NOT NULL DEFAULT '0',
  `add_new_user` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `external_id` varchar(255) DEFAULT '0',
  `product_sid` int(10) unsigned DEFAULT NULL,
  `import_type` varchar(20) NOT NULL DEFAULT 'increment',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `active` (`active`),
  KEY `usr_id` (`usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parsers`
--

LOCK TABLES `parsers` WRITE;
/*!40000 ALTER TABLE `parsers` DISABLE KEYS */;
/*!40000 ALTER TABLE `parsers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_gateways` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(64) NOT NULL DEFAULT '',
  `caption` varchar(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `id` (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways`
--

LOCK TABLES `payment_gateways` WRITE;
/*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
INSERT INTO `payment_gateways` VALUES (1,'2checkout','2Checkout',1),(2,'authnet_sim','Authorize.Net',1),(4,'paypal_standard','PayPal Standard',1),(7,'paypal_pro','PayPal Pro',1);
/*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateways_properties`
--

DROP TABLE IF EXISTS `payment_gateways_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_gateways_properties` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_sid` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `value` text,
  `add_parameter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `object_sid` (`object_sid`,`id`),
  KEY `id` (`id`),
  KEY `add_parameter` (`add_parameter`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways_properties`
--

LOCK TABLES `payment_gateways_properties` WRITE;
/*!40000 ALTER TABLE `payment_gateways_properties` DISABLE KEYS */;
INSERT INTO `payment_gateways_properties` VALUES (7,1,'2co_account_id','1441929',''),(8,1,'secret_word','tango',''),(9,1,'demo','1',''),(10,4,'paypal_account_email','nwyksa_1285483066_biz@gmail.com',''),(11,4,'currency_code','USD',''),(12,4,'use_sandbox','1',''),(13,2,'authnet_api_login_id','2S3bb3W3',''),(14,2,'authnet_api_transaction_key','255D2a3G2t7LpLRy',''),(15,2,'authnet_api_md5_hash_value','first',''),(16,2,'currency_code','USD',''),(17,2,'authnet_use_test_account','1',''),(27,1,'2co_api_user_login','some_user_keensteps',''),(28,1,'2co_api_user_password','sjb_test',''),(48,7,'country','US',''),(49,1,'sandbox','0',''),(50,7,'user_name','nwyksa_1309859439_biz_api1.gmail.com',''),(51,7,'user_password','1309859488',''),(52,7,'user_signature','AmFybhbzL8c6.tDGIZ0TFJa8IhLEAkxQ4Y3nx3OTfD-AqSlpzMhrZVO2',''),(53,7,'use_sandbox','1','');
/*!40000 ALTER TABLE `payment_gateways_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `name` (`name`),
  KEY `role` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=328495 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (325448,'product','apply_for_a_job','10','inherit','','Please subscribe to Apply'),(325469,'product','post_job','10','inherit','',''),(325470,'product','post_resume','10','allow','3',''),(325473,'contract','post_resume','1','allow','3',NULL),(325474,'contract','post_job','1','inherit','',NULL),(325499,'contract','apply_for_a_job','1','inherit','',NULL),(325503,'contract','post_resume','5','allow','3',NULL),(325504,'contract','post_job','5','inherit','',NULL),(325529,'contract','apply_for_a_job','5','inherit','',NULL),(326648,'product','apply_for_a_job','5','inherit','hide',''),(326669,'product','post_job','5','inherit','',''),(326670,'product','post_resume','5','inherit','',''),(326678,'product','apply_for_a_job','4','inherit','',''),(326699,'product','post_job','4','allow','10',''),(326700,'product','post_resume','4','inherit','',''),(326738,'product','apply_for_a_job','2','inherit','hide',''),(326759,'product','post_job','2','allow','',''),(326760,'product','post_resume','2','inherit','',''),(327098,'product','apply_for_a_job','11','inherit','',''),(327119,'product','post_job','11','allow','',''),(327120,'product','post_resume','11','inherit','',''),(327338,'product','apply_for_a_job','9','inherit','','Please subscribe to Apply'),(327359,'product','post_job','9','inherit','',''),(327360,'product','post_resume','9','allow','',''),(327818,'product','apply_for_a_job','7','inherit','',''),(327838,'product','post_job','7','allow','10',''),(327839,'product','post_resume','7','inherit','',''),(328148,'product','apply_for_a_job','1','inherit','hide',''),(328168,'product','post_job','1','allow','',''),(328169,'product','post_resume','1','inherit','',''),(328238,'product','apply_for_a_job','3','inherit','hide',''),(328258,'product','post_job','3','inherit','',''),(328259,'product','post_resume','3','inherit','',''),(328358,'group','apply_for_a_job','36','allow','',''),(328378,'group','post_job','36','','',''),(328379,'group','post_resume','36','','',''),(328388,'group','apply_for_a_job','41','allow','',''),(328408,'group','post_job','41','','',''),(328409,'group','post_resume','41','','',''),(328451,'guest','apply_for_a_job','guest','allow',NULL,'Please register to Apply'),(328465,'guest','post_job','guest','',NULL,NULL),(328466,'guest','post_resume','guest','',NULL,NULL),(328467,'product','resume_access','12','allow','',''),(328468,'product','apply_for_a_job','12','inherit','hide',''),(328469,'product','post_job','12','deny','',''),(328470,'product','post_resume','12','inherit','',''),(328475,'product','resume_access','13','','',''),(328476,'product','apply_for_a_job','13','inherit','',''),(328477,'product','post_job','13','allow','',''),(328478,'product','post_resume','13','inherit','',''),(328491,'product','resume_access','8','','',''),(328492,'product','apply_for_a_job','8','inherit','',''),(328493,'product','post_job','8','inherit','',''),(328494,'product','post_resume','8','allow','1','');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posting_pages`
--

DROP TABLE IF EXISTS `posting_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posting_pages` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `description` text,
  `listing_type_sid` int(10) DEFAULT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posting_pages`
--

LOCK TABLES `posting_pages` WRITE;
/*!40000 ALTER TABLE `posting_pages` DISABLE KEYS */;
INSERT INTO `posting_pages` VALUES (11,'PostJob','Job','',6,2),(19,'General','Resume','',7,1);
/*!40000 ALTER TABLE `posting_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `detailed_description` text,
  `user_group_sid` int(11) DEFAULT NULL,
  `availability_from` datetime DEFAULT NULL,
  `availability_to` datetime DEFAULT NULL,
  `trial` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `serialized_extra_info` text,
  `order` int(11) NOT NULL DEFAULT '1',
  `number_of_postings` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `name` (`name`),
  KEY `user_group_sid` (`user_group_sid`),
  KEY `availability_from` (`availability_from`),
  KEY `availability_to` (`availability_to`),
  KEY `trial` (`trial`),
  KEY `active` (`active`),
  KEY `order` (`order`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (8,'Free Resume Posting','<p>- Resume Posting for 1 month;</p>\r\n<p>- Efficient Resume Management;</p>\r\n<p>- Upgrade to Priority Resume;</p>\r\n<p>- Many more...</p>',36,NULL,NULL,0,1,'a:9:{s:16:\"listing_type_sid\";s:1:\"7\";s:5:\"price\";s:4:\"0.00\";s:11:\"post_resume\";s:1:\"1\";s:18:\"number_of_listings\";s:1:\"1\";s:16:\"listing_duration\";s:2:\"30\";s:8:\"featured\";s:1:\"0\";s:13:\"resume_access\";s:0:\"\";s:17:\"expiration_period\";s:1:\"0\";s:7:\"default\";N;}',3,1),(13,'Free job posting','',41,NULL,NULL,0,1,'a:10:{s:16:\"listing_type_sid\";s:1:\"6\";s:5:\"price\";s:4:\"0.00\";s:8:\"post_job\";s:1:\"1\";s:18:\"number_of_listings\";s:0:\"\";s:16:\"listing_duration\";s:2:\"30\";s:8:\"featured\";s:1:\"0\";s:17:\"featured_employer\";s:1:\"0\";s:13:\"resume_access\";s:1:\"0\";s:17:\"expiration_period\";s:0:\"\";s:7:\"default\";N;}',2,0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotions` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `discount` float DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'fixed',
  `product_sid` text,
  `maximum_uses` int(11) DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions_history`
--

DROP TABLE IF EXISTS `promotions_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotions_history` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_sid` int(10) unsigned DEFAULT NULL,
  `code_sid` int(10) unsigned DEFAULT NULL,
  `invoice_sid` int(10) unsigned DEFAULT NULL,
  `product_sid` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `code_info` text,
  `amount` float DEFAULT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `user_sid` (`user_sid`,`code_sid`,`invoice_sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions_history`
--

LOCK TABLES `promotions_history` WRITE;
/*!40000 ALTER TABLE `promotions_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refine_search`
--

DROP TABLE IF EXISTS `refine_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refine_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(10) NOT NULL,
  `listing_type_sid` int(10) NOT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  `user_field` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `listing_type_sid` (`listing_type_sid`),
  KEY `order` (`order`),
  KEY `user_field` (`user_field`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refine_search`
--

LOCK TABLES `refine_search` WRITE;
/*!40000 ALTER TABLE `refine_search` DISABLE KEYS */;
INSERT INTO `refine_search` VALUES (6,216,6,2,0),(8,198,7,5,0),(10,216,7,3,0),(11,213,7,7,0),(14,199,6,10,0),(33,198,6,8,0),(38,199,7,6,0);
/*!40000 ALTER TABLE `refine_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relations_listing_fields_posting_pages`
--

DROP TABLE IF EXISTS `relations_listing_fields_posting_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relations_listing_fields_posting_pages` (
  `sid` int(10) NOT NULL AUTO_INCREMENT,
  `field_sid` int(10) NOT NULL,
  `page_sid` int(10) NOT NULL,
  `listing_type_sid` int(10) NOT NULL,
  `order` int(10) NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `field_sid` (`field_sid`),
  KEY `page_sid` (`page_sid`),
  KEY `listing_type_sid` (`listing_type_sid`),
  KEY `order` (`order`)
) ENGINE=MyISAM AUTO_INCREMENT=409 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relations_listing_fields_posting_pages`
--

LOCK TABLES `relations_listing_fields_posting_pages` WRITE;
/*!40000 ALTER TABLE `relations_listing_fields_posting_pages` DISABLE KEYS */;
INSERT INTO `relations_listing_fields_posting_pages` VALUES (43,199,19,7,5),(48,224,19,7,8),(50,367,19,7,4),(143,198,19,7,6),(174,218,19,7,9),(179,198,11,6,7),(180,199,11,6,4),(181,203,11,6,2),(185,197,11,6,1),(191,319,19,7,18),(229,234,11,6,14),(393,359,11,6,9),(394,359,19,7,15),(395,359,25,17,5),(396,359,26,21,5),(397,359,28,22,5),(398,359,29,23,6),(399,359,34,24,5),(400,360,11,6,15),(401,273,19,7,20),(403,274,19,7,19),(405,362,19,7,1),(406,363,19,7,16),(407,364,19,7,14),(408,364,11,6,12);
/*!40000 ALTER TABLE `relations_listing_fields_posting_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` varchar(255) NOT NULL,
  `user_sid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `time` (`time`),
  KEY `user_sid` (`user_sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`sid`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1148 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (12,'task_scheduler_last_executed_date','04.23.2012'),(13,'system_email',''),(15,'i18n_default_language','en'),(16,'i18n_default_domain','Frontend'),(18,'escape_html_tags','htmlpurifier'),(19,'radius_search_unit','miles'),(20,'file_valid_types','doc,docx,png,jpg,gif,xls,xlsx,pdf,avi,wmv,mpg,3gp,flv'),(34,'listing_currency','$'),(35,'transaction_currency','USD'),(36,'default_page_template_by_http','empty.tpl'),(37,'TEMPLATE_USER_THEME','Bootstrap'),(38,'TEMPLATE_ADMIN_THEME','default'),(40,'DEFAULT_PAGE_TEMPLATE','index.tpl'),(50,'htmltags','a:47:{s:1:\"a\";s:0:\"\";s:7:\"address\";s:0:\"\";s:1:\"b\";s:0:\"\";s:2:\"br\";s:0:\"\";s:3:\"big\";s:0:\"\";s:10:\"blockquote\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:4:\"cite\";s:0:\"\";s:4:\"code\";s:0:\"\";s:3:\"del\";s:0:\"\";s:3:\"div\";s:0:\"\";s:2:\"em\";s:0:\"\";s:4:\"font\";s:0:\"\";s:2:\"h1\";s:0:\"\";s:2:\"h2\";s:0:\"\";s:2:\"h3\";s:0:\"\";s:2:\"h4\";s:0:\"\";s:2:\"h5\";s:0:\"\";s:2:\"h6\";s:0:\"\";s:2:\"hr\";s:0:\"\";s:1:\"i\";s:0:\"\";s:3:\"img\";s:0:\"\";s:3:\"ins\";s:0:\"\";s:3:\"kbd\";s:0:\"\";s:2:\"li\";s:0:\"\";s:2:\"ol\";s:0:\"\";s:1:\"p\";s:0:\"\";s:3:\"pre\";s:0:\"\";s:1:\"q\";s:0:\"\";s:4:\"samp\";s:0:\"\";s:5:\"small\";s:0:\"\";s:4:\"span\";s:0:\"\";s:6:\"strike\";s:0:\"\";s:6:\"strong\";s:0:\"\";s:3:\"sub\";s:0:\"\";s:3:\"sup\";s:0:\"\";s:5:\"table\";s:0:\"\";s:5:\"thead\";s:0:\"\";s:5:\"tbody\";s:0:\"\";s:5:\"tfoot\";s:0:\"\";s:2:\"tr\";s:0:\"\";s:2:\"td\";s:0:\"\";s:2:\"th\";s:0:\"\";s:2:\"tt\";s:0:\"\";s:1:\"u\";s:0:\"\";s:2:\"ul\";s:0:\"\";s:3:\"var\";s:0:\"\";}'),(51,'htmlFilter','<a>,<address>,<b>,<br>,<br/>,<br />,<big>,<blockquote>,<caption>,<cite>,<code>,<del>,<div>,<em>,<font>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<hr>,<i>,<img>,<ins>,<kbd>,<li>,<ol>,<p>,<pre>,<q>,<samp>,<small>,<span>,<strike>,<strong>,<sub>,<sup>,<table>,<thead>,<tbody>,<tfoot>,<tr>,<td>,<th>,<tt>,<u>,<ul>,<var>'),(52,'smtp','3'),(53,'smtp_port',''),(54,'smtp_host',''),(55,'smtp_username','admin'),(56,'smtp_password','adminpass'),(57,'smtp_sender',''),(58,'sendmail_path','/usr/sbin/sendmail'),(59,'smtp_security','none'),(61,'site_title','SmartJobBoard'),(67,'action','save_settings'),(75,'turn_on_refine_search_Resume','1'),(76,'turn_on_refine_search_Job','1'),(77,'error_control_mode','debug'),(86,'jobg8_wsdl_url','http://upload.testing.jobg8.com/AdvertUploadWebService.asmx?WSDL'),(90,'applications_jobg8_company_name_filter','0'),(91,'applications_jobg8_company_list','company 1\r\ncompany 2\r\nFusionCompany'),(105,'display_on_resume_page','1'),(106,'display_on_job_page','1'),(108,'maintenance_mode','0'),(109,'maintenance_mode_ip',''),(110,'count_listings','3'),(113,'IndeedPublisherID','6053709130975284'),(117,'refine_search_items_limit','20'),(119,'automatically_delete_expired_listings','1'),(120,'period_delete_expired_listings','10'),(121,'captcha_type','kCaptcha'),(142,'IndeedSiteType',''),(143,'IndeedJobType',''),(146,'lType',''),(147,'li_apiKey','77oi743bav9ehl'),(148,'li_secKey','UTUREXr0moeJ2RJc'),(151,'li_companyWidget','1'),(152,'li_resumeWidget','1'),(156,'fb_appID','111597540863'),(157,'fb_appSecret','336f1f6b99f669506072067edafaa92a'),(160,'fb_shareJob','1'),(161,'fb_shareResume','1'),(165,'display_for_all_pages','1'),(171,'Keywords','IS_EMPTY'),(186,'submit','save'),(187,'page','#generalTab'),(192,'numberOfPageViewsToExecCronIfExceeded','50'),(196,'number_emails','2'),(197,'time_sending_emails','1313606116'),(198,'send_emails','2'),(202,'username','admin'),(203,'password','admin'),(209,'applications_jobg8_product_filter','0'),(210,'applications_jobg8_product_list',''),(211,'applications_jobg8_job_category_filter','0'),(214,'IndeedCountry','us'),(318,'enable_promotion_codes','1'),(355,'num_of_listings_sent_in_email_alerts','20'),(356,'mc_apikey',''),(357,'mc_listId',''),(698,'enableCache','0'),(700,'cacheHours','2'),(701,'cacheMinutes','0'),(741,'tax',''),(752,'profiler','0'),(758,'google_TrackingID',''),(773,'get_keyword_from_file','0'),(955,'send_payment_to','SmartJobBoard'),(988,'task_scheduler_last_executed_time_hourly',NULL),(989,'task_scheduler_last_executed_time_daily',NULL),(999,'captcha_max_allowed_auth_attempts','5'),(1041,'soc_network','googleplus'),(1042,'oauth2_client_id','592720396096-4goidq0sl75nn71kdk6q8e07dd99mrfp.apps.googleusercontent.com'),(1043,'client_secret','6V-lIiD4aZ1x3NrB8QUMXRsc'),(1044,'developer_key','AIzaSyApFCCJfCe-B8LdC3xVMrpYhHoSqqEbdfY'),(1046,'passed_parameters_via_uri','googleplus'),(1051,'timezone','America/New_York'),(1069,'jobG8BuyApplicationsStatus','1'),(1075,'facebookAppPages','a:0:{};'),(1078,'domain',''),(1079,'signup_employer_linkedin','0'),(1080,'signup_employer_facebook','0'),(1081,'signup_employer_googleplus','0'),(1082,'public_resume_access','1'),(1083,'signup_jobseeker_linkedin','0'),(1084,'signup_jobseeker_facebook','0'),(1085,'signup_jobseeker_googleplus','0'),(1086,'home_page_title',''),(1087,'home_page_description',''),(1088,'search_by_radius','1'),(1090,'location_limit',''),(1091,'google_api_key','AIzaSyA_O3Q44rnb58PetTaYnx65CMftFND0qls'),(1092,'search_by_location','1'),(1101,'theme_font_Bootstrap','\"Fira Sans\", sans-serif'),(1102,'theme_logo_Bootstrap','logo.svg'),(1103,'theme_favicon_Bootstrap','favicon.ico'),(1104,'theme_main_banner_text_Bootstrap','<h1>Search <span style=\"color:#FFFFFF\">$listings_types.Job</span> live jobs</h1>\r\n<p>Finding your new job just got easier</p>'),(1105,'theme_secondary_banner_text_Bootstrap','<h3>POST YOUR JOB TODAY</h3>\r\n<div>Job seekers will be able to find<br />your first-class job</div><a class=\"btn btn__yellow btn__bold btn-post-job\" href=\"$GLOBALS.site_url/add-listing/?listing_type_id=Job\">Post a Job</a>'),(1111,'theme_custom_css_Bootstrap',''),(1112,'theme_custom_js_Bootstrap',''),(1113,'theme_footer_Bootstrap','<div><div><ul><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/\">Home</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/contact/\">Contact</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/about/\">About Us </a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/terms-of-use/\">Terms &amp; Conditions</a></li></ul></div>\r\n<div><ul><li>Employer</li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/add-listing/?listing_type_id=Job\">Post a Job</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/resumes/\">Search Resumes</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/login/\">Sign in</a></li></ul></div>\r\n<div><ul><li>Job Seeker</li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/jobs/\">Find Jobs</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/add-listing/?listing_type_id=Resume\">Create Resume</a></li><li><a class=\"footer-nav__link\" href=\"$GLOBALS.site_url/login/\">Sign in</a></li></ul></div>\r\n<div><ul><li><a class=\"footer-nav__link footer-nav__link-social footer-nav__link-facebook\" href=\"\">Facebook</a></li><li><a class=\"footer-nav__link footer-nav__link-social footer-nav__link-twitter\" href=\"#\">Twitter</a></li><li><a class=\"footer-nav__link footer-nav__link-social footer-nav__link-plus\" href=\"#\">Google Plus</a></li><li><a class=\"footer-nav__link footer-nav__link-social footer-nav__link-in\" href=\"#\">LinkedIn</a></li></ul></div></div>\r\n<div>&copy; 2008-$current_year Powered by <a href=\"http://www.smartjobboard.com\" target=\"_blank\" title=\"Job Board Software, Script\">SmartJobBoard Job Board Software</a></div>'),(1114,'theme_jobs_by_category_Bootstrap','1'),(1115,'theme_jobs_by_city_Bootstrap','1'),(1116,'theme_jobs_by_country_Bootstrap','0'),(1117,'theme_jobs_by_state_Bootstrap','1'),(1118,'theme_bottom_section_html_Bootstrap','<h3>Sign up for job alerts</h3>\r\n<div>Get job alerts straight to your inbox.</div>\r\n<div>Enter your email to get started. You will be able to unsubscribe at any moment.</div>'),(1120,'theme_button_color_1_Bootstrap','#419dd4'),(1121,'theme_button_color_2_Bootstrap','#ff6a50'),(1122,'theme_button_color_3_Bootstrap','#ffe15e'),(1124,'theme_main_banner_Bootstrap','top-banner.png'),(1125,'theme_secondary_banner_Bootstrap','post-a-job.svg'),(1128,'home_page_keywords',''),(1129,'db-patch-google-place-full-text','patched'),(1130,'db-patch-patch-apply_click','patched'),(1131,'db-patch-youtube-facebook-phrases','patched'),(1132,'db-patch-sl-76-more-results','patched'),(1133,'db-patch-locations-service','patched'),(1134,'db-patch-sl-96-themes','patched'),(1135,'db-patch-remove-add-listing-type','patched'),(1136,'IndeedKeywords',''),(1137,'IndeedLocation','Los Angeles'),(1138,'IndeedRadius',''),(1139,'db-patch-SL-49','patched'),(1140,'db-patch-SL-122','patched'),(1141,'db-patch-SL-100','patched'),(1142,'date_format','%b %d, %Y'),(1143,'db-patch-SL-120','patched'),(1144,'db-patch-SL-129','patched'),(1145,'db-patch-SL-119','patched'),(1146,'db-patch-SL-138','patched'),(1147,'db-patch-5.0.3','patched');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopping_cart`
--

DROP TABLE IF EXISTS `shopping_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopping_cart` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_sid` int(11) DEFAULT NULL,
  `product_info` text,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopping_cart`
--

LOCK TABLES `shopping_cart` WRITE;
/*!40000 ALTER TABLE `shopping_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `shopping_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_scheduler_log`
--

DROP TABLE IF EXISTS `task_scheduler_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_scheduler_log` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `last_executed_date` datetime NOT NULL,
  `notifieds_sent` int(11) NOT NULL,
  `expired_listings` int(11) NOT NULL,
  `expired_contracts` int(11) NOT NULL,
  `log_text` text NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `last_executed_date` (`last_executed_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_scheduler_log`
--

LOCK TABLES `task_scheduler_log` WRITE;
/*!40000 ALTER TABLE `task_scheduler_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_scheduler_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(255) NOT NULL DEFAULT '',
  `user_sid` int(10) DEFAULT NULL,
  `invoice_sid` int(10) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `description` text,
  `date` date DEFAULT NULL,
  `amount` double DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploaded_files`
--

DROP TABLE IF EXISTS `uploaded_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaded_files` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_group` varchar(255) DEFAULT NULL,
  `saved_file_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `creation_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaded_files`
--

LOCK TABLES `uploaded_files` WRITE;
/*!40000 ALTER TABLE `uploaded_files` DISABLE KEYS */;
INSERT INTO `uploaded_files` VALUES (7,'Logo_6','irg.jpg','pictures','irg_1.jpg','image/jpeg',NULL),(8,'Logo_6_thumb','irg.jpg','pictures','irg_1_thumb.jpg','image/jpeg',NULL),(9,'Logo_4','chilisoft.jpg','pictures','chilisoft_1.jpg','image/jpeg',NULL),(10,'Logo_4_thumb','chilisoft.jpg','pictures','chilisoft_1_thumb.jpg','image/jpeg',NULL),(11,'Logo_5','inventa.jpg','pictures','inventa_1.jpg','image/jpeg',NULL),(12,'Logo_5_thumb','inventa.jpg','pictures','inventa_1_thumb.jpg','image/jpeg',NULL),(13,'Logo_3','Jardini logo_1.jpg','pictures','Jardini logo_1_1.jpg','image/jpeg',NULL),(14,'Logo_3_thumb','Jardini logo_1.jpg','pictures','Jardini logo_1_1_thumb.jpg','image/jpeg',NULL),(17,'Logo_8','logo_emp.jpg','pictures','logo_emp_1.jpg','image/jpeg',NULL),(18,'Logo_8_thumb','logo_emp.jpg','pictures','logo_emp_1_thumb.jpg','image/jpeg',NULL),(21,'Resume_1','test_resume_4.docx','files','test_resume_4_1.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(22,'Resume_3','test_resume_4.docx','files','test_resume_4_2.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(23,'Resume_4','test_resume_4.docx','files','test_resume_4_3.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(25,'Resume_7','test_resume_4.docx','files','test_resume_4_4.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(41,'Resume_15','test_resume_4_2.docx','files','test_resume_4_2_1.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(43,'application_0e277fdc866ebfdc849b68125c9defe9','test_resume_4_2.docx','files','test_resume_4_2_3.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',NULL),(54,'Photo_15','tumblr_np40qa7UyM1qex968o1_500.jpg','pictures','tumblr_np40qa7UyM1qex968o1_500_1.png','image/jpeg','1453974284');
/*!40000 ALTER TABLE `uploaded_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(255) DEFAULT NULL,
  `default_product` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `ufi` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (36,'JobSeeker',8,'Job Seeker'),(41,'Employer',13,'Employer');
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups_properties`
--

DROP TABLE IF EXISTS `user_groups_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups_properties` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_sid` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `value` text,
  `add_parameter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `object_sid` (`object_sid`,`id`),
  KEY `id` (`id`),
  KEY `add_parameter` (`add_parameter`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups_properties`
--

LOCK TABLES `user_groups_properties` WRITE;
/*!40000 ALTER TABLE `user_groups_properties` DISABLE KEYS */;
INSERT INTO `user_groups_properties` VALUES (103,36,'notify_on_contract_expiration','22',''),(104,36,'notify_on_listing_activation','13',''),(105,36,'notify_on_listing_expiration','18',''),(107,36,'notify_subscription_activation','24',''),(118,36,'welcome_email','49',''),(124,41,'notify_on_contract_expiration','22',''),(125,41,'notify_on_listing_activation','14',''),(126,41,'notify_on_listing_expiration','17',''),(128,41,'notify_subscription_activation','24',''),(139,41,'welcome_email','48','');
/*!40000 ALTER TABLE `user_groups_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profile_fields`
--

DROP TABLE IF EXISTS `user_profile_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profile_fields` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_group_sid` int(10) unsigned DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `instructions` text,
  `maxlength` int(10) DEFAULT NULL,
  `width` int(5) DEFAULT NULL,
  `height` int(5) DEFAULT NULL,
  `second_width` int(5) DEFAULT NULL,
  `second_height` int(5) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `level_1` varchar(255) DEFAULT NULL,
  `level_2` varchar(255) DEFAULT NULL,
  `level_3` varchar(255) DEFAULT NULL,
  `level_4` varchar(255) DEFAULT NULL,
  `display_as_select_boxes` tinyint(1) NOT NULL DEFAULT '0',
  `parent_sid` int(10) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `display_as` varchar(255) DEFAULT NULL,
  `choiceLimit` int(11) DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `user_group_sid` (`user_group_sid`),
  KEY `order` (`order`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profile_fields`
--

LOCK TABLES `user_profile_fields` WRITE;
/*!40000 ALTER TABLE `user_profile_fields` DISABLE KEYS */;
INSERT INTO `user_profile_fields` VALUES (58,41,1,'CompanyName','Company Name','string','',1,'',256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(59,41,2,'FullName','Full Name','string',NULL,0,'',256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(60,41,3,'WebSite','Website','string',NULL,0,'',256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(61,41,5,'Country','Country','string','',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,'country_name',0),(62,41,6,'State','State','string',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,'state_name',0),(63,41,7,'City','City','string',NULL,0,NULL,256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,NULL,0),(67,41,8,'CompanyDescription','Company Description','text',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'text.tpl',NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(81,41,0,'Logo','Logo','logo',NULL,0,NULL,NULL,250,250,150,150,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(116,41,10,'ZipCode','Zip Code','string','',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,NULL,0),(119,41,4,'Location','Location','location',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(120,41,11,'Latitude','Latitude','string','',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,NULL,0),(121,41,12,'Longitude','Longitude','string','',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,119,0,NULL,0),(122,41,4,'GooglePlace','Location','google_place',NULL,0,'',256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0),(123,36,0,'FullName','Full Name','string',NULL,0,'',256,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,0);
/*!40000 ALTER TABLE `user_profile_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_session_data_storage`
--

DROP TABLE IF EXISTS `user_session_data_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_session_data_storage` (
  `user_sid` int(10) unsigned DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `last_activity` datetime NOT NULL,
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_sid` (`user_sid`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_session_data_storage`
--

LOCK TABLES `user_session_data_storage` WRITE;
/*!40000 ALTER TABLE `user_session_data_storage` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_session_data_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
  `session_key` varchar(32) NOT NULL DEFAULT '',
  `user_sid` int(11) NOT NULL DEFAULT '0',
  `remote_ip` varchar(32) NOT NULL DEFAULT '',
  `user_agent` varchar(255) NOT NULL DEFAULT '',
  `start` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_key`),
  KEY `user_sid` (`user_sid`),
  KEY `remote_ip` (`remote_ip`),
  KEY `start` (`start`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_group_sid` int(10) unsigned DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `active` int(1) unsigned DEFAULT '0',
  `verification_key` varchar(255) DEFAULT NULL,
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `reference_uid` varchar(255) DEFAULT NULL,
  `Location_Country` text,
  `Location_State` text,
  `Location_City` varchar(255) DEFAULT NULL,
  `CompanyName` varchar(255) DEFAULT NULL,
  `FullName` varchar(255) DEFAULT NULL,
  `WebSite` varchar(255) DEFAULT NULL,
  `CompanyDescription` longtext,
  `Logo` varchar(255) DEFAULT NULL,
  `trial` text,
  `Location_ZipCode` varchar(255) DEFAULT NULL,
  `Location_Latitude` double DEFAULT NULL,
  `Location_Longitude` double DEFAULT NULL,
  `GooglePlace` varchar(255) DEFAULT NULL,
  `Location` text,
  `extUserID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `user_group_sid` (`user_group_sid`),
  KEY `active` (`active`),
  KEY `featured` (`featured`),
  KEY `password` (`password`),
  KEY `registration_date` (`registration_date`),
  KEY `verification_key` (`verification_key`),
  KEY `ip` (`ip`),
  KEY `reference_uid` (`reference_uid`),
  KEY `extUserID` (`extUserID`),
  KEY `CompanyName` (`CompanyName`),
  KEY `FullName` (`FullName`),
  KEY `Location_Latitude` (`Location_Latitude`),
  KEY `Location_Longitude` (`Location_Longitude`),
  KEY `GooglePlace` (`GooglePlace`),
  FULLTEXT KEY `GooglePlaceF` (`GooglePlace`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'js@test.com','32981a13284db7a021131df49e6cd203','js@mail.com',36,'2012-04-24 11:59:33',1,'i83qfjnawldgkzro12yxv5emp4uhcbt9760s',0,'127.0.0.1',NULL,'227','7','Durango',NULL,'John Smith [Resume Sample]',NULL,NULL,NULL,'10','81303',NULL,NULL,NULL,NULL,NULL),(3,'jardini@test.com','2f11cb292d739442ee9895cc084ed187','employer@gmail.com',41,'2012-04-24 12:00:09',1,'ohk8bxrg3qwmc20jvn9u4tis7dz5yafe1l6p',1,'192.168.0.50',NULL,'United States','Massachusetts','Cambridge','Jardini Sample Employer','John Brown','testurl.com','<p>Jardini Company is one of the largest companies in the construction and real estate industry.</p>','Logo_3',NULL,'02142',42.36360168457031,-71.0824966430664,'Cambridge, MA 02142, United States','United States Massachusetts Cambridge 02142 42.36360168457031 -71.0824966430664',NULL),(4,'chilisoft@test.com','d3171941cd84c6e462bf4969796aedd1','cv.jf@hotmail.com',41,'2012-04-24 12:00:09',1,'cadh2zs80twrpqbfoku1jl735en4iv9gy6mx',1,'192.168.0.50',NULL,'United States','California','San Francisco','Chilisoft Sample Employer','Michael Huston','testurl.com','<p>As a distributor we focus on security solutions and carefully select leading or emerging products from reliable and reputable vendors that can benefit our resellers and and-user clients in our target markets. We work to ensure that our staff are well t</p>','Logo_4',NULL,'94103',37.77259826660156,-122.41000366210938,'San Francisco, CA 94103, United States','United States California San_ Francisco 94103 37.77259826660156 -122.41000366210938',NULL),(5,'inventa@test.com','020bedb3ad0a7431baa53c0d6b078cff','mail@box.com',41,'2012-04-24 12:00:09',1,'d2lopfrqm3607w415yu8ibjc9egsavznxtkh',1,'192.168.0.50',NULL,'United States','Georgia','Atlanta','Inventa Sample Employer','Silvia','testurl.com','<p>Our company gained wide experience in co-operation with different organizations and companies. Today we&#39;re one of the best companies working on intellectual property market. Our company has already fulfilled works concerning protection of more than 400</p>','Logo_5',NULL,'30307',33.77239990234375,-84.3279037475586,'Atlanta, GA 30307, United States','United States Georgia Atlanta 30307 33.77239990234375 -84.3279037475586',NULL),(6,'irg@test.com','dc2e69864af36f7ff06e82d5f9919dba','cv@irg.org',41,'2012-04-24 12:00:09',1,'t8befckq541oypdsun9ijlm2w6a0h7zv3grx',1,'192.168.0.50',NULL,'United States','Connecticut','Hartford','IRG Sample Employer','Steve Martin','testurl.com','<p>IRG is the Resource Company that provides development-related services for private- and public-sector clients. Our company offers a wide range of environmental services.</p>','Logo_6',NULL,'06141',41.7599983215332,-72.69000244140625,'Hartford, CT 06141, United States','United States Connecticut Hartford 06141 41.7599983215332 -72.69000244140625',NULL),(8,'test@test.com','202cb962ac59075b964b07152d234b70','nwyksasdf@gmail.com',41,'2012-04-24 12:00:09',1,'ldjmnokuch4qvf917eix6tyzw3b8p0rga52s',0,'192.168.0.31',NULL,'United States','California','Sacramento','Sample Employer','Robert White','testurl.com','<p><strong>This is not a real company. Please don&#39;t try to contact us. </strong><br /><br />We&#39;re one of the leading management consulting firms in United States. We provide a complete consulting expertise. We have a wide range of products, processes and services to support your company&#39;s business needs.</p>','Logo_8','7','',38.58157189999999,-121.49439960000001,'Sacramento, CA, United States','United States California Sacramento 38.58157189999999 -121.49439960000001',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_properties`
--

DROP TABLE IF EXISTS `users_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_properties` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_sid` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `value` text,
  `add_parameter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `object_sid` (`object_sid`),
  KEY `id` (`id`),
  KEY `add_parameter` (`add_parameter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_properties`
--

LOCK TABLES `users_properties` WRITE;
/*!40000 ALTER TABLE `users_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_properties` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-04-22 12:49:17
