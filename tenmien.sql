-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Jan 31, 2026 at 08:13 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tenmien`
--

-- --------------------------------------------------------

--
-- Table structure for table `caidatchung`
--

CREATE TABLE `caidatchung` (
  `id` int NOT NULL,
  `tieude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagebanner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sodienthoai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(2555) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(2555) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webgach` varchar(2565) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apikey` varchar(2555) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callback` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cardvip_partner_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CardVIP Partner ID',
  `cardvip_partner_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CardVIP Partner Key',
  `cardvip_api_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CardVIP API URL',
  `cardvip_callback` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CardVIP Callback URL',
  `facebook_link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zalo_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_bot_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telegram Bot Token',
  `telegram_admin_chat_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telegram Admin Chat ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caidatchung`
--

INSERT INTO `caidatchung` (`id`, `tieude`, `theme`, `keywords`, `mota`, `imagebanner`, `sodienthoai`, `banner`, `logo`, `webgach`, `apikey`, `callback`, `cardvip_partner_id`, `cardvip_partner_key`, `cardvip_api_url`, `cardvip_callback`, `facebook_link`, `zalo_phone`, `telegram_bot_token`, `telegram_admin_chat_id`) VALUES
(1, 'THANHVU.NET V4 UY TÍN TIỆN LỢI', '2', 'WEB BÁN DOMAIN NỘI ĐỊA UY TÍN', 'DỊCH VỤ DOMAIN UY TÍN CHẤT LƯỢNG', '', '0856761038', '4b455JDIvR8', '', 'cardvip.vn', '15626594-8251-4D4A-90E4-16F55C855D90', '/Packages/Callback.php', '32009175419', '422206536bdf5fce97e80c8e14d481eb', 'http://api.cardvip.vn/chargingws/v2', 'https://vtkt.online/callback', 'https://www.facebook.com/thanh.vu.826734', '0856761038', '8546022568:AAHq8cNiXZRa34pODa2Cfigx_fqbu9Wtalk', '7358984141');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int NOT NULL,
  `uid` int DEFAULT NULL,
  `pin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requestid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `uid` int DEFAULT NULL COMMENT 'ID người dùng gửi phản hồi',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên người dùng',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email người dùng',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Nội dung phản hồi/lỗi',
  `admin_reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Phản hồi từ admin',
  `status` int DEFAULT '0' COMMENT '0: Chờ xử lý, 1: Đã trả lời, 2: Đã đọc',
  `telegram_chat_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chat ID Telegram của user',
  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thời gian gửi',
  `reply_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thời gian admin trả lời'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `uid`, `username`, `email`, `message`, `admin_reply`, `status`, `telegram_chat_id`, `time`, `reply_time`) VALUES
