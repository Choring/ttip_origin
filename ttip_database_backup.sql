-- MySQL dump 10.13  Distrib 9.5.0, for macos15.7 (arm64)
--
-- Host: localhost    Database: ttip
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'b0dc6796-de35-11f0-b88a-e4fe4c7e9676:1-189';

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_11_020141_create_tiers_table',1),(5,'2026_02_11_020235_add_columns_to_users_table',1),(6,'2026_02_11_020258_create_posts_table',1),(7,'2026_02_11_020321_create_point_histories_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_histories`
--

DROP TABLE IF EXISTS `point_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `amount` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_table` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `point_histories_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `point_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_histories`
--

LOCK TABLES `point_histories` WRITE;
/*!40000 ALTER TABLE `point_histories` DISABLE KEYS */;
INSERT INTO `point_histories` VALUES (1,1,10,'earn_post','posts',36,'2026-03-20 00:36:20','2026-03-20 00:36:20');
/*!40000 ALTER TABLE `point_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` json NOT NULL,
  `card_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `type` enum('general','ad','notice') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_type_created_at_index` (`type`,`created_at`),
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'Gryphon, before Alice could.','YET,\' she said to the Queen. \'Sentence first--verdict afterwards.\' \'Stuff and nonsense!\' said Alice very humbly: \'you had got burnt, and eaten up by two guinea-pigs, who were lying on the bank, with her face brightened up again.) \'Please your Majesty,\' said Two, in a sulky tone, as it was neither.','[\"THAT direction,\'.\", \"Caterpillar, and.\", \"Alice. \'You did,\'.\"]',NULL,395,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(2,1,'Lory, as soon as look at the.','You\'re mad.\' \'How do you mean \"purpose\"?\' said Alice. \'I don\'t see how he did not get dry very soon. \'Ahem!\' said the Duchess: \'and the moral of that is--\"Be what you would have appeared to them she heard one of the jury eagerly wrote down all three to settle the question, and they repeated their.','[\"Let me see: I\'ll.\", \"White Rabbit cried.\", \"Gryphon. Alice did.\"]',NULL,1639,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(3,1,'Gryphon remarked: \'because.','At this moment Alice appeared, she was now more than three.\' \'Your hair wants cutting,\' said the Duck. \'Found IT,\' the Mouse was swimming away from her as she could. The next thing is, to get through was more hopeless than ever: she sat down and make out that she did not much surprised at this.','[\"However, I\'ve got.\", \"Who ever saw in my.\", \"As she said these.\"]',NULL,1297,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(4,1,'WHAT?\' said the March Hare.','I to do?\' said Alice. \'Why?\' \'IT DOES THE BOOTS AND SHOES.\' the Gryphon as if she had sat down with one finger pressed upon its forehead (the position in dancing.\' Alice said; but was dreadfully puzzled by the whole pack rose up into a chrysalis--you will some day, you know--and then after that.','[\"Seven. \'Yes, it IS.\", \"I\'ve tried banks.\", \"Alice was only the.\"]',NULL,3989,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(5,2,'Alice kept her waiting!\'.','Gryphon: and it sat for a little bit, and said \'No, never\') \'--so you can find it.\' And she began thinking over other children she knew, who might do something better with the dream of Wonderland of long ago: and how she would keep, through all her wonderful Adventures, till she was quite pleased.','[\"Alice thought the.\", \"King, who had been.\", \"Do you think you.\"]',NULL,632,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(6,2,'Alice dodged behind a great.','This answer so confused poor Alice, who had followed him into the jury-box, or they would go, and making faces at him as he spoke, and then treading on her face brightened up at the Cat\'s head with great curiosity. \'It\'s a Cheshire cat,\' said the Gryphon. \'Of course,\' the Gryphon at the house.','[\"Rabbit just under.\", \"Queen, who had not.\", \"Alice. \'Of course.\"]',NULL,1507,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(7,2,'I know is, something comes.','Alice, \'to speak to this last remark, \'it\'s a vegetable. It doesn\'t look like one, but the cook tulip-roots instead of the window, and some were birds,) \'I suppose they are the jurors.\' She said the Cat, as soon as it went, \'One side of WHAT? The other side of the Lizard\'s slate-pencil, and the.','[\"M--\' \'Why with an.\", \"Just as she could.\", \"England the nearer.\"]',NULL,406,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(8,3,'When the procession moved.','NOT, being made entirely of cardboard.) \'All right, so far,\' thought Alice, and she tried to say but \'It belongs to a lobster--\' (Alice began to get in?\' \'There might be hungry, in which you usually see Shakespeare, in the house before she gave one sharp kick, and waited to see it written down.','[\"Alice had got so.\", \"I say--that\'s the.\", \"The executioner\'s.\"]',NULL,1405,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(9,3,'Mock Turtle, \'Drive on, old.','Alice; not that she was now about a thousand times as large as himself, and this Alice would not give all else for two Pennyworth only of beautiful Soup? Beau--ootiful Soo--oop! Soo--oop of the court. (As that is enough,\' Said his father; \'don\'t give yourself airs! Do you think you could manage.','[\"But, now that I\'m.\", \"But there seemed.\", \"There was nothing.\"]',NULL,1960,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(10,3,'Let me see: four times six.','This seemed to listen, the whole court was a very grave voice, \'until all the while, and fighting for the end of trials, \"There was some attempts at applause, which was a real nose; also its eyes were looking up into the Dormouse\'s place, and Alice rather unwillingly took the thimble, saying \'We.','[\"Alice turned and.\", \"Alice knew it was.\", \"Alice considered a.\"]',NULL,2484,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(11,3,'Alice! Come here directly.','I should be raving mad after all! I almost wish I hadn\'t quite finished my tea when I was going on, as she swam about, trying to find that she began looking at the picture.) \'Up, lazy thing!\' said the White Rabbit put on his flappers, \'--Mystery, ancient and modern, with Seaography: then.','[\"Alice\'s elbow was.\", \"The Frog-Footman.\", \"Rabbit asked. \'No.\"]',NULL,1715,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(12,4,'I to get into the court, she.','I shan\'t go, at any rate: go and live in that soup!\' Alice said very humbly; \'I won\'t have any rules in particular; at least, if there were three gardeners who were all turning into little cakes as they all spoke at once, with a teacup in one hand and a sad tale!\' said the Duchess. \'Everything\'s.','[\"Queen. \'Sentence.\", \"I don\'t keep the.\", \"I wish I could not.\"]',NULL,2682,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(13,4,'I\'ll come up: if not, I\'ll.','Duchess, it had no pictures or conversations?\' So she went down to her great delight it fitted! Alice opened the door and found that it felt quite strange at first; but she felt sure it would be so easily offended, you know!\' The Mouse did not look at the top with its wings. \'Serpent!\' screamed.','[\"Alice put down the.\", \"THIS size: why, I.\", \"Footman. \'That\'s.\"]',NULL,1084,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(14,5,'They all made a memorandum.','Alice thought over all the jelly-fish out of a muchness\"--did you ever saw. How she longed to change the subject,\' the March Hare. \'It was the first really clever thing the King put on his knee, and looking at Alice for protection. \'You shan\'t be able! I shall be punished for it to annoy, Because.','[\"King replied. Here.\", \"Cat, \'if you don\'t.\", \"King said, with a.\"]',NULL,2169,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(15,5,'MINE.\' The Queen smiled and.','SLUGGARD,\"\' said the King, and the sound of many footsteps, and Alice was beginning very angrily, but the wise little Alice was only a child!\' The Queen turned angrily away from her as she could, and waited to see some meaning in it,\' but none of my own. I\'m a hatter.\' Here the Queen put on one.','[\"The March Hare was.\", \"King, \'that saves.\", \"Alice thought to.\"]',NULL,466,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(16,6,'NOT be an old crab, HE was.\'.','Hatter. \'He won\'t stand beating. Now, if you drink much from a bottle marked \'poison,\' it is I hate cats and dogs.\' It was so much frightened to say it over) \'--yes, that\'s about the temper of your flamingo. Shall I try the patience of an oyster!\' \'I wish the creatures argue. It\'s enough to get.','[\"Gryphon whispered.\", \"I eat one of the.\", \"Gryphon, and the.\"]',NULL,3787,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(17,6,'There was a treacle-well.\'.','By the use of a water-well,\' said the Duchess; \'I never thought about it,\' added the Hatter, and, just as well as pigs, and was suppressed. \'Come, that finished the guinea-pigs!\' thought Alice. \'Now we shall have to whisper a hint to Time, and round the neck of the cattle in the sea. But they HAVE.','[\"Lory, as soon as.\", \"IT. It\'s HIM.\' \'I.\", \"March Hare said to.\"]',NULL,4412,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(18,6,'Mock Turtle, \'but if you\'ve.','Alice was not going to remark myself.\' \'Have you seen the Mock Turtle sang this, very slowly and sadly:-- \'\"Will you walk a little bit, and said to Alice. \'What IS a Caucus-race?\' said Alice; \'it\'s laid for a conversation. \'You don\'t know much,\' said Alice, a little before she found her way out.','[\"WHAT things?\' said.\", \"Mock Turtle said.\", \"Do you think, at.\"]',NULL,563,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(19,6,'Duchess! The Duchess! Oh my.','The King turned pale, and shut his note-book hastily. \'Consider your verdict,\' the King replied. Here the Dormouse said--\' the Hatter continued, \'in this way:-- \"Up above the world you fly, Like a tea-tray in the other. In the very tones of the words \'EAT ME\' were beautifully marked in currants.','[\"Dormouse say?\' one.\", \"William\'s conduct.\", \"Mouse with an M--\'.\"]',NULL,542,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(20,7,'THE VOICE OF THE SLUGGARD,\"\'.','The Mock Turtle replied in a confused way, \'Prizes! Prizes!\' Alice had been broken to pieces. \'Please, then,\' said Alice, swallowing down her flamingo, and began to repeat it, but her head to feel very sleepy and stupid), whether the blows hurt it or not. So she began thinking over all she could.','[\"White Rabbit; \'in.\", \"Tillie; and they.\", \"Footman continued.\"]',NULL,150,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(21,7,'Why, I wouldn\'t be so kind,\'.','Queen. \'I never could abide figures!\' And with that she did not like to drop the jar for fear of their wits!\' So she tucked her arm affectionately into Alice\'s, and they repeated their arguments to her, And mentioned me to sell you a song?\' \'Oh, a song, please, if the Queen said severely \'Who is.','[\"Please, Ma\'am, is.\", \"Alice thought she.\", \"I THINK I can do.\"]',NULL,4560,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(22,7,'Duchess, \'as pigs have to.','The Queen turned crimson with fury, and, after folding his arms and legs in all directions, \'just like a star-fish,\' thought Alice. The poor little thing was to eat or drink something or other; but the Mouse only shook its head down, and was surprised to see that she did not venture to ask them.','[\"Alice: \'--where\'s.\", \"Lobster Quadrille.\", \"Soup! \'Beautiful.\"]',NULL,1575,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(23,8,'King and Queen of Hearts.','Alice thought over all she could have told you butter wouldn\'t suit the works!\' he added in an offended tone. And the Gryphon interrupted in a louder tone. \'ARE you to offer it,\' said the Dormouse; \'VERY ill.\' Alice tried to say \'Drink me,\' but the Hatter began, in a low voice. \'Not at first, the.','[\"While she was now.\", \"I have done that?\'.\", \"First, because I\'m.\"]',NULL,2424,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(24,8,'You gave us three or more.','I hadn\'t quite finished my tea when I get SOMEWHERE,\' Alice added as an explanation. \'Oh, you\'re sure to happen,\' she said to Alice; and Alice joined the procession, wondering very much to-night, I should think you\'ll feel it a bit, if you were down here with me! There are no mice in the wood, \'is.','[\"Alice again, for.\", \"Lobster Quadrille.\", \"YOU, and no one to.\"]',NULL,2941,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(25,9,'I shall think nothing of the.','And have grown most uncommonly fat; Yet you finished the first really clever thing the King in a great hurry. \'You did!\' said the Pigeon. \'I\'m NOT a serpent!\' said Alice desperately: \'he\'s perfectly idiotic!\' And she tried to speak, and no room at all for any lesson-books!\' And so she turned to.','[\"THAT like?\' said.\", \"All this time with.\", \"Queen had only one.\"]',NULL,3389,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(26,9,'Mock Turtle to sing this.','Come on!\' So they had a VERY good opportunity for repeating his remark, with variations. \'I shall do nothing of tumbling down stairs! How brave they\'ll all think me at home! Why, I wouldn\'t be so proud as all that.\' \'With extras?\' asked the Gryphon, sighing in his note-book, cackled out \'Silence!\'.','[\"Alice, \'it\'s very.\", \"I had to sing you.\", \"Lory hastily. \'I.\"]',NULL,2066,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(27,9,'Caterpillar. \'Well, I should.','Puss,\' she began, rather timidly, saying to herself, as usual. I wonder if I would talk on such a very poor speaker,\' said the Caterpillar. \'Well, perhaps your feelings may be ONE.\' \'One, indeed!\' said the Hatter. \'Nor I,\' said the Cat said, waving its tail when I\'m angry. Therefore I\'m mad.\' \'I.','[\"It was the matter.\", \"Hatter. He came in.\", \"Alice ventured to.\"]',NULL,2247,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(28,10,'I must sugar my hair.\" As a.','Exactly as we needn\'t try to find that she began nursing her child again, singing a sort of lullaby to it in the house of the Lobster Quadrille, that she was as long as there was nothing else to do, so Alice soon began talking to herself, \'Now, what am I then? Tell me that first, and then treading.','[\"Dormouse followed.\", \"IT. It\'s HIM.\' \'I.\", \"Sir, With no jury.\"]',NULL,667,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(29,10,'I\'ll eat it,\' said the Queen.','Jack-in-the-box, and up I goes like a steam-engine when she went out, but it all came different!\' the Mock Turtle to sing you a present of everything I\'ve said as yet.\' \'A cheap sort of present!\' thought Alice. \'Now we shall have somebody to talk to.\' \'How are you getting on now, my dear?\' it.','[\"The Hatter opened.\", \"It was opened by.\", \"NEVER come to the.\"]',NULL,3135,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(30,10,'And yet I don\'t want to see.','King say in a low, trembling voice. \'There\'s more evidence to come upon them THIS size: why, I should be free of them bowed low. \'Would you like to drop the jar for fear of their hearing her; and the moment they saw Alice coming. \'There\'s PLENTY of room!\' said Alice as he spoke. \'A cat may look at.','[\"While she was now.\", \"Oh dear! I wish I.\", \"Alice was not here.\"]',NULL,1324,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(31,10,'As for pulling me out of the.','PLEASE mind what you\'re talking about,\' said Alice. \'Why?\' \'IT DOES THE BOOTS AND SHOES.\' the Gryphon remarked: \'because they lessen from day to day.\' This was not a mile high,\' said Alice. \'Why, there they are!\' said the Gryphon. \'It all came different!\' the Mock Turtle went on. Her listeners.','[\"I shall have to go.\", \"Cat, and vanished.\", \"Caterpillar took.\"]',NULL,186,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(32,11,'Hatter. He came in with the.','There was a different person then.\' \'Explain all that,\' he said to live. \'I\'ve seen a cat without a grin,\' thought Alice; \'but a grin without a grin,\' thought Alice; \'only, as it\'s asleep, I suppose it doesn\'t matter much,\' thought Alice, \'they\'re sure to kill it in time,\' said the March Hare.','[\"Mock Turtle. Alice.\", \"I am, sir,\' said.\", \"Dormouse say?\' one.\"]',NULL,2140,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(33,11,'Duchess, it had grown up,\'.','King, and the poor child, \'for I never knew so much contradicted in her pocket) till she was in managing her flamingo: she succeeded in curving it down \'important,\' and some of them even when they hit her; and the Queen merely remarking as it is.\' \'Then you shouldn\'t talk,\' said the King, the.','[\"I was thinking I.\", \"Alice began, in a.\", \"THE KING AND QUEEN.\"]',NULL,383,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(34,11,'Mock Turtle: \'why, if a dish.','Mock Turtle, \'Drive on, old fellow! Don\'t be all day to day.\' This was such a dreadful time.\' So Alice got up this morning? I almost wish I hadn\'t to bring but one; Bill\'s got the other--Bill! fetch it here, lad!--Here, put \'em up at this corner--No, tie \'em together first--they don\'t reach half.','[\"The first witness.\", \"Nobody moved. \'Who.\", \"Caterpillar called.\"]',NULL,1723,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(35,11,'Knave of Hearts, who only.','Alice felt a little bottle on it, (\'which certainly was not going to leave off being arches to do so. \'Shall we try another figure of the earth. Let me see: that would happen: \'\"Miss Alice! Come here directly, and get ready for your interesting story,\' but she had never seen such a capital one for.','[\"King, \'that only.\", \"English!\' said the.\", \"Alice. \'Then you.\"]',NULL,312,'general','2026-03-20 00:23:44','2026-03-20 00:23:44'),(36,1,'첫 글 테스트 중','이렇게 글을 등록 하면 되는구나 ㅎㅎ','[\"요약 1\", \"요약 2\"]',NULL,0,'general','2026-03-20 00:36:20','2026-03-20 00:36:20');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('sXV1x5nsfFkdHBoqwgDhrb2PGQWdTO9vvRjH3DoU',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibnVSZGN3cDgwT0l4c1RYc0dTWnBTRDh1eUJMeHVWYmhCTXpFR0VKNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1773999646),('XBkeF84w0fhRkdnh7kXNcTr158D39d2TyipDemv1',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkhYbURDdU93bFZIZ1ZjR3o2cTJnWG1uMG9NS3NOTDZhanBzSHdQOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773995581);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiers`
--

DROP TABLE IF EXISTS `tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_points` int NOT NULL DEFAULT '0',
  `icon_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tiers_min_points_index` (`min_points`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiers`
--

LOCK TABLES `tiers` WRITE;
/*!40000 ALTER TABLE `tiers` DISABLE KEYS */;
INSERT INTO `tiers` VALUES (1,'씨앗',0,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03'),(2,'새싹',100,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03'),(3,'잎새',500,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03'),(4,'가지',1500,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03'),(5,'열매',3000,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03'),(6,'거목',10000,NULL,'2026-03-20 00:23:03','2026-03-20 00:23:03');
/*!40000 ALTER TABLE `tiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_points` int NOT NULL DEFAULT '0',
  `tier_id` bigint unsigned DEFAULT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tier_id_foreign` (`tier_id`),
  KEY `users_current_points_index` (`current_points`),
  CONSTRAINT `users_tier_id_foreign` FOREIGN KEY (`tier_id`) REFERENCES `tiers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'테스트유저','test@example.com',NULL,'$2y$12$e6msz/uzU80gUh6lexwcsOwTvnwsC0MZ581JKHTnFtj7lsIT5uh.e',510,3,0,NULL,NULL,'2026-03-20 00:23:03','2026-03-20 00:36:20'),(2,'Hortense Schroeder','bins.maude@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'fVNX6l4yjI','2026-03-20 00:23:03','2026-03-20 00:23:03'),(3,'Mrs. Bette Considine Jr.','trever27@example.com','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'93DCMj68zm','2026-03-20 00:23:03','2026-03-20 00:23:03'),(4,'River Hill IV','jaquan61@example.org','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'JtvqTwNmDW','2026-03-20 00:23:03','2026-03-20 00:23:03'),(5,'Mrs. Ramona Halvorson I','yrogahn@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'6rx714McvD','2026-03-20 00:23:03','2026-03-20 00:23:03'),(6,'Abbie Hettinger','ledner.alexandrine@example.com','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'b5EqwgEaWA','2026-03-20 00:23:03','2026-03-20 00:23:03'),(7,'Dr. Hassan Nicolas','hreichel@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'ZtWpHQNiP4','2026-03-20 00:23:03','2026-03-20 00:23:03'),(8,'Gerald Conn','karolann.bailey@example.com','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'5H6VgmX9dq','2026-03-20 00:23:03','2026-03-20 00:23:03'),(9,'Mitchell Von','thompson.abdul@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'DRe4pXyNid','2026-03-20 00:23:03','2026-03-20 00:23:03'),(10,'Ewell Davis','mitchell.veda@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'SHXlzxipH5','2026-03-20 00:23:03','2026-03-20 00:23:03'),(11,'Carolina Ondricka','pauline.baumbach@example.net','2026-03-20 00:23:03','$2y$12$ipgCvgZDp77WGBOwF0ixYOhGsjyFDNLka.k5uALhMhuIuduEeW/eO',0,NULL,0,NULL,'XL502Bio1l','2026-03-20 00:23:03','2026-03-20 00:23:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-24  0:19:15
