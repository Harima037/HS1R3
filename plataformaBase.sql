CREATE DATABASE  IF NOT EXISTS `plataformaBase` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `plataformaBase`;
-- MySQL dump 10.13  Distrib 5.6.11, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: quick
-- ------------------------------------------------------
-- Server version	5.6.16

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
-- Table structure for table `sentrygroups`
--

DROP TABLE IF EXISTS `sentryGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sentryGroups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `creadoAl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificadoAl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sentrygroups`
--

LOCK TABLES `sentryGroups` WRITE;
/*!40000 ALTER TABLE `sentryGroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `sentryGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sentrythrottle`
--

DROP TABLE IF EXISTS `sentryThrottle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sentryThrottle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `throttle_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sentrythrottle`
--

LOCK TABLES `sentryThrottle` WRITE;
/*!40000 ALTER TABLE `sentryThrottle` DISABLE KEYS */;
/*!40000 ALTER TABLE `sentryThrottle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sentryusers`
--

DROP TABLE IF EXISTS `sentryUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sentryUsers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `persist_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apellidoPaterno` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apellidoMaterno` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creadoAl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificadoAl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `borradoAl` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `users_activation_code_index` (`activation_code`),
  KEY `users_reset_password_code_index` (`reset_password_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sentryusers`
--

LOCK TABLES `sentryUsers` WRITE;
/*!40000 ALTER TABLE `sentryUsers` DISABLE KEYS */;
INSERT INTO `sentryUsers` VALUES (1,'root','root@localhost','$2y$10$LI0.G8uwOtDKytIR5Y5gLeUNWczPClly8Bt8E9FHU9RSHwMsvO3y6','{\"superuser\":1}',1,NULL,NULL,'2014-10-08 21:25:59','$2y$10$U0WMtgei9hjEr5igtUtTRuuvUn9AZHW.c4l2wQJdQaeQOLDhvEZV.',NULL,'root','super','usuario','','','2014-06-13 18:53:24','2014-10-08 21:25:59',NULL);
/*!40000 ALTER TABLE `sentryUsers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sentryusersgroups`
--

DROP TABLE IF EXISTS `sentryUsersGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sentryUsersGroups` (
  `sentry_user_id` int(10) unsigned NOT NULL,
  `sentry_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sentry_user_id`,`sentry_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sentryusersgroups`
--

LOCK TABLES `sentryUsersGroups` WRITE;
/*!40000 ALTER TABLE `sentryUsersGroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `sentryUsersGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sysbitacora`
--

DROP TABLE IF EXISTS `sysBitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysBitacora` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idUsuario` int(10) unsigned DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `tipo` varchar(6) DEFAULT NULL,
  `controlador` varchar(45) NOT NULL,
  `idRecurso` varchar(45) DEFAULT NULL,
  `info` text,
  `creadoAl` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sysbitacora`
--

LOCK TABLES `sysBitacora` WRITE;
/*!40000 ALTER TABLE `sysBitacora` DISABLE KEYS */;
/*!40000 ALTER TABLE `sysBitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sysconfiguracionvariables`
--

DROP TABLE IF EXISTS `sysConfiguracionVariables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysConfiguracionVariables` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `variable` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `creadoAl` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificadoAl` timestamp NULL DEFAULT NULL,
  `borradoAl` timestamp NULL DEFAULT NULL,
  `creadoPor` int(10) DEFAULT NULL,
  `actualizadoPor` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sysconfiguracionvariables`
--

LOCK TABLES `sysConfiguracionVariables` WRITE;
/*!40000 ALTER TABLE `sysConfiguracionVariables` DISABLE KEYS */;
/*!40000 ALTER TABLE `sysConfiguracionVariables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sysgruposmodulos`
--

DROP TABLE IF EXISTS `sysGruposModulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysGruposModulos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_UNIQUE` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sysgruposmodulos`
--

LOCK TABLES `sysGruposModulos` WRITE;
/*!40000 ALTER TABLE `sysGruposModulos` DISABLE KEYS */;
INSERT INTO `sysGruposModulos` VALUES (1,'ADMIN','Administrador','administrador','fa-cog',1),(2,'DASHBOARD','Dashboard','dashboard','fa-dashboard',0);
/*!40000 ALTER TABLE `sysGruposModulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sysmodulos`
--

DROP TABLE IF EXISTS `sysModulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysModulos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idSysGrupoModulo` int(10) unsigned NOT NULL,
  `idSysPermiso` int(11) NOT NULL,
  `key` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_UNIQUE` (`key`),
  KEY `sysModulos_sysGruposModulos_foreign_idx` (`idSysGrupoModulo`),
  KEY `sysModulos_sysPermisos_foreign_idx` (`idSysPermiso`),
  CONSTRAINT `sysModulos_sysGruposModulos_foreign` FOREIGN KEY (`idSysGrupoModulo`) REFERENCES `sysgruposmodulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sysmodulos`
--

LOCK TABLES `sysModulos` WRITE;
/*!40000 ALTER TABLE `sysModulos` DISABLE KEYS */;
INSERT INTO `sysModulos` VALUES (1,1,1,'USUARIOS','Usuarios','usuarios','fa-user',1),(2,1,1,'ROLES','Roles','roles','fa-group',1),(3,1,2,'PERMISOS','Permisos','permisos','fa-unlock',0);
/*!40000 ALTER TABLE `sysModulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `syspermisos`
--

DROP TABLE IF EXISTS `sysPermisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysPermisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `permisos` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `syspermisos`
--

LOCK TABLES `sysPermisos` WRITE;
/*!40000 ALTER TABLE `sysPermisos` DISABLE KEYS */;
INSERT INTO `sysPermisos` VALUES (1,'CRUD','C|R|U|D'),(2,'Reporte','R');
/*!40000 ALTER TABLE `sysPermisos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-10-10 14:31:20
