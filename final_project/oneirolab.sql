-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2026-06-26 12:42:27
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `oneirolab`
--

-- --------------------------------------------------------

--
-- 資料表結構 `dreams`
--

CREATE TABLE `dreams` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sentiment` varchar(20) NOT NULL,
  `sentiment_score` float NOT NULL,
  `analysis` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_public` tinyint(1) DEFAULT 0,
  `resonance_count` int(11) DEFAULT 0,
  `ai_analysis` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `dreams`
--

INSERT INTO `dreams` (`id`, `user_id`, `content`, `sentiment`, `sentiment_score`, `analysis`, `image_url`, `created_at`, `is_public`, `resonance_count`, `ai_analysis`, `status`) VALUES
(8, 1, '考試60分', '中性', 0, NULL, NULL, '2026-06-24 13:51:58', 0, 0, NULL, 'active'),
(9, 1, '考試70分', '正向', 0.25, NULL, NULL, '2026-06-24 13:52:05', 0, 0, NULL, 'active'),
(10, 1, '考試80分', '正向', 0.5, NULL, NULL, '2026-06-24 13:52:13', 0, 0, NULL, 'active'),
(11, 1, '考試90分', '正向', 0.75, NULL, NULL, '2026-06-24 13:52:30', 0, 0, NULL, 'active'),
(12, 1, '考試100分', '正向', 1, NULL, 'https://picsum.photos/seed/dream_12/600/400', '2026-06-24 13:52:37', 0, 0, '讓我們一起探索這個夢境的深層意義。\n\n**佛洛伊德視角**\n\n夢境中的「考試100分」可能代表個人的潛意識壓抑和慾望。考試通常與成就和評估有關，代表著個人的自我評估和期望。達到100分的成績可能象徵著個人的渴望獲得完全的認可和接受。然而，這也可能是個人的潛意識壓抑的表現，表明個人內心深處的恐懼和自我批評。\n\n**榮格視角**\n\n從榮格的角度來看，這個夢境可能與集體潛意識和原型符號有關。考試和成績可能代表著個人的內在原型符號，例如「成功者」或「失敗者」。這個夢境可能表明個人的內在衝突和對成功的渴望。榮格也提到，原型符號往往與個人的童年經驗和家族傳承有關，夢境中的考試可能是個人的潛意識對童年經驗和家族傳承的回應。\n\n這個夢境的意義可能是個人的潛意識對成功和認可的渴望，同時也可能是個人的內在壓抑和恐懼的表現。', 'active'),
(13, 1, '踩到狗大便', '負向', -0.6, NULL, NULL, '2026-06-24 13:52:49', 1, 0, '首先，我們需要了解這個夢境背後的潛在意義。從佛洛伊德的角度來看，這個夢境可能代表著個體的潛意識壓抑和慾望。踩到狗大便可能象徵著個體對於某些不舒服或不被承認的慾望或情感的壓抑。狗大便可能代表著個體的無意識或不被承認的慾望，踩到它可能象徵著個體對這些慾望的壓抑或逃避。\n\n從榮格的角度來看，這個夢境可能與集體潛意識和原型符號有關。狗大便可能代表著個體與原始、自然的慾望和情感的連結。踩到它可能象徵著個體的自我覺醒和面對自己的原始慾望和情感的意願。這個夢境可能提示著個體需要面對自己的真實慾望和情感，放棄不必要的壓抑和逃避。', 'active'),
(14, 1, '沒帶雨傘走在路上突然下大雨', '負向', -0.6, NULL, NULL, '2026-06-24 13:53:27', 0, 0, NULL, 'active'),
(15, 1, '在路上撿到錢', '正向', 0.3, NULL, NULL, '2026-06-24 17:02:24', 1, 0, NULL, 'active'),
(16, 1, '在路上撿到錢', '正向', 0.3, NULL, 'https://picsum.photos/seed/dream_16/600/400', '2026-06-25 02:24:31', 0, 0, NULL, 'active'),
(17, 6, '那是一個被濃霧包裹的黃昏。所有的聲音都被拉長、放大，像是隔著厚厚的水層聽著舊唱片。我明知道自己正在走一條回家的路，但周遭的建築卻像萬花筒般不斷扭曲、重組，把童年的老房子與陌生的摩天大樓拼貼在一起。空氣裡瀰漫著一種揉合了雨水與陳舊紙張的氣味。在這裡，時間不是一條直線，而是一個不斷墜落的螺旋，我一邊墜落，一邊看著無數個過去與未來的自己擦肩而過。', '負向', -0.7, NULL, 'https://picsum.photos/seed/dream_17/600/400', '2026-06-25 17:10:45', 1, 0, '這個夢境似乎是一個複雜的符號系統，需要從不同的角度進行分析。從佛洛伊德的角度來看，這個夢境可能代表著個體的潛意識壓抑與慾望。濃霧、拉長的聲音和扭曲的建築可能象徵著個體的內心混亂和無法表達的慾望。\n\n在這個夢境中，個體正在走一條回家的路，但周遭的環境卻變得扭曲和不確定，這可能代表著個體對於自己的身份和未來的不確定性。空氣裡的氣味也許代表著個體內心的混亂和無法釋放的壓力。\n\n從榮格的角度來看，這個夢境可能代表著個體與集體潛意識的連結。黃昏的景色和扭曲的建築可能象徵著個體的原始經驗和無法表達的自我。無數個過去與未來的自己擦肩而過，也許代表著個體對於自己的多重身份和未來的可能性。\n\n總的來說，這個夢境是一個複雜的符號系統，需要透過不同的角度進行分析才能揭示其真實含義。', 'active'),
(18, 6, '我置身於一片無邊無際的金黃色麥浪之中，風吹過的時候，發出像海浪一樣溫柔的沙沙聲。天空是近乎透明的極光色，粉紅與翠綠交織。我發現自己竟然能輕盈地飄浮起來，不需要翅膀，只要輕輕一躍，就能在溫暖的氣流中滑行。那些在現實中早已遺失的物件、逝去的寵物，都安靜地圍繞在身邊。沒有焦慮，沒有喧囂，只有一種被宇宙深深擁抱著的、久違的平靜。', '正向', 0.95, NULL, 'https://picsum.photos/seed/dream_18/600/400', '2026-06-25 17:12:35', 0, 0, '這個夢境表面上看似是一個美麗的、充滿和諧的場景，但深層分析卻揭示出更為複雜的內涵。\n\n**佛洛伊德視角**\n\n從佛洛伊德的觀點來看，這個夢境可能代表著個體的潛意識壓抑和慾望的滿足。金黃色麥浪和溫柔的沙沙聲可能象徵著個體的內在世界中充滿著美好和愉悅的感受。飄浮的體驗可能代表著個體對於自由和無拘束的慾望。那些在現實中遺失的物件和逝去的寵物的出現，可能是個體對於過去的回憶和未來的希望的渴望。\n\n**榮格視角**\n\n從榮格的觀點來看，這個夢境可能代表著個體與集體潛意識的連結。極光色和粉紅與翠綠的交織可能象徵著個體與自然和宇宙的連結。飄浮的體驗可能代表著個體對於自己的原型符號的覺醒，原型符號是指個體內在世界中固有的、無需經驗就能知覺的模式和形象。這個夢境可能是個體對於自己的潛在能力和潛在自我覺醒的預兆。', 'active'),
(19, 7, '跟阿公一起抓老鼠', '正向', 0.7, NULL, 'https://picsum.photos/seed/dream_19/600/400', '2026-06-25 17:28:45', 0, 0, NULL, 'active'),
(20, 1, '踩到狗大便', '負向', -0.8, NULL, 'https://picsum.photos/seed/dream_20/600/400', '2026-06-25 17:31:56', 0, 0, '讓我們一起探索這個夢境的深層意義。\n\n**佛洛伊德視角：**\n\n踩到狗大便的夢境可能代表了你內心的潛意識壓抑與慾望。狗大便可能象徵著你不願意面對的污垢或不潔之物，這可能與你的內心世界中某些不舒服的感受或慾望有所關聯。這個夢境可能是你潛意識的方法，表達出你不願意承認或面對的感受或慾望。\n\n**榮格視角：**\n\n從榮格的角度來看，狗大便可能代表著集體潛意識中的「不潔」或「污穢」的原型符號。這可能與你的內心世界中某些不安全或不穩定的感受有所關聯。這個夢境可能是你潛意識的方法，表達出你對於不確定的感受的恐懼或不適應。\n\n總的來說，這個夢境可能是你內心世界中某些不舒服的感受或慾望的表達，這需要你深入探索自己內心世界的意義。', 'deleted'),
(21, 1, '踩到一大坨狗大便', '負向', -0.8, NULL, NULL, '2026-06-25 17:36:08', 1, 0, NULL, 'active'),
(22, 1, '踩到自己拉的大便', '負向', -0.8, NULL, 'https://picsum.photos/seed/dream_22/600/400', '2026-06-25 17:36:25', 0, 0, NULL, 'deleted');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('dreamer','analyst') DEFAULT 'dreamer',
  `balance` int(11) DEFAULT 100,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `balance`, `created_at`) VALUES