(1, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'WEB NHƯ CAK', 'CUT', 2, NULL, '29/11/2025 - 00:03:39', '29/11/2025 - 00:04:36'),
(2, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'con cak admin', 'cmm', 2, NULL, '29/11/2025 - 00:19:09', '29/11/2025 - 00:19:41'),
(3, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'hi', 'hi bạn', 2, NULL, '01/12/2025 - 22:37:52', '02/12/2025 - 00:41:49'),
(4, 13, 'heo', 'hihihah@gmail.com', 'lô thg chó lỗi lắm vcl', 'sori bạn', 2, NULL, '02/01/2026 - 03:13:43', '02/01/2026 - 03:15:36'),
(5, 13, 'heo', 'huhu@gmail.com', 'quá là mệt', 'e xin lỗi ạ', 2, NULL, '02/01/2026 - 03:14:28', '02/01/2026 - 03:15:13'),
(6, 14, 'mun', 'hihihaha@gmail.com', 'LO AD XONG R', NULL, 0, NULL, '12/01/2026 - 02:34:35', NULL),
(7, 16, 'thuhoang', 'hoang@gmail.com', 'Alo ahhaa alo bạn ơi', NULL, 0, NULL, '12/01/2026 - 17:42:52', NULL),
(8, 12, 'vu1', 'thanhvuaws@gmail.com', '.aaaaaaaaaaaa', 'Cảm ơn bạn đã phản hồi. Chúng tôi đã xử lý vấn đề của bạn.', 2, NULL, '12/01/2026 - 19:04:26', '17/01/2026 - 21:23:44'),
(9, 22, 'caonggiac123', 'caonggiac@gmail.com', 'ditconcumay', 'nội dung phản hồi', 1, NULL, '12/01/2026 - 22:14:12', '17/01/2026 - 21:21:39'),
(10, 22, 'caonggiac123', 'caonggiac@gmail.com', 'web dải ló', 'Cảm ơn bạn đã phản hồi. Chúng tôi đã xử lý vấn đề của bạn.', 1, NULL, '12/01/2026 - 22:14:25', '17/01/2026 - 21:17:32'),
(11, 24, 'Topaz', 'chaunhutkha47@gmail.com', 'Chưa biết lỗi gì', NULL, 1, NULL, '13/01/2026 - 16:29:54', '17/01/2026 - 21:16:50'),
(12, 12, 'vu123', 'thanhvuaws@gmail.com', 'alo alo nghe ko', NULL, 1, NULL, '17/01/2026 - 20:04:40', '17/01/2026 - 20:04:55'),
(13, 12, 'vu123', 'thanhvuaws@gmail.com', 'hihihhihihihhihihih', NULL, 1, NULL, '17/01/2026 - 20:28:07', '17/01/2026 - 20:28:16'),
(14, 12, 'vu123', 'thanhvuaws@gmail.com', 'thg ad dep trai', 'Cảm ơn thg lol nha', 2, NULL, '17/01/2026 - 23:52:14', '17/01/2026 - 23:53:00'),
(15, 12, 'vu123', 'thanhvuaws@gmail.com', 'thgad deptrai vl', 'camon thg lol', 2, NULL, '20/01/2026 - 10:42:32', '20/01/2026 - 10:43:57'),
(16, 12, 'vu123', 'thanhvuaws@gmail.com', 'allo côde ngon nha', NULL, 1, NULL, '24/01/2026 - 10:40:33', '24/01/2026 - 10:40:41'),
(17, 12, 'vu123', 'thanhvuaws@gmail.com', 'hayyy qta  quá ta', 'Cảm ơn bạn đã phản nhắn cút đi', 2, NULL, '24/01/2026 - 10:41:40', '24/01/2026 - 10:42:10'),
(18, 26, 'anhky206', 'wkzum2006@gmail.com', 'hello tester here', 'CAMON BẠN NHE', 2, NULL, '26/01/2026 - 14:02:55', '26/01/2026 - 14:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int NOT NULL,
  `uid` int DEFAULT NULL,
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ns1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ns2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hsd` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `mgd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timedns` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ahihi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `uid`, `domain`, `ns1`, `ns2`, `hsd`, `status`, `mgd`, `time`, `timedns`, `ahihi`) VALUES
(13, 8, 'thnhs.com', 'hieuthu2.net', 'hieu2thu.net', 1, 1, '245972390', '09/10/2025 - 23:22:37', '0', 0),
(14, 8, 'chatgpt.store', 'hieu2thu', 'netthanhvu', 1, 1, '853435910', '10/10/2025 - 01:11:40', '0', 0),
(15, 8, 'cucudfsdf.com', '11111222', 'hieu2thu.net', 1, 1, '247305497', '11/10/2025 - 18:53:57', '0', NULL),
(16, 9, 'cucubububububuu.com', 'hieuthu2.neter', 'netthanhvu', 1, 1, '952109907', '15/10/2025 - 20:40:42', '0', 0),
(17, 11, 'systemadmin.info', 'sdfgsdgs234', 'sdfgsdgs', 1, 1, '958190992', '27/10/2025 - 01:53:43', '42/10/2025', 0),
(18, 11, 'awss12.org', 'awsfcj.org1', 'olderaws.org1', 1, 1, '131638644', '27/10/2025 - 16:08:37', '0', 0),
(19, 11, 'uqwojf.com', 'awsfcj.org1', 'olderaws.org', 1, 1, '300107175', '29/10/2025 - 01:37:30', '0', 0),
(20, 11, 'h.com', '1111122', 'olderaws.org', 1, 1, '787763391', '02/11/2025 - 21:19:22', '0', 0),
(21, 11, 'bv.com', 'hieuthu2.ne', 'olderaws.org', 1, 1, '130347298', '02/11/2025 - 21:23:02', '0', 0),
(22, 11, 'tst.com', 'q123', 'q', 1, 4, '200912899', '12/11/2025 - 16:56:44', '27/11/2025', 0),
(23, 11, 'minhnghia.info', 'minhnghia.sexy.inf', 'minhnghia.vn.info', 1, 1, '771515495', '19/11/2025 - 01:06:32', '17/12/2025', 0),
(24, 11, 'cucu.com', '111112', 'hieu2thu.net', 1, 1, '810047792', '02/12/2025 - 00:11:43', '0', NULL),
(25, 13, 'bvc.com', 'QQQQQQQQQQQQo', 'WWWWWWWWWWW', 1, 1, '17672981885201', '2026-01-02 03:09:48', '02/01/2026', 0),
(26, 12, 'sourceappnote.com', 'EEEEEEEEEE', 'RRRRRRRRRR', 1, 0, '17673290209962', '2026-01-02 11:43:40', '0', NULL),
(27, 12, 'trong.com', 'TTTTTTTTTT', 'RRR', 1, 0, '17673293771292', '2026-01-02 11:49:37', '0', NULL),
(28, 12, 'qw.com', 'E', 'R', 1, 0, '17673296325598', '2026-01-02 11:53:52', '0', NULL),
(29, 12, 'cho123nm.com', 'Uuuu', 'Qqqq', 1, 0, '17682161389556', '2026-01-12 18:08:58', '0', NULL),
(30, 12, 'abc.com', 'abc.info.com', 'abc.net.com', 1, 4, '17682178557852', '2026-01-12 18:37:35', '17/01/2026', 0),
(31, 24, 'vtktonline.net', 'wtf', 'wth', 1, 1, '17682967031568', '2026-01-13 16:31:43', '0', NULL),
(32, 12, 'manhmanh.tech', '123', '321', 1, 1, '17690114716176', '2026-01-21 23:04:31', '21/01/2026', 0),
(33, 26, 'vlua.info', 'vlua1', 'vlua2', 1, 1, '17694108876808', '2026-01-26 14:01:27', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hostinghistory`
--

CREATE TABLE `hostinghistory` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `hosting_id` int NOT NULL,
  `period` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mgd` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hostinghistory`
--

INSERT INTO `hostinghistory` (`id`, `uid`, `hosting_id`, `period`, `mgd`, `time`, `status`) VALUES
(1, 12, 8, 'month', '17682160987055', '2026-01-12 18:08:18', 1),
(2, 12, 9, 'month', '17682193058763', '2026-01-12 19:01:45', 1),
(3, 8, 9, 'year', '17684008088007', '2026-01-14 21:26:48', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listdomain`
--

CREATE TABLE `listdomain` (
  `id` int NOT NULL,
  `image` varchar(2655) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(2555) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duoi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listdomain`
--

INSERT INTO `listdomain` (`id`, `image`, `price`, `duoi`) VALUES
(1, '/domain/images/dot_com.svg', '66000', '.com'),
(2, '/domain/images/net-d3afe36203d3.svg', '55000', '.net'),
(3, '/domain/images/info-3a404a27668b.svg', '55000', '.info'),
(4, '/domain/images/org-292f994350a0.svg', '70000', '.org'),
(5, '/domain/images/tech-9e40579214ad.svg', '99000', '.tech'),
(12, 'images/ai.png', '1200000', '.ai'),
(13, 'images/io.png', '320000', '.io'),
(14, 'images/vaa.edu.vn.png', '10000000', '.vaa.edu.vn'),
(15, 'images/xyz.png', '230000', '.xyz'),
(16, 'images/online-39e2ea191774.svg', '300000', '.online');

-- --------------------------------------------------------

--
-- Table structure for table `listhosting`
--

CREATE TABLE `listhosting` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_month` int NOT NULL,
  `price_year` int NOT NULL,
  `specs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listhosting`
--

INSERT INTO `listhosting` (`id`, `name`, `description`, `price_month`, `price_year`, `specs`, `image`, `time`) VALUES
(1, 'Student Start', 'Gói hosting tiết kiệm nhất dành cho sinh viên, thực tập sinh làm bài tập lớn hoặc blog cá nhân đơn giản.', 25000, 250000, '1 Core CPU, 512MB RAM, 2GB SSD, Băng thông không giới hạn, 1 Website.', 'images/hosting/1.jpg', '02/01/2026 - 21:46:01'),
(2, 'Personal Basic', 'Phù hợp cho website giới thiệu bản thân, landing page hoặc các trang tin tức nhỏ ít người truy cập.', 49000, 490000, '1 Core CPU, 1GB RAM, 10GB SSD NVMe, Free SSL, 2 Database.', 'images/hosting/8.jpg', '02/01/2026 - 21:47:39'),
(3, 'Standard Host', 'Cân bằng giữa hiệu năng và chi phí. Lựa chọn tốt nhất cho các website bán hàng nhỏ và vừa.', 99000, 990000, '2 Core CPU, 2GB RAM, 20GB SSD NVMe, LiteSpeed Webserver, Daily Backup.', 'images/hosting/7.jpg', '02/01/2026 - 21:49:19'),
(4, 'WP Optimized', 'Được cấu hình chuyên biệt cho mã nguồn WordPress, tích hợp sẵn Cache giúp website tải nhanh gấp 3 lần.', 129000, 1290000, '2 Core CPU, 4GB RAM, 25GB NVMe, WP Toolkit Deluxe, Imunify360.', 'images/hosting/6.jpg', '02/01/2026 - 21:51:08'),
(5, 'Business Pro', 'Hạ tầng mạnh mẽ, ổn định cao dành cho website công ty, cổng thông tin điện tử yêu cầu chịu tải tốt.', 250000, 2500000, '4 Core CPU, 8GB RAM, 50GB NVMe, IP Riêng (Dedicated IP), Priority Support.', 'images/hosting/5.jpg', '02/01/2026 - 21:53:02'),
(6, 'E-com Power', 'Tối ưu hóa cho WooCommerce/Magento. Xử lý mượt mà các giao dịch mua bán và lượng truy cập lớn cùng lúc.', 450000, 4500000, '6 Core CPU, 12GB RAM, 100GB NVMe, Redis Cache, Chống DDoS Layer 7.', 'images/hosting/4.jpg', '02/01/2026 - 21:54:03'),
(7, 'Storage Max', 'Dành riêng cho nhu cầu lưu trữ dữ liệu, hình ảnh, backup hoặc chia sẻ file với dung lượng ổ cứng cực lớn.', 150000, 1500000, '1 Core CPU, 2GB RAM, 500GB HDD, Băng thông 10TB/tháng.', 'images/hosting/3.jpg', '02/01/2026 - 21:55:51'),
(8, 'Unlimited Plus', 'Thoải mái phát triển mà không lo về dung lượng hay băng thông. Phù hợp cho các nhà phát triển web quản lý nhiều site.', 300000, 3000000, '3 Core CPU, 6GB RAM, Unlimited SSD, Unlimited Bandwidth, Unlimited Addon Domains.', 'images/hosting/2.jpg', '02/01/2026 - 21:56:50'),
(9, 'Enterprise VIP', 'Giải pháp Hosting cao cấp nhất. Tài nguyên phần cứng biệt lập, đảm bảo hiệu suất 99.99% uptime.', 990000, 9900000, '8 Core CPU, 32GB RAM, 200GB NVMe Gen4, Backup theo giờ, Kỹ thuật hỗ trợ 1:1.', 'images/hosting/1.jpg', '02/01/2026 - 21:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `listsourcecode`
--

CREATE TABLE `listsourcecode` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` int NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `download_link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listsourcecode`
--

INSERT INTO `listsourcecode` (`id`, `name`, `description`, `price`, `file_path`, `download_link`, `image`, `category`, `time`) VALUES
(7, 'Source Code Game Bắn Súng FPS 3D - Đồ Họa Cực Đỉnh (Unity/Unreal)', 'Source code FPS 3D đồ họa chất lượng cao, được tối ưu mượt mà cho thương mại hoặc đồ án. Tích hợp kho vũ khí đa dạng, AI thông minh biết ẩn nấp/phục kích, cùng 2 chế độ chơi (Campaign & Survival). Giao diện hiện đại, cấu trúc code sạch dễ dàng Reskin.', 2500000, NULL, NULL, 'images/sourcecode/bansung.png', 'Ngôn ngữ: C# (Unity)', '24/01/2026 - 10:00:31'),
(8, 'Source Code Game Nấu Ăn Nhà Hàng - Cooking Master Chef 3D (Full Unity Project)', 'Đây là bộ Source Code Game Nấu Ăn (Cooking Simulation) độc quyền, giúp người chơi trải nghiệm cảm giác trở thành một đầu bếp thực thụ. Game được thiết kế với đồ họa bắt mắt, âm thanh vui nhộn, phù hợp cho mọi lứa tuổi.', 580000, NULL, NULL, 'images/sourcecode/codenauan.png', 'Unity, C#, Casual Game, Cooking, Simulation', '24/01/2026 - 10:07:17'),
(9, 'Roblox Racing Creator Toolkit - Bộ Công Cụ Làm Game Đua Xe Độc Quyền (Full Lua Script)', 'Sở hữu ngay bộ Toolkit độc quyền giúp bạn tự tay xây dựng game đua xe đỉnh cao trên nền tảng Roblox. Bộ công cụ cung cấp đầy đủ tài nguyên để bạn tạo ra một thế giới tốc độ mà không cần viết code từ đầu.', 5400000, NULL, NULL, 'images/sourcecode/duuaxee.png', 'Roblox, Lua, Racing Game, Game Kit, Script, 3D, Vehicle', '24/01/2026 - 10:09:18'),
(10, 'Facebook Security Toolkit - Bộ Công Cụ Bảo Mật & Quản Lý Tài Khoản (Full Source C#)', 'Giải pháp toàn diện cho việc quản lý và bảo vệ tài khoản Facebook số lượng lớn. Tool được thiết kế tối ưu cho các nhà quảng cáo (Ads thủ) và người làm dịch vụ Facebook, giúp tự động hóa quy trình bảo mật.', 7500000, NULL, NULL, 'images/sourcecode/fb.png', 'C#, .NET, Security Tool, Facebook API, Automation', '24/01/2026 - 10:12:45'),
(11, 'Source Code Game Sinh Tồn Thế Giới Mở Voxel - Minecraft Style (Đa Ngôn Ngữ)', 'Mã nguồn game Sandbox thế giới mở phong cách Voxel 3D huyền thoại. Cho phép người chơi tự do khám phá, khai thác tài nguyên và xây dựng các công trình vĩ đại từ những khối vuông kỳ diệu.', 3600000, NULL, NULL, 'images/sourcecode/minecraf.png', 'Voxel, Sandbox, Survival, Java, Multiplayer', '24/01/2026 - 10:14:25'),
(12, 'Roblox Creator Toolkit - Bộ Công Cụ Làm Game & Thiết Kế Map Độc Quyền (Lua Script)', 'Bạn muốn tự làm game Roblox nhưng chưa biết bắt đầu từ đâu? Bộ Toolkit độc quyền này cung cấp mọi công cụ cần thiết để bạn hiện thực hóa ý tưởng, từ thiết kế map parkour đến lập trình mini-game phức tạp.', 6300000, NULL, NULL, 'images/sourcecode/roblox.png', 'Roblox, Lua, Game Kit, Parkour, 3D, Script, Roblox Studio', '24/01/2026 - 10:16:01'),
(13, 'Source Code Game Sinh Tồn Trên Biển - Ocean Raft Survival Simulator (Full Unity Project)', 'Trải nghiệm cuộc chiến sinh tồn khắc nghiệt giữa đại dương bao la. Người chơi sẽ bắt đầu với một chiếc bè nhỏ và phải làm mọi cách để sống sót trước những mối nguy hiểm rình rập từ biển cả.', 7800000, NULL, NULL, 'images/sourcecode/sinhttontrenbien.png', 'Unity, Survival, Adventure, Ocean, Simulation, Raft, 3D', '24/01/2026 - 10:17:31'),
(14, 'Network Stress & Load Tester - Công Cụ Kiểm Thử Chịu Tải Web (Educational Purpose)', 'Bộ công cụ hỗ trợ lập trình viên và quản trị viên mạng mô phỏng lưu lượng truy cập lớn để kiểm tra độ ổn định của máy chủ (Server/VPS). Sản phẩm phục vụ mục đích nghiên cứu, học tập về giao thức mạng và tối ưu hóa hệ thống.', 8000000, NULL, NULL, 'images/sourcecode/toolddoss.png', 'C#, Network Tool, Stress Test, Load Balancing, TCP/IP', '24/01/2026 - 10:19:51'),
(15, 'Zombie Survival Shooter - Source Code Game Bắn Súng Sinh Tồn (Đa Ngôn Ngữ Support)', 'Dấn thân vào thế giới hậu tận thế đầy rẫy nguy hiểm. Đây là bộ mã nguồn game bắn súng góc nhìn thứ nhất (FPS) hoàn chỉnh, nơi người chơi phải chiến đấu chống lại làn sóng Zombie hung hãn để sinh tồn.', 4300000, NULL, NULL, 'images/sourcecode/zombies.png', 'C#, Python, C++, FPS, Zombie, Survival, Horror, 3D', '24/01/2026 - 10:23:01');

-- --------------------------------------------------------

--
-- Table structure for table `listvps`
--

CREATE TABLE `listvps` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price_month` int NOT NULL,
  `price_year` int NOT NULL,
  `specs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listvps`
--

INSERT INTO `listvps` (`id`, `name`, `description`, `price_month`, `price_year`, `specs`, `image`, `time`) VALUES
(1, 'VPS Cloud Starter', 'Cấu hình khởi điểm, phù hợp cho sinh viên học Linux, chạy VPN cá nhân hoặc các script nhẹ.', 89000, 890000, '1 Core CPU, 1GB RAM, 20GB SSD Cloud, 1 IP Public, Băng thông 1TB.', 'images/vps/1.jpg', '02/01/2026 - 22:09:51'),
(2, 'VPS Basic', 'Lựa chọn tốt cho việc chạy Website WordPress cá nhân, Landing Page hoặc Proxy nuôi tài khoản.', 159000, 1590000, '1 Core CPU, 2GB RAM, 30GB SSD NVMe, 1 IP Public, Miễn phí DirectAdmin.', 'images/vps/2.jpg', '02/01/2026 - 22:11:16'),
(3, 'VPS Business', 'Cân bằng hoàn hảo hiệu năng và chi phí. Chạy tốt các mã nguồn PHP, Node.js hoặc làm Server game nhỏ.', 250000, 2500000, '2 Core CPU, 4GB RAM, 50GB SSD NVMe, Băng thông không giới hạn, Backup tự động.', 'images/vps/3.jpg', '02/01/2026 - 22:12:07'),
(4, 'VPS Window', 'Tối ưu hóa cho HĐH Windows Server. Thích hợp treo tool MMO, chạy phần mềm kế toán hoặc Forex.', 320000, 3200000, '2 Core CPU, 4GB RAM, 60GB NVMe, Windows Server 2012/2019 License, Remote Desktop mượt.', 'images/vps/4.jpg', '02/01/2026 - 22:13:02'),
(5, 'VPS Pro Max', 'Sử dụng CPU xung nhịp cao. Dành cho website thương mại điện tử lớn, chạy quảng cáo hoặc App mobile backend.', 550000, 5500000, '4 Core CPU, 8GB RAM, 80GB NVMe Gen4, Chống DDoS Layer 4/7, Load Balancing.', 'images/vps/5.jpg', '02/01/2026 - 22:14:19'),
(6, 'VPS Storage', 'Dung lượng ổ cứng cực lớn, phù hợp làm server backup dữ liệu, chia sẻ file nội bộ hoặc camera an ninh.', 450000, 4500000, '2 Core CPU, 4GB RAM, 500GB HDD SAS, Băng thông 10TB/tháng, Tốc độ đọc ghi ổn định.', 'images/vps/6.jpg', '02/01/2026 - 22:15:33'),
(7, 'VPS Game Server', 'Cấu hình chuyên biệt để mở Server Minecraft, GTA 5 Roleplay hoặc CS:GO với độ trễ (Ping) cực thấp.', 790000, 7900000, '6 Core CPU (High Frequency), 16GB RAM, 100GB NVMe, Anti-DDoS Game, Network 1Gbps.', 'images/vps/7.jpg', '02/01/2026 - 22:16:43'),
(8, 'VPS GPU', 'Có tích hợp Card đồ họa rời. Dành cho nhu cầu treo giả lập Android, render video hoặc train AI đơn giản.', 1200000, 12000000, '8 Core CPU, 16GB RAM, GPU 4GB VRAM, 120GB SSD, Hỗ trợ ảo hóa lồng nhau (Nested Virtualization).', 'images/vps/8.jpg', '02/01/2026 - 22:17:46'),
(9, 'VPS Enterprise', 'Sức mạnh ngang ngửa máy chủ vật lý. Dành cho các hệ thống ERP, CRM hoặc Database lớn của doanh nghiệp.', 2500000, 25000000, '12 Core CPU, 32GB RAM, 300GB NVMe Raid 10, IP Riêng, Hỗ trợ kỹ thuật 24/7 VIP.', 'images/vps/9.jpg', '02/01/2026 - 22:18:44');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_01_28_000001_add_cardvip_fields_to_caidatchung_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('h.ttieen06@gmail.com', '$2y$12$Zqf30gBIs9FIA8QkOFwJpumjvWnzEOY0QjPqXdf.wWrrvrmooGKhu', '2026-01-21 15:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sourcecodehistory`
--

CREATE TABLE `sourcecodehistory` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `source_code_id` int NOT NULL,
  `mgd` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sourcecodehistory`
--

INSERT INTO `sourcecodehistory` (`id`, `uid`, `source_code_id`, `mgd`, `time`, `status`) VALUES
(14, 12, 14, '17692258993570', '2026-01-24 10:38:19', 1),
(15, 12, 15, '17694104158712', '2026-01-26 13:53:35', 1),
(16, 26, 15, '17694112034384', '2026-01-26 14:06:43', 1),
(17, 12, 14, '17694706818103', '2026-01-27 06:38:01', 1),
(18, 12, 13, '17695610172846', '2026-01-28 07:43:37', 1),
(19, 12, 11, '17695612197981', '2026-01-28 07:46:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `taikhoan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matkhau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tien` int DEFAULT '0',
  `chucvu` int DEFAULT '0',
  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `taikhoan`, `matkhau`, `email`, `tien`, `chucvu`, `time`) VALUES
(7, 'cuto', '1bbd886460827015e5d605ed44252251', 'chuml@gmail.com', 99999, 0, '09/10/2025 - 18:12:05'),
(8, 'thanhvu1', 'a3229bee41455f57b53a1e305d0b0037', 'cuto123@gmail.com', 888767999, 0, '09/10/2025 - 23:20:31'),
(9, 'cho123nm123', '069951877d52417a5e5375deca971622', 'chumli@gmail.com', 999933999, 0, '15/10/2025 - 18:05:32'),
(10, 'thanhvu2', '24e460f92c036c0a7928905bb84eba0a', 'toiiulaptrinh@gmail.com', 0, 0, '15/10/2025 - 23:44:12'),
(11, 'adminvu', 'c9279a3f6c684f5c7d5d7060fc4ac3b7', 'adc@gmail.com', 2146986247, 0, '16/10/2025 - 01:26:11'),
(12, 'vu123', '6e88c25d3c7c5fb51d3005deb663611c', 'thanhvuaws@gmail.com', 55120200, 1, '01/01/2026 - 20:52:15'),
(13, 'heo', 'fdd7a8526ca90079c6a6c446dcbbcfda', 'qưeqwaw@gmail.com', 32800, 0, '02/01/2026 - 02:57:15'),
(14, 'mun', '24e460f92c036c0a7928905bb84eba0a', 'hihihaha@gmail.com', 0, 0, '12/01/2026 - 02:34:03'),
(15, 'Bucu', '6828763a6c565d5fd15ea5ddbc04ec31', 'bubu@gmail.com', 500000, 0, '12/01/2026 - 03:09:42'),
(16, 'thuhoang', 'd23aeaa4304428811f3b38c03449bddb', 'hoang@gmail.com', 0, 0, '12/01/2026 - 17:41:57'),
(17, 'nammo123', 'f1859c5f746061d33730444b97a7a8e3', 'ahiham@gmail.com', 0, 0, '12/01/2026 - 18:20:20'),
(18, 'Vaa123', 'f1859c5f746061d33730444b97a7a8e3', '2431540219@vaa.edu.vn', 0, 0, '12/01/2026 - 18:22:58'),
(19, 'Nghialead123', 'd6b0ab7f1c8ab8f514db9a6d85de160a', 'nl473278@gmail.com', 0, 0, '12/01/2026 - 18:31:18'),
(20, 'HAHA', 'a2f41da47310013db5f6ae22c3288c3d', 'truongngoctien28082006@gmail.com', 0, 0, '12/01/2026 - 18:55:02'),
(21, 'thuytien', 'fd723056939a8c81ed44a84c82abdd19', 'h.ttieen06@gmail.com', 0, 0, '12/01/2026 - 21:49:01'),
(22, 'caonggiac123', '8afa4d901b1e02364cc60ea49a87636b', 'caonggiac@gmail.com', 0, 0, '12/01/2026 - 22:08:27'),
(23, 'ngua1', 'ac2647b79608c1ac868ad801045a01d9', 'ngua3@gmail.com', 0, 0, '12/01/2026 - 22:46:38'),
(24, 'Topaz', 'b5cac38130366f0e9b24cfe8f8385ef0', 'chaunhutkha47@gmail.com', 299943800, 0, '13/01/2026 - 16:27:47'),
(25, 'tien123', '047265ca4444f031cdf8ab851ba36cf5', 'thuytien@gmail.com', 0, 0, '26/01/2026 - 13:57:47'),
(26, 'anhky206', 'a4d213fc44651c6a610c180107e5ab23', 'wkzum2006@gmail.com', 5645000, 0, '26/01/2026 - 13:58:56');

-- --------------------------------------------------------

--
-- Table structure for table `vpshistory`
--

CREATE TABLE `vpshistory` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vps_id` int NOT NULL,
  `period` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mgd` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vpshistory`
--

INSERT INTO `vpshistory` (`id`, `uid`, `vps_id`, `period`, `mgd`, `time`, `status`) VALUES
(1, 12, 8, 'year', '17673673119500', '2026-01-02 22:21:52', 1),
(2, 12, 5, 'month', '17673692775136', '2026-01-02 22:54:38', 1),
(3, 8, 8, 'month', '17684007342502', '2026-01-14 21:25:34', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `caidatchung`
--
ALTER TABLE `caidatchung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_theme` (`theme`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requestid` (`requestid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_domain` (`domain`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_mgd` (`mgd`);

--
-- Indexes for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `hosting_id` (`hosting_id`),
  ADD KEY `mgd` (`mgd`);

--
-- Indexes for table `listdomain`
--
ALTER TABLE `listdomain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_duoi` (`duoi`);

--
-- Indexes for table `listhosting`
--
ALTER TABLE `listhosting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listsourcecode`
--
ALTER TABLE `listsourcecode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listvps`
--
ALTER TABLE `listvps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `email` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `source_code_id` (`source_code_id`),
  ADD KEY `mgd` (`mgd`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_taikhoan` (`taikhoan`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_chucvu` (`chucvu`);

--
-- Indexes for table `vpshistory`
--
ALTER TABLE `vpshistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `vps_id` (`vps_id`),
  ADD KEY `mgd` (`mgd`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `caidatchung`
--
ALTER TABLE `caidatchung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `listdomain`
--
ALTER TABLE `listdomain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `listhosting`
--
ALTER TABLE `listhosting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `listsourcecode`
--
ALTER TABLE `listsourcecode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `listvps`
--
ALTER TABLE `listvps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `vpshistory`
--
ALTER TABLE `vpshistory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `fk_cards_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  ADD CONSTRAINT `fk_hostinghistory_hosting` FOREIGN KEY (`hosting_id`) REFERENCES `listhosting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hostinghistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  ADD CONSTRAINT `fk_sourcecodehistory_sourcecode` FOREIGN KEY (`source_code_id`) REFERENCES `listsourcecode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sourcecodehistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vpshistory`
--
ALTER TABLE `vpshistory`
  ADD CONSTRAINT `fk_vpshistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vpshistory_vps` FOREIGN KEY (`vps_id`) REFERENCES `listvps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
