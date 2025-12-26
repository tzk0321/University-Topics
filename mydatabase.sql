-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: mydatabase
-- ------------------------------------------------------
-- Server version	8.0.43

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

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `food_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `food_id` (`food_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`food_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,55,2,'想詢問一下，何時方便拿？','2025-11-29 04:53:23'),(2,55,4,'中午12點 剛好午休有空','2025-11-29 04:56:00'),(3,55,2,'可以~ 那我們就約這個時間吧','2025-11-29 04:58:01'),(4,55,4,'喔對 要提醒你一下 這是生巧克力 會苦喔','2025-11-29 05:02:01'),(5,55,2,'恩恩 我知道 謝謝提醒！','2025-11-29 05:02:35'),(6,27,5,'您好！我想請問一下，今天甚麼時候方便拿取？','2025-12-01 04:38:57'),(7,27,3,'可能要等我下班，大約晚上7點半，你方便嗎','2025-12-01 04:40:41'),(8,27,5,'恩恩，沒有問題','2025-12-01 04:40:58');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_items`
--

DROP TABLE IF EXISTS `food_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food_items` (
  `food_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `food_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(20) NOT NULL,
  `category` enum('熟食','乾貨/速食','生鮮食品','蔬果','飲料','零食','甜點','其他') NOT NULL,
  `expiry_datetime` datetime NOT NULL,
  `pickup_address_city` varchar(50) NOT NULL,
  `pickup_address_street` varchar(100) NOT NULL,
  `pickup_landmark` varchar(100) DEFAULT NULL,
  `origin` varchar(100) NOT NULL,
  `storage_method` enum('常溫','冷藏','冷凍') NOT NULL,
  `item_state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '上架中',
  `remark` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`food_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `food_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_items`
--

LOCK TABLES `food_items` WRITE;
/*!40000 ALTER TABLE `food_items` DISABLE KEYS */;
INSERT INTO `food_items` VALUES (1,1,'rice_meal_01.jpg','經典排骨便當',2,'份','熟食','2025-11-23 18:30:00','臺北市信義區','忠孝東路五段20號','市政府捷運站','店家','常溫','未開封','下午兩點剛買的，請儘快取食','2025-11-22 16:42:48','2025-11-22 16:42:48'),(2,2,'bread_02.jpg','全麥吐司',1,'條','乾貨/速食','2025-11-26 10:00:00','新北市板橋區','文化路一段137巷5弄12號','社區警衛室','店家','常溫','未開封','多買了一條，效期新鮮','2025-11-22 16:42:48','2025-11-22 16:42:48'),(3,3,'fruit_03.jpg','有機香蕉',6,'根','蔬果','2025-11-24 15:00:00','桃園市中壢區','中山路123號','全聯對面','自製','常溫','已開封','有點熟了，適合馬上吃','2025-11-22 16:42:48','2025-11-22 16:42:48'),(4,1,'drink_04.jpg','無糖綠茶',4,'罐','飲料','2025-11-23 15:30:00','臺中市西屯區','福星路407號','逢甲夜市口','店家','冷藏','未開封','店內活動多拿的，保存良好','2025-11-22 16:42:48','2025-11-22 16:42:48'),(5,2,'noodle_05.jpg','紅燒牛肉麵',1,'碗','熟食','2025-11-23 14:00:00','高雄市苓雅區','四維四路12號','辦公大樓一樓','店家','常溫','未開封','臨時有事，沒空吃，包裝完整','2025-11-22 16:42:48','2025-11-22 16:42:48'),(6,3,'snack_06.jpg','海苔洋芋片',3,'包','零食','2026-03-01 23:59:00','臺北市大安區','復興南路一段88號','捷運站出口','店家','常溫','未開封','看電影多買的，很脆','2025-11-22 16:42:48','2025-11-22 16:42:48'),(7,1,'dessert_07.jpg','草莓大福',5,'個','甜點','2025-11-23 20:00:00','新北市永和區','中正路600巷2號','85度C旁邊','店家','冷藏','未開封','新鮮製作，建議兩小時內食用完畢','2025-11-22 16:42:48','2025-11-22 16:42:48'),(8,2,'canned_08.jpg','鮪魚罐頭',2,'罐','乾貨/速食','2026-12-31 00:00:00','桃園市桃園區','國際路二段500號','便利商店前','店家','常溫','未開封','家裡囤太多，長期食品','2025-11-22 16:42:48','2025-11-22 16:42:48'),(9,3,'meat_09.jpg','冷凍雞胸肉',3,'片','生鮮食品','2026-01-15 00:00:00','臺中市北區','健行路400號','市場門口','店家','冷凍','未開封','適合健身者，真空包裝','2025-11-22 16:42:48','2025-11-22 16:42:48'),(10,1,'vegetarian_10.jpg','素食炒飯',2,'份','熟食','2025-11-23 13:00:00','高雄市左營區','博愛二路100號','百貨公司側門','店家','常溫','未開封','蛋奶素，份量足','2025-11-22 16:42:48','2025-11-22 16:42:48'),(11,2,'cake_11.jpg','香草蛋糕',1,'個','甜點','2025-11-24 12:00:00','臺北市松山區','南京東路三段303巷','路口花店','店家','冷藏','未開封','同事生日會剩餘，口感綿密','2025-11-22 16:42:48','2025-11-22 16:42:48'),(12,3,'vegetable_12.jpg','高麗菜',1,'顆','蔬果','2025-11-27 00:00:00','新北市三重區','重新路四段100號','菜市場入口','店家','常溫','未開封','新鮮直送，無農藥殘留','2025-11-22 16:42:48','2025-11-22 16:42:48'),(13,1,'soup_13.jpg','玉米濃湯',2,'碗','熟食','2025-11-23 19:00:00','桃園市中壢區','中央路168號','大學宿舍門口','自製','常溫','未開封','晚餐多煮的','2025-11-22 16:42:48','2025-11-22 16:42:48'),(14,2,'juice_14.jpg','鮮榨果汁',3,'瓶','飲料','2025-11-24 09:00:00','臺中市南屯區','公益路二段51號','健身房櫃台','店家','冷藏','未開封','柳橙口味，無添加糖','2025-11-22 16:42:48','2025-11-22 16:42:48'),(15,3,'pork_15.jpg','冷凍豬肉片',500,'克','生鮮食品','2026-02-01 00:00:00','高雄市前鎮區','中山三路200號','家樂福對面','店家','冷凍','未開封','火鍋肉片，原價販售','2025-11-22 16:42:48','2025-11-22 16:42:48'),(16,1,'sandwich_16.jpg','鮪魚三明治',4,'個','熟食','2025-11-23 17:00:00','臺北市內湖區','瑞光路301號','科技大樓一樓','店家','冷藏','未開封','早餐店剩餘，當作下午茶','2025-11-22 16:42:48','2025-11-22 16:42:48'),(17,2,'cookie_17.jpg','手工餅乾',12,'片','零食','2025-12-15 00:00:00','新北市土城區','中央路三段2號','烘焙坊','自製','常溫','已開封','剛出爐，密封保存','2025-11-22 16:42:48','2025-11-22 16:42:48'),(18,3,'instant_noodles_18.jpg','泡麵組合',8,'包','乾貨/速食','2026-08-01 00:00:00','桃園市八德區','介壽路一段99號','大潤發旁邊','店家','常溫','未開封','多種口味，宵夜必備','2025-11-22 16:42:48','2025-11-22 16:42:48'),(19,1,'dumpling_19.jpg','冷凍水餃',30,'顆','生鮮食品','2026-04-01 00:00:00','臺中市西區','臺灣大道二段300號','管理員處','店家','冷凍','未開封','高麗菜豬肉餡','2025-11-22 16:42:48','2025-11-22 16:42:48'),(20,2,'muffin_20.jpg','巧克力瑪芬',6,'個','甜點','2025-11-24 15:00:00','高雄市楠梓區','大學路200號','宿舍交誼廳','店家','常溫','未開封','口感濕潤，可微波加熱','2025-11-22 16:42:48','2025-11-22 16:42:48'),(21,3,'tea_21.jpg','紅茶包',1,'盒','飲料','2027-01-01 00:00:00','臺北市中山區','林森北路50號','旅館大廳','店家','常溫','未開封','英國進口，獨立包裝','2025-11-22 16:42:48','2025-11-22 16:42:48'),(22,1,'pizza_22.jpg','夏威夷披薩',1,'份','熟食','2025-11-23 16:30:00','新北市中和區','景平路40號','披薩店門口','店家','常溫','已開封','剩餘兩片，外帶包裝','2025-11-22 16:42:48','2025-11-22 16:42:48'),(23,2,'rice_23.jpg','白米飯',2,'碗','熟食','2025-11-23 12:30:00','桃園市平鎮區','中豐路50號','自助餐店','店家','常溫','未開封','午餐多打的，無配菜','2025-11-22 16:42:48','2025-11-22 16:42:48'),(24,3,'salad_24.jpg','生菜沙拉',4,'盒','蔬果','2025-11-23 21:00:00','臺中市南區','復興路三段100號','醫院對面','店家','冷藏','未開封','附凱薩醬，適合輕食','2025-11-22 16:42:48','2025-11-22 16:42:48'),(25,1,'seafood_25.jpg','冷凍蝦仁',400,'克','生鮮食品','2026-03-20 00:00:00','高雄市鼓山區','明誠路88號','海鮮專賣店','店家','冷凍','未開封','新鮮急凍，適用炒菜','2025-11-22 16:42:48','2025-11-22 16:42:48'),(26,2,'energy_bar_26.jpg','能量棒',10,'條','零食','2026-05-01 00:00:00','臺北市士林區','承德路四段15號','體育館入口','店家','常溫','未開封','健身後補充能量','2025-11-22 16:42:48','2025-11-22 16:42:48'),(27,3,'cup_noodle_27.jpg','速食杯麵',5,'杯','乾貨/速食','2025-12-10 00:00:00','新北市新莊區','中正路800號','宿舍自動販賣機旁','店家','常溫','未開封','即期品便宜分享','2025-11-22 16:42:48','2025-11-22 16:42:48'),(28,1,'pie_28.jpg','蘋果派',8,'吋','甜點','2025-11-24 18:00:00','桃園市龜山區','文化一路200巷','家庭住家','自製','冷藏','已開封','慶祝派對剩餘一半','2025-11-22 16:42:48','2025-11-22 16:42:48'),(29,2,'beef_29.jpg','滷牛肉',300,'克','熟食','2025-11-24 09:00:00','臺中市大里區','國光路二段50號','熟食攤位','店家','冷藏','未開封','真空包裝，已調味','2025-11-22 16:42:48','2025-11-22 16:42:48'),(30,3,'egg_30.jpg','雞蛋',10,'顆','生鮮食品','2025-12-05 00:00:00','高雄市三民區','建工路400號','農業市集','店家','冷藏','未開封','友善飼養，放牧雞蛋','2025-11-22 16:42:48','2025-11-22 16:42:48'),(31,1,'drink_31.jpg','罐裝咖啡',12,'罐','飲料','2025-12-01 00:00:00','臺北市信義區','基隆路一段10號','便利商店','店家','常溫','未開封','即將到期，整箱販售','2025-11-22 16:42:48','2025-11-22 16:42:48'),(32,2,'bread_32.jpg','法式長棍',1,'條','乾貨/速食','2025-11-24 10:00:00','新北市板橋區','縣民大道三段1號','百貨公司麵包店','店家','常溫','未開封','當日現烤，口感酥脆','2025-11-22 16:42:48','2025-11-22 16:42:48'),(33,3,'soup_33.jpg','雞湯塊',4,'盒','乾貨/速食','2027-06-01 00:00:00','桃園市中壢區','環中東路700號','大賣場出口','店家','常溫','未開封','方便料理，快速煮湯','2025-11-22 16:42:48','2025-11-22 16:42:48'),(34,1,'vegetable_34.jpg','紅蘿蔔',5,'條','蔬果','2025-11-29 00:00:00','臺中市西屯區','市政路1號','市政府公車站牌','店家','冷藏','未開封','新鮮，可做果汁','2025-11-22 16:42:48','2025-11-22 16:42:48'),(35,2,'dumpling_35.jpg','素食鍋貼',10,'個','熟食','2025-11-23 15:00:00','高雄市苓雅區','三多四路80號','百貨美食街','店家','常溫','未開封','純素，內餡豐富','2025-11-22 16:42:48','2025-11-22 16:42:48'),(36,3,'snack_36.jpg','堅果組合',1,'罐','零食','2026-02-01 00:00:00','臺北市大安區','仁愛路四段505號','公司櫃台','店家','常溫','未開封','健康零食，已開封但未食用','2025-11-22 16:42:48','2025-11-22 16:42:48'),(37,1,'dessert_37.jpg','提拉米蘇',2,'杯','甜點','2025-11-24 12:00:00','新北市永和區','得和路378號','咖啡店','店家','冷藏','未開封','濃郁咖啡香','2025-11-22 16:42:48','2025-11-22 16:42:48'),(38,2,'can_38.jpg','玉米粒罐頭',4,'罐','乾貨/速食','2026-11-01 00:00:00','桃園市桃園區','國際路一段100號','社區活動中心','店家','常溫','未開封','煮湯或沙拉皆可','2025-11-22 16:42:48','2025-11-22 16:42:48'),(39,3,'fish_39.jpg','冷凍鮭魚',2,'片','生鮮食品','2026-01-01 00:00:00','臺中市北區','五權路22號','生鮮超市','店家','冷凍','未開封','厚切，適合香煎','2025-11-22 16:42:48','2025-11-22 16:42:48'),(40,1,'rice_meal_40.jpg','日式壽司組合',15,'個','熟食','2025-11-23 20:00:00','高雄市左營區','自由二路111號','壽司店','店家','冷藏','未開封','多種口味，建議兩小時內食用','2025-11-22 16:42:48','2025-11-22 16:42:48'),(41,2,'cake_41.jpg','芋泥捲',1,'條','甜點','2025-11-24 18:00:00','臺北市松山區','復興北路500號','糕點店','店家','冷藏','未開封','綿密芋泥，口感清爽','2025-11-22 16:42:48','2025-11-22 16:42:48'),(42,3,'veg_42.jpg','有機小黃瓜',3,'條','蔬果','2025-11-26 00:00:00','新北市三重區','集美街33號','農夫市集','店家','常溫','未開封','可直接生食','2025-11-22 16:42:48','2025-11-22 16:42:48'),(43,1,'instant_rice_43.jpg','調理包飯',5,'盒','乾貨/速食','2026-09-01 00:00:00','桃園市中壢區','健行路50號','便利商店','店家','常溫','未開封','咖哩雞口味，微波即食','2025-11-22 16:42:48','2025-11-22 16:42:48'),(44,2,'drink_44.jpg','氣泡水',6,'罐','飲料','2026-10-01 00:00:00','臺中市南屯區','文心路一段99號','大樓地下室','店家','常溫','未開封','檸檬風味，解渴','2025-11-22 16:42:48','2025-11-22 16:42:48'),(45,3,'meat_45.jpg','羊肉片',400,'克','生鮮食品','2026-02-15 00:00:00','高雄市前鎮區','中華五路50號','好市多附近','店家','冷凍','未開封','涮火鍋專用','2025-11-22 16:42:48','2025-11-22 16:42:48'),(46,1,'meal_46.jpg','義大利麵',1,'份','熟食','2025-11-23 18:00:00','臺北市內湖區','內湖路一段300號','餐廳側門','店家','常溫','未開封','奶油培根口味，剛做好','2025-11-22 16:42:48','2025-11-22 16:42:48'),(47,2,'chocolate_47.jpg','黑巧克力',3,'片','零食','2026-06-01 00:00:00','新北市土城區','學府路二段1號','宿舍大門','店家','常溫','未開封','70% 可可','2025-11-22 16:42:48','2025-11-22 16:42:48'),(48,3,'canned_soup_48.jpg','蘑菇濃湯罐頭',2,'罐','乾貨/速食','2027-03-01 00:00:00','桃園市八德區','興豐路333號','雜貨店','店家','常溫','未開封','加熱即可食用','2025-11-22 16:42:48','2025-11-22 16:42:48'),(49,1,'seafood_49.jpg','冷凍花枝丸',1,'包','生鮮食品','2026-05-01 00:00:00','臺中市西區','公正路18號','社區冰箱','店家','冷凍','未開封','煮湯或油炸皆宜','2025-11-22 16:42:48','2025-11-22 16:42:48'),(50,2,'donut_50.jpg','甜甜圈',4,'個','甜點','2025-11-24 08:00:00','高雄市楠梓區','德民路66號','麵包店','店家','常溫','未開封','原味和草莓各兩個','2025-11-22 16:42:48','2025-11-22 16:42:48'),(51,4,'apple_box.jpg','富士蘋果禮盒',3,'顆','蔬果','2025-11-29 18:00:00','台北市','信義路五段7號','101大樓門口','日本青森','常溫','未開封','親友送的禮盒，吃不完分享','2025-11-27 08:54:47','2025-11-27 08:54:47'),(52,4,'costco_chicken.jpg','好市多烤雞',1,'半隻','熟食','2025-11-28 22:00:00','台北市','信義路五段7號','101大樓門口','好市多','冷藏','已開封','晚餐買太多，已分裝到保鮮盒，請盡快領取','2025-11-27 08:54:47','2025-11-27 08:54:47'),(53,4,'oolong_tea.jpg','高山烏龍茶飲',2,'瓶','飲料','2025-11-28 23:59:59','台北市','信義路五段7號','101大樓門口','便利商店','冷藏','未開封','開會多訂的飲料','2025-11-27 08:54:47','2025-11-27 08:54:47'),(54,4,'instant_noodle.jpg','維力炸醬麵',1,'袋(5包)','乾貨/速食','2025-11-30 12:00:00','台北市','信義路五段7號','101大樓門口','全聯','常溫','未開封','家裡庫存太多，期限快到了','2025-11-27 08:54:47','2025-11-27 08:54:47'),(55,4,'chocolate_cake.jpg','生巧克力蛋糕',1,'塊','甜點','2025-11-29 15:00:00','台北市','信義路五段7號','101大樓門口','知名甜點店','冷藏','未開封','這幾天不在家怕壞掉，送給有緣人','2025-11-27 08:54:47','2025-11-27 08:54:47'),(56,4,'1764523709_4170.jpg','羊奶',1,'瓶','飲料','2025-12-02 11:59:00','屏東市','民生路4-18號','屏東大學民生校區','台南民宿','冷藏','上架中','狀態：未開封。備註：','2025-11-30 17:28:29','2025-11-30 17:28:29'),(57,5,'1764534649_1182.jpg','義大利麵',1,'盤','熟食','2025-12-01 11:30:00','屏東市','民生路4-18號','屏大民生校區','自己做','常溫','已預訂','狀態：已開封。備註：煮的時候，份量抓錯了','2025-11-30 20:30:49','2025-11-30 20:42:41');
/*!40000 ALTER TABLE `food_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_items_label`
--

DROP TABLE IF EXISTS `food_items_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food_items_label` (
  `food_id` int unsigned NOT NULL,
  `label_id` int unsigned NOT NULL,
  PRIMARY KEY (`food_id`,`label_id`),
  KEY `label_id` (`label_id`),
  CONSTRAINT `food_items_label_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`food_id`) ON DELETE CASCADE,
  CONSTRAINT `food_items_label_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `labels` (`label_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_items_label`
--

LOCK TABLES `food_items_label` WRITE;
/*!40000 ALTER TABLE `food_items_label` DISABLE KEYS */;
INSERT INTO `food_items_label` VALUES (1,1),(5,2),(54,2),(57,2),(10,3),(23,3),(40,3),(4,4),(14,4),(31,4),(44,4),(53,4),(56,4),(2,5),(20,5),(32,5),(11,6),(28,6),(41,6),(55,6),(10,7),(24,7),(35,7),(51,7),(8,8),(38,8),(48,8),(2,9),(3,9),(30,9),(42,9),(51,9),(4,10),(27,10),(31,10),(52,10),(53,10),(54,10),(55,10),(56,10),(57,10);
/*!40000 ALTER TABLE `food_items_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labels` (
  `label_id` int unsigned NOT NULL AUTO_INCREMENT,
  `label_name` varchar(50) NOT NULL,
  PRIMARY KEY (`label_id`),
  UNIQUE KEY `label_name` (`label_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `labels`
--

LOCK TABLES `labels` WRITE;
/*!40000 ALTER TABLE `labels` DISABLE KEYS */;
INSERT INTO `labels` VALUES (1,'便當'),(10,'即期食品'),(9,'有機'),(7,'素食'),(8,'罐頭'),(6,'蛋糕'),(3,'飯食'),(4,'飲料'),(5,'麵包'),(2,'麵食');
/*!40000 ALTER TABLE `labels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,4,'有人預約了您的食物 (ID: 55)，請前往查看。',0,'2025-11-29 05:19:30'),(2,4,'有人預約了您的食物 (ID: 51)，請前往查看。',0,'2025-11-29 16:18:03'),(3,4,'有人預約了您的食物 (ID: 51)，請前往「我的預約/分享」進行審核。',0,'2025-11-29 16:23:29'),(4,3,'有人在您的料理「速食杯麵」留言：您好！我想請問一下，...',0,'2025-12-01 04:38:57'),(5,3,'有人在您的料理「速食杯麵」留言：恩恩，沒有問題',0,'2025-12-01 04:40:58'),(6,5,'有人預約了您的食物 (ID: 57)，請前往「我的預約/分享」進行審核。',0,'2025-12-01 04:41:46'),(7,3,'有人預約了您的食物 (ID: 27)，請前往「我的預約/分享」進行審核。',0,'2025-12-01 04:42:17'),(8,2,'恭喜！發布者同意了您的預約 (訂單 #2)，請準時前往領取。',0,'2025-12-01 04:42:41'),(9,2,'有人預約了您的食物 (ID: 44)，請前往「我的預約/分享」進行審核。',0,'2025-12-01 04:46:06');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `food_id` int unsigned NOT NULL,
  `requester_id` int unsigned NOT NULL,
  `publisher_id` int unsigned NOT NULL,
  `status` enum('pending','confirmed','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `food_id` (`food_id`),
  KEY `requester_id` (`requester_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`food_id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,51,2,4,'confirmed','2025-11-29 16:23:29'),(2,57,2,5,'confirmed','2025-12-01 04:41:46'),(3,27,5,3,'pending','2025-12-01 04:42:17'),(4,44,5,2,'pending','2025-12-01 04:46:06');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `account` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'user','index123456@gmail.com','password123456'),(2,'Judy','judy05@gmail.com.tw','judy05'),(3,'Eric','Eric963@gmail.com.tw','Eric123456789'),(4,'smallfive','smallfive@gmail.com','smail55555'),(5,'test','test01@gmail.com','test01');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-04  8:14:18