(1, 'BoYuan', '$2b$10$NCwqtZaxP6XpKuEGRB7ydujbVJDLcjYufWBwtibRgc2SPlEcdsiYe', 'a1123356@mail.nuk.edu.tw', 'dreamer', 210, '2026-06-24 11:34:32'),
(5, 'Benson', '$2b$10$HQUwT55VdHSrSVJvezvs9Of1kY/BGrAUQwLbgSL.cCkHNMIW1Q8aG', 'a0909805776@gmail.com', 'analyst', 0, '2026-06-24 16:51:58'),
(6, '許帛軒', '$2b$10$A9Z/13q1LlD.r7wDM1Fx2eifaXe6Ub4Ll7CaBIqX8jE2q1OLJXZkS', 'a1123320@mail.nuk.edu.tw', 'dreamer', 80, '2026-06-25 17:06:43'),
(7, '張至翔', '$2b$10$hmdIulcVcnBuFc6yUh.W/eXsnH/o/vjvRfv52KnSJP65pz9wDKF86', 'a1123321@mail.nuk.edu.tw', 'dreamer', 90, '2026-06-25 17:11:46'),
(8, 'Gongyoo', '$2b$10$.tgFZk.dLxBB789QFepcd.jwErMW0OUS2uzjb0k7cFd1prBRg6sce', 'zz2385103@gmail.com', 'analyst', 0, '2026-06-25 18:36:15');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `dreams`
--
ALTER TABLE `dreams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `dreams`
--
ALTER TABLE `dreams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `dreams`
--
ALTER TABLE `dreams`
  ADD CONSTRAINT `dreams_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
