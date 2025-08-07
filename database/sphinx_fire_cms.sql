-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 05:43 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sphinx_fire_cms`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `determine_ab_test_winner` (IN `p_test_id` CHAR(36))   BEGIN
    DECLARE v_winner VARCHAR(50);
    
    -- Find variant with highest conversion rate (with a minimum sample size)
    SELECT variant_key INTO v_winner
    FROM campaign_ab_test_variants
    WHERE test_id = p_test_id
    AND impressions >= 100 -- Minimum sample size
    ORDER BY conversion_rate DESC
    LIMIT 1;
    
    -- Update the test with the winner if found
    IF v_winner IS NOT NULL THEN
        UPDATE campaign_ab_tests
        SET winning_variant = v_winner,
            status = 'completed'
        WHERE id = p_test_id;
    END IF;
    
    -- You can select the winner to see the result after calling the procedure
    SELECT v_winner;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPageContent` (IN `page_slug` VARCHAR(100), IN `lang_code` VARCHAR(5))   BEGIN
    DECLARE lang_id INT;
    
    -- Get language ID
    SELECT id INTO lang_id FROM languages WHERE code = lang_code AND is_active = TRUE;
    
    -- Get page info
    SELECT 
        p.id,
        p.slug,
        p.template,
        pt.title,
        pt.meta_title,
        pt.meta_description,
        pt.meta_keywords,
        pt.content,
        pt.featured_image,
        pt.og_title,
        pt.og_description,
        pt.og_image
    FROM pages p
    JOIN page_translations pt ON p.id = pt.page_id
    WHERE p.slug = page_slug AND pt.language_id = lang_id AND p.status = 'published';
    
    -- Get page sections
    SELECT 
        s.id,
        s.section_type,
        s.section_key,
        s.sort_order,
        s.settings,
        st.title,
        st.subtitle,
        st.content,
        st.button_text,
        st.button_url,
        st.background_image
    FROM sections s
    JOIN section_translations st ON s.id = st.section_id
    WHERE s.page_id = (SELECT id FROM pages WHERE slug = page_slug)
    AND st.language_id = lang_id
    AND s.is_active = TRUE
    ORDER BY s.sort_order;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `record_ab_test_impression` (IN `p_test_id` CHAR(36), IN `p_variant_key` VARCHAR(50))   BEGIN
    UPDATE campaign_ab_test_variants
    SET impressions = impressions + 1,
        conversion_rate = CASE 
            WHEN (impressions + 1) > 0 THEN (conversions * 100.0) / (impressions + 1)
            ELSE 0
        END
    WHERE test_id = p_test_id AND variant_key = p_variant_key;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TrackEvent` (IN `event_name_val` VARCHAR(100), IN `event_category_val` VARCHAR(50), IN `event_data_val` JSON, IN `page_url_val` VARCHAR(500), IN `user_id_val` INT, IN `session_id_val` VARCHAR(128), IN `ip_address_val` VARCHAR(45), IN `user_agent_val` TEXT)   BEGIN
    INSERT INTO events (
        event_name, event_category, event_data, page_url,
        user_id, session_id, ip_address, user_agent
    ) VALUES (
        event_name_val, event_category_val, event_data_val, page_url_val,
        user_id_val, session_id_val, ip_address_val, user_agent_val
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `track_campaign_conversion` (IN `p_campaign_id` CHAR(36), IN `p_lead_id` CHAR(36), IN `p_event_name` VARCHAR(100), IN `p_event_category` VARCHAR(50), IN `p_event_value` DECIMAL(10,2), IN `p_event_currency` VARCHAR(3), IN `p_event_data` JSON, IN `p_utm_source` VARCHAR(100), IN `p_utm_medium` VARCHAR(100), IN `p_utm_campaign` VARCHAR(100), IN `p_utm_term` VARCHAR(100), IN `p_utm_content` VARCHAR(100), IN `p_page_url` VARCHAR(500), IN `p_referrer` VARCHAR(500), IN `p_user_agent` TEXT, IN `p_ip_address` VARCHAR(45), IN `p_device_type` VARCHAR(20), OUT `v_conversion_id` CHAR(36))   BEGIN
    SET v_conversion_id = UUID();

    INSERT INTO campaign_conversions (
        id, campaign_id, lead_id, event_name, event_category, event_value, event_currency,
        event_data, utm_source, utm_medium, utm_campaign, utm_term, utm_content,
        page_url, referrer, user_agent, ip_address, device_type
    ) VALUES (
        v_conversion_id, p_campaign_id, p_lead_id, p_event_name, p_event_category, p_event_value, p_event_currency,
        p_event_data, p_utm_source, p_utm_medium, p_campaign, p_utm_term, p_utm_content,
        p_page_url, p_referrer, p_user_agent, p_ip_address, p_device_type
    );
    
    -- Update A/B test metrics if applicable
    IF p_event_name = 'Lead' OR p_event_name = 'Purchase' THEN
        UPDATE campaign_ab_test_variants
        SET conversions = conversions + 1,
            conversion_rate = CASE 
                WHEN impressions > 0 THEN (conversions + 1) * 100.0 / impressions
                ELSE 0
            END
        WHERE test_id IN (
            SELECT id FROM campaign_ab_tests 
            WHERE campaign_id = p_campaign_id AND status = 'running'
        )
        AND variant_key = COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(p_event_data, '$.variant')),
            'A' -- Default to A if no variant specified
        );
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdatePageViews` (IN `page_url` VARCHAR(500), IN `referrer_url` VARCHAR(500), IN `user_agent_str` TEXT, IN `ip_addr` VARCHAR(45), IN `session_id_str` VARCHAR(128), IN `user_id_val` INT, IN `device_type_val` ENUM('desktop','mobile','tablet'), IN `browser_val` VARCHAR(50), IN `os_val` VARCHAR(50), IN `country_val` VARCHAR(2), IN `city_val` VARCHAR(100))   BEGIN
    INSERT INTO page_views (
        page_url, referrer, user_agent, ip_address, session_id, 
        user_id, device_type, browser, os, country, city
    ) VALUES (
        page_url, referrer_url, user_agent_str, ip_addr, session_id_str,
        user_id_val, device_type_val, browser_val, os_val, country_val, city_val
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `ab_test_results`
-- (See below for the actual view)
--
CREATE TABLE `ab_test_results` (
`test_id` char(36)
,`campaign_name` varchar(100)
,`test_name` varchar(100)
,`status` enum('draft','running','completed','paused')
,`variant_key` varchar(50)
,`variant_name` varchar(50)
,`is_control` tinyint(1)
,`impressions` int(11)
,`conversions` int(11)
,`conversion_rate` decimal(5,2)
,`winning_variant` varchar(50)
,`start_date` timestamp
,`end_date` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_campaigns`
-- (See below for the actual view)
--
CREATE TABLE `active_campaigns` (
`id` char(36)
,`slug` varchar(100)
,`campaign_name` varchar(100)
,`campaign_type` varchar(50)
,`template` varchar(50)
,`language_id` int(11)
,`title` varchar(255)
,`subtitle` varchar(255)
,`offer_headline` varchar(255)
,`meta_title` varchar(255)
,`meta_description` text
,`start_date` timestamp
,`end_date` timestamp
,`offer_details` longtext
,`conversion_goal` varchar(50)
,`utm_parameters` longtext
,`is_ab_test` tinyint(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_pages`
-- (See below for the actual view)
--
CREATE TABLE `active_pages` (
`id` int(11)
,`slug` varchar(100)
,`template` varchar(50)
,`status` enum('draft','published','archived')
,`language_id` int(11)
,`title` varchar(255)
,`meta_title` varchar(255)
,`meta_description` text
,`content` longtext
,`featured_image` varchar(255)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_services`
-- (See below for the actual view)
--
CREATE TABLE `active_services` (
`id` int(11)
,`slug` varchar(100)
,`category_id` int(11)
,`category_slug` varchar(100)
,`language_id` int(11)
,`name` varchar(100)
,`short_description` text
,`full_description` longtext
,`featured_image` varchar(255)
,`is_featured` tinyint(1)
,`sort_order` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT '#DC2626',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `slug`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'compliance', '#DC2626', 1, 1, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(2, 'safety-tips', '#10B981', 2, 1, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(3, 'case-studies', '#6366F1', 3, 1, '2025-07-24 20:12:12', '2025-07-24 20:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `blog_category_translations`
--

CREATE TABLE `blog_category_translations` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_category_translations`
--

INSERT INTO `blog_category_translations` (`id`, `category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'Compliance', 'Fire safety compliance guides and regulations', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(4, 1, 2, 'الامتثال', 'أدلة الامتثال للسلامة من الحريق واللوائح', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(5, 2, 1, 'Safety Tips', 'Practical fire safety tips for businesses', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(6, 2, 2, 'نصائح السلامة', 'نصائح عملية للسلامة من الحريق للأعمال', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(7, 3, 1, 'Case Studies', 'Real-world fire safety case studies', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(8, 3, 2, 'دراسات حالة', 'دراسات حالة واقعية في السلامة من الحريق', NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `author_id` int(11) NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `reading_time` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `shares_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `category_id`, `slug`, `author_id`, `featured_image`, `reading_time`, `status`, `is_featured`, `views_count`, `likes_count`, `shares_count`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'civil-defense-inspection', 1, 'civil-defense.jpg', 6, 'published', 1, 0, 0, 0, '2025-07-24 20:04:00', '2025-07-24 20:04:00', '2025-07-24 20:12:12'),
(2, 2, 'top-10-fire-safety-tips', 1, 'fire-tips.jpg', 4, 'published', 0, 0, 0, 0, '2025-07-24 20:04:00', '2025-07-24 20:04:00', '2025-07-24 20:12:12'),
(3, 3, 'case-study-warehouse', 1, 'warehouse-case.jpg', 7, 'published', 0, 0, 0, 0, '2025-07-24 20:04:00', '2025-07-24 20:04:00', '2025-07-24 20:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_translations`
--

CREATE TABLE `blog_post_translations` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_post_translations`
--

INSERT INTO `blog_post_translations` (`id`, `post_id`, `language_id`, `title`, `excerpt`, `content`, `tags`, `meta_title`, `meta_description`, `meta_keywords`, `og_title`, `og_description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'How to Pass Civil Defense Inspection in Egypt', 'Step-by-step guide to passing fire safety inspection.', '', '[\"civil defense\",\"inspection\",\"compliance\"]', 'How to Pass Civil Defense Inspection in Egypt', 'Expert guide to passing fire safety inspection in Egypt.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(2, 1, 2, 'كيف تجتاز فحص الدفاع المدني في مصر', 'دليل خطوة بخطوة لاجتياز فحص السلامة من الحريق.', '', '[\"الدفاع المدني\",\"فحص\",\"امتثال\"]', 'كيف تجتاز فحص الدفاع المدني في مصر', 'دليل الخبراء لاجتياز فحص السلامة من الحريق في مصر.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(3, 2, 1, 'Top 10 Fire Safety Tips for Factories', 'Essential tips to prevent fire incidents in factories.', '', '[\"safety\",\"tips\",\"factory\"]', 'Top 10 Fire Safety Tips for Factories', 'Essential fire safety tips for industrial facilities.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(4, 2, 2, 'أفضل 10 نصائح للسلامة من الحريق للمصانع', 'نصائح أساسية لمنع الحريق في المصانع.', '', '[\"سلامة\",\"نصائح\",\"مصنع\"]', 'أفضل 10 نصائح للسلامة من الحريق للمصانع', 'نصائح أساسية للسلامة من الحريق للمنشآت الصناعية.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(5, 3, 1, 'Case Study: Warehouse Fire Prevention', 'How a warehouse avoided disaster with proper safety measures.', '', '[\"case study\",\"warehouse\",\"prevention\"]', 'Case Study: Warehouse Fire Prevention', 'How a warehouse avoided fire disaster.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12'),
(6, 3, 2, 'دراسة حالة: منع حريق في مخزن', 'كيف تجنب مخزن كارثة حريق بإجراءات السلامة.', '', '[\"دراسة حالة\",\"مخزن\",\"منع\"]', 'دراسة حالة: منع حريق في مخزن', 'كيف تجنب مخزن كارثة حريق.', NULL, NULL, NULL, '2025-07-24 20:12:12', '2025-07-24 20:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `cache_key` varchar(255) NOT NULL,
  `cache_value` longtext NOT NULL,
  `expiry_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_ab_tests`
--

CREATE TABLE `campaign_ab_tests` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `campaign_id` char(36) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `status` enum('draft','running','completed','paused') DEFAULT 'draft',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `traffic_split` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`traffic_split`)),
  `winning_variant` varchar(50) DEFAULT NULL,
  `metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metrics`)),
  `created_by` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_ab_test_variants`
--

CREATE TABLE `campaign_ab_test_variants` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `test_id` char(36) NOT NULL,
  `variant_name` varchar(50) NOT NULL,
  `variant_key` varchar(50) NOT NULL,
  `is_control` tinyint(1) DEFAULT 0,
  `content_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_changes`)),
  `impressions` int(11) DEFAULT 0,
  `conversions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_conversions`
--

CREATE TABLE `campaign_conversions` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `campaign_id` char(36) NOT NULL,
  `lead_id` char(36) DEFAULT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_category` varchar(50) DEFAULT NULL,
  `event_value` decimal(10,2) DEFAULT NULL,
  `event_currency` varchar(3) DEFAULT 'EGP',
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `utm_term` varchar(100) DEFAULT NULL,
  `utm_content` varchar(100) DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_landing_pages`
--

CREATE TABLE `campaign_landing_pages` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `slug` varchar(100) NOT NULL,
  `campaign_name` varchar(100) NOT NULL,
  `campaign_type` varchar(50) NOT NULL,
  `template` varchar(50) DEFAULT 'default',
  `status` enum('draft','published','archived','scheduled') DEFAULT 'draft',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `target_audience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_audience`)),
  `offer_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`offer_details`)),
  `conversion_goal` varchar(50) DEFAULT NULL,
  `utm_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`utm_parameters`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_ab_test` tinyint(1) DEFAULT 0,
  `created_by` char(36) NOT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaign_landing_pages`
--

INSERT INTO `campaign_landing_pages` (`id`, `slug`, `campaign_name`, `campaign_type`, `template`, `status`, `start_date`, `end_date`, `target_audience`, `offer_details`, `conversion_goal`, `utm_parameters`, `settings`, `is_ab_test`, `created_by`, `updated_by`, `published_at`, `created_at`, `updated_at`) VALUES
('3695b3b3-68c9-11f0-a302-98fa9b4ad0f5', 'fire-safety-assessment-offer', '20% Off Fire Safety Assessment', 'retargeting', 'discount-offer', 'published', '2025-07-24 20:03:06', '2025-08-23 20:03:06', NULL, NULL, 'lead', NULL, NULL, 0, '00000000-0000-0000-0000-000000000000', NULL, NULL, '2025-07-24 20:03:06', '2025-07-24 20:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_landing_page_translations`
--

CREATE TABLE `campaign_landing_page_translations` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `page_id` char(36) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `hero_content` text DEFAULT NULL,
  `offer_headline` varchar(255) DEFAULT NULL,
  `offer_description` text DEFAULT NULL,
  `benefits_content` text DEFAULT NULL,
  `testimonials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`testimonials`)),
  `faq_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faq_content`)),
  `cta_primary` varchar(100) DEFAULT NULL,
  `cta_secondary` varchar(100) DEFAULT NULL,
  `form_title` varchar(255) DEFAULT NULL,
  `success_message` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaign_landing_page_translations`
--

INSERT INTO `campaign_landing_page_translations` (`id`, `page_id`, `language_id`, `title`, `subtitle`, `hero_content`, `offer_headline`, `offer_description`, `benefits_content`, `testimonials`, `faq_content`, `cta_primary`, `cta_secondary`, `form_title`, `success_message`, `meta_title`, `meta_description`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
('369acc36-68c9-11f0-a302-98fa9b4ad0f5', '3695b3b3-68c9-11f0-a302-98fa9b4ad0f5', 1, 'Special Offer: 20% Off Fire Safety Assessment', 'Protect Your Facility. Ensure Compliance. Save Money.', 'Get a comprehensive fire safety assessment from certified engineers.', 'Save 20% on Professional Fire Safety Assessment', 'Limited time offer: Act now and save 20% on our comprehensive fire safety assessment.', NULL, NULL, NULL, 'CLAIM YOUR 20% DISCOUNT NOW', NULL, 'Book Your Discounted Assessment', 'Thank you! Your discount has been claimed. Our team will contact you within 4 hours to schedule your assessment.', 'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire', 'Limited time offer: 20% off professional fire safety assessment for industrial facilities. Certified engineers, same-day response, and compliance guarantee.', NULL, NULL, NULL, '2025-07-24 20:03:06', '2025-07-24 20:03:06'),
('369adb53-68c9-11f0-a302-98fa9b4ad0f5', '3695b3b3-68c9-11f0-a302-98fa9b4ad0f5', 2, 'عرض خاص: خصم 20% على تقييم السلامة من الحريق', 'احمِ منشأتك. ضمان الامتثال. وفر المال.', 'احصل على تقييم شامل للسلامة من الحريق من مهندسين معتمدين.', 'وفر 20% على تقييم السلامة من الحريق الاحترافي', 'عرض لفترة محدودة: اغتنم الفرصة الآن ووفر 20% على تقييم السلامة الشامل.', NULL, NULL, NULL, 'احصل على خصم 20% الآن', NULL, 'احجز تقييمك المخفض', 'شكرًا لك! تم المطالبة بالخصم الخاص بك. سيتصل بك فريقنا خلال 4 ساعات لتحديد موعد التقييم.', 'عرض خاص: خصم 20% على تقييم السلامة من الحريق | سفينكس فاير', 'عرض لفترة محدودة: خصم 20% على تقييم السلامة من الحريق الاحترافي للمنشآت الصناعية. مهندسون معتمدون واستجابة في نفس اليوم وضمان الامتثال.', NULL, NULL, NULL, '2025-07-24 20:03:06', '2025-07-24 20:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_leads`
--

CREATE TABLE `campaign_leads` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `campaign_id` char(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `facility_type` varchar(100) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_urgent` tinyint(1) DEFAULT 0,
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `utm_term` varchar(100) DEFAULT NULL,
  `utm_content` varchar(100) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `landing_page` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `status` enum('new','contacted','qualified','converted','lost') DEFAULT 'new',
  `assigned_to` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `campaign_performance`
-- (See below for the actual view)
--
CREATE TABLE `campaign_performance` (
`id` char(36)
,`campaign_name` varchar(100)
,`campaign_type` varchar(50)
,`conversion_goal` varchar(50)
,`total_leads` bigint(21)
,`converted_leads` bigint(21)
,`page_views` bigint(21)
,`form_submissions` bigint(21)
,`conversion_rate` decimal(26,2)
,`start_date` timestamp
,`end_date` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `contact_forms`
--

CREATE TABLE `contact_forms` (
  `id` int(11) NOT NULL,
  `form_name` varchar(100) NOT NULL,
  `form_key` varchar(100) NOT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fields`)),
  `email_recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`email_recipients`)),
  `success_message` text DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_forms`
--

INSERT INTO `contact_forms` (`id`, `form_name`, `form_key`, `fields`, `email_recipients`, `success_message`, `redirect_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General Contact', 'general_contact', '[{\"name\":\"name\",\"type\":\"text\",\"required\":true,\"label\":\"Full Name\"},{\"name\":\"email\",\"type\":\"email\",\"required\":true,\"label\":\"Email\"},{\"name\":\"phone\",\"type\":\"tel\",\"required\":false,\"label\":\"Phone\"},{\"name\":\"company\",\"type\":\"text\",\"required\":false,\"label\":\"Company\"},{\"name\":\"message\",\"type\":\"textarea\",\"required\":true,\"label\":\"Message\"}]', '[\"info@sphinxfire.com\"]', 'Thank you for your message. We will contact you within 24 hours.', NULL, 1, '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(2, 'Site Visit Request', 'site_visit', '[{\"name\":\"name\",\"type\":\"text\",\"required\":true,\"label\":\"Full Name\"},{\"name\":\"company\",\"type\":\"text\",\"required\":true,\"label\":\"Company Name\"},{\"name\":\"email\",\"type\":\"email\",\"required\":true,\"label\":\"Email\"},{\"name\":\"phone\",\"type\":\"tel\",\"required\":true,\"label\":\"Phone\"},{\"name\":\"facility_type\",\"type\":\"select\",\"required\":true,\"label\":\"Facility Type\",\"options\":[\"Manufacturing\",\"Warehouse\",\"Chemical\",\"Retail\",\"Office\",\"Other\"]},{\"name\":\"location\",\"type\":\"text\",\"required\":true,\"label\":\"Location\"},{\"name\":\"urgent\",\"type\":\"checkbox\",\"required\":false,\"label\":\"Urgent Request\"}]', '[\"sales@sphinxfire.com\",\"info@sphinxfire.com\"]', 'Your site visit request has been submitted. Our team will contact you within 24 hours to schedule the visit.', NULL, 1, '2025-07-21 21:10:41', '2025-07-21 21:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `status` enum('new','read','replied','closed') DEFAULT 'new',
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_category` varchar(50) DEFAULT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `page_url` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `native_name` varchar(50) NOT NULL,
  `direction` enum('ltr','rtl') DEFAULT 'ltr',
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `native_name`, `direction`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'en', 'English', 'English', 'ltr', 1, 1, '2025-07-21 21:10:40', '2025-07-21 21:10:40'),
(2, 'ar', 'Arabic', 'العربية', 'rtl', 1, 0, '2025-07-21 21:10:40', '2025-07-21 21:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `source` varchar(100) DEFAULT NULL,
  `campaign` varchar(100) DEFAULT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `service_interest` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `budget_range` varchar(50) DEFAULT NULL,
  `urgency` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('new','contacted','qualified','proposal','won','lost') DEFAULT 'new',
  `assigned_to` int(11) DEFAULT NULL,
  `estimated_value` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `type` enum('city','region','industrial_zone') DEFAULT 'city',
  `coordinates` point DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `slug`, `type`, `coordinates`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'sadat-city', 'industrial_zone', NULL, 1, 1, '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(2, 'october-city', 'industrial_zone', NULL, 1, 2, '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(3, 'quesna', 'industrial_zone', NULL, 1, 3, '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(4, 'badr-city', 'industrial_zone', NULL, 1, 4, '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(5, '10th-ramadan', 'industrial_zone', NULL, 1, 5, '2025-07-21 21:10:41', '2025-07-21 21:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `location_content`
--

CREATE TABLE `location_content` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `content_type` enum('advantage','service_highlight','testimonial','project_example') NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_content_translations`
--

CREATE TABLE `location_content_translations` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `additional_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_translations`
--

CREATE TABLE `location_translations` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `local_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `location_translations`
--

INSERT INTO `location_translations` (`id`, `location_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `local_keywords`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Sadat Industrial City', 'Fire protection services for Sadat Industrial City', 'Fire Protection Sadat City - Sphinx Fire', 'Professional fire safety services in Sadat Industrial City, Egypt', 'fire protection Sadat City, firefighting systems Sadat industrial zone', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(2, 1, 2, 'مدينة السادات الصناعية', 'خدمات الحماية من الحريق لمدينة السادات الصناعية', 'الحماية من الحريق مدينة السادات - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في مدينة السادات الصناعية، مصر', 'الحماية من الحريق مدينة السادات، أنظمة مكافحة الحريق المنطقة الصناعية السادات', '2025-07-21 21:10:41', '2025-07-21 21:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_log`
--

CREATE TABLE `maintenance_log` (
  `id` int(11) NOT NULL,
  `task_type` enum('backup','optimization','update','cleanup','security') NOT NULL,
  `task_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','running','completed','failed') DEFAULT 'pending',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `url` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `folder` varchar(100) DEFAULT 'uploads',
  `is_optimized` tinyint(1) DEFAULT 0,
  `optimization_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`optimization_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_translations`
--

CREATE TABLE `media_translations` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `template` varchar(50) DEFAULT 'default',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_homepage` tinyint(1) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `template`, `status`, `is_homepage`, `parent_id`, `sort_order`, `created_by`, `updated_by`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'home', 'default', 'published', 1, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(2, 'about', 'default', 'published', 0, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(3, 'services', 'default', 'published', 0, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-25 02:31:22'),
(4, 'projects', 'default', 'published', 0, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-25 02:48:23'),
(5, 'blog', 'default', 'published', 0, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(6, 'contact', 'default', 'published', 0, NULL, 0, 1, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(60, 'blog-article', 'default', 'draft', 0, NULL, 0, 1, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(70, 'project-article', 'default', 'draft', 0, NULL, 0, 1, NULL, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(71, 'service-article', 'default', 'draft', 0, NULL, 0, 1, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11');

--
-- Triggers `pages`
--
DELIMITER $$
CREATE TRIGGER `update_pages_timestamp` BEFORE UPDATE ON `pages` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `page_translations`
--

CREATE TABLE `page_translations` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_translations`
--

INSERT INTO `page_translations` (`id`, `page_id`, `language_id`, `title`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `excerpt`, `featured_image`, `og_title`, `og_description`, `og_image`, `canonical_url`, `created_at`, `updated_at`) VALUES
(7, 4, 1, 'Projects', 'Projects', 'Explore our completed fire safety projects.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(8, 4, 2, 'المشاريع', 'المشاريع', 'تصفح مشاريعنا المنجزة في مجال السلامة من الحريق.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(9, 5, 1, 'Blog', 'Blog', 'Fire safety insights, tips, and news.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(10, 5, 2, 'المدونة', 'المدونة', 'مقالات ونصائح وأخبار عن السلامة من الحريق.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(11, 6, 1, 'Contact', 'Contact', 'Contact Sphinx Fire for fire safety consultation.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00'),
(12, 6, 2, 'تواصل معنا', 'تواصل معنا', 'اتصل بسفينكس فاير لاستشارة في السلامة من الحريق.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-24 21:56:00', '2025-07-24 21:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE `page_views` (
  `id` int(11) NOT NULL,
  `page_url` varchar(500) NOT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet') DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `page_views`
--
DELIMITER $$
CREATE TRIGGER `update_blog_views` AFTER INSERT ON `page_views` FOR EACH ROW BEGIN
    IF NEW.page_url LIKE '/blog/%' THEN
        UPDATE blog_posts 
        SET views_count = views_count + 1 
        WHERE CONCAT('/blog/', slug) = NEW.page_url;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `performance_metrics`
--

CREATE TABLE `performance_metrics` (
  `id` int(11) NOT NULL,
  `page_url` varchar(500) NOT NULL,
  `metric_type` enum('lcp','fid','cls','ttfb','load_time') NOT NULL,
  `value` decimal(10,3) NOT NULL,
  `device_type` enum('desktop','mobile','tablet') DEFAULT 'desktop',
  `user_agent` text DEFAULT NULL,
  `connection_type` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_logo` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `budget_range` varchar(50) DEFAULT NULL,
  `status` enum('completed','ongoing','planned') DEFAULT 'completed',
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `services_provided` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`services_provided`)),
  `team_size` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `category_id`, `slug`, `client_name`, `client_logo`, `location`, `project_date`, `duration_days`, `budget_range`, `status`, `featured_image`, `gallery`, `services_provided`, `team_size`, `is_featured`, `is_published`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'delta-paint', 'Delta Paint Manufacturing', NULL, 'Sadat City', '2024-01-15', NULL, NULL, 'completed', 'delta-paint.jpg', NULL, NULL, NULL, 1, 1, 0, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 2, 'city-center-mall', 'City Center Mall', NULL, 'Cairo', '2023-09-10', NULL, NULL, 'completed', 'city-center-mall.jpg', NULL, NULL, NULL, 0, 1, 0, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 3, 'petrochem-suez', 'Petrochemical Complex', NULL, 'Suez', '2024-03-05', NULL, NULL, 'completed', 'petrochem-suez.jpg', NULL, NULL, NULL, 1, 1, 0, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `project_categories`
--

CREATE TABLE `project_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT '#DC2626',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_categories`
--

INSERT INTO `project_categories` (`id`, `slug`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'industrial', '#DC2626', 1, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 'commercial', '#EF4444', 2, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 'chemical', '#F59E42', 3, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `project_category_translations`
--

CREATE TABLE `project_category_translations` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_category_translations`
--

INSERT INTO `project_category_translations` (`id`, `category_id`, `language_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Industrial', 'Industrial fire safety projects', '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 1, 2, 'صناعي', 'مشاريع حماية صناعية من الحريق', '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 2, 1, 'Commercial', 'Commercial and retail fire safety projects', '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(4, 2, 2, 'تجاري', 'مشاريع حماية تجارية ومولات', '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(5, 3, 1, 'Chemical', 'Chemical plant fire safety projects', '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(6, 3, 2, 'كيميائي', 'مشاريع حماية مصانع كيميائية', '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `project_translations`
--

CREATE TABLE `project_translations` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `challenge` text DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `results` text DEFAULT NULL,
  `testimonial` text DEFAULT NULL,
  `testimonial_author` varchar(100) DEFAULT NULL,
  `testimonial_position` varchar(100) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_translations`
--

INSERT INTO `project_translations` (`id`, `project_id`, `language_id`, `title`, `subtitle`, `description`, `challenge`, `solution`, `results`, `testimonial`, `testimonial_author`, `testimonial_position`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Delta Paint Manufacturing', 'Complete Fire Network + Foam Suppression', 'UL/FM certified firefighting system with specialized foam suppression for paint storage areas.', 'Paint storage required special foam system.', 'Installed UL/FM foam system.', 'Passed civil defense inspection on first try.', 'Sphinx Fire handled everything with precision and speed.', 'Ahmed Hassan', 'Safety Manager', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58'),
(2, 1, 2, 'مصنع دلتا للدهانات', 'شبكة مكافحة كاملة + نظام رغوي', 'نظام مكافحة حريق معتمد UL/FM مع نظام رغوي خاص لمخازن الدهانات.', 'مخزن الدهانات تطلب نظام رغوي خاص.', 'تم تركيب نظام رغوي معتمد UL/FM.', 'اجتياز فحص الدفاع المدني من أول مرة.', 'سفينكس فاير أنجزت كل شيء بدقة وسرعة.', 'أحمد حسن', 'مدير السلامة', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58'),
(3, 2, 1, 'City Center Mall - Cairo', 'Comprehensive Fire Safety', 'Complete fire protection system for 5-story shopping complex with evacuation systems.', 'Complex evacuation planning for large crowds.', 'Installed advanced alarm and evacuation system.', 'Zero incidents since installation.', 'Professional, fast, and reliable.', 'Fatma Nour', 'Operations Director', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58'),
(4, 2, 2, 'سيتي سنتر مول - القاهرة', 'حماية شاملة للمول', 'نظام حماية متكامل لمجمع تسوق من 5 طوابق مع أنظمة إخلاء.', 'تخطيط إخلاء معقد لأعداد كبيرة.', 'تركيب نظام إنذار وإخلاء متقدم.', 'لا حوادث منذ التركيب.', 'احترافية وسرعة وموثوقية.', 'فاطمة نور', 'مدير العمليات', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58'),
(5, 3, 1, 'Petrochemical Complex - Suez', 'High-Risk Deluge System', 'Advanced deluge system with SCADA monitoring for chemical processing facility.', 'High-risk chemical storage required special deluge system.', 'Installed deluge system with SCADA integration.', 'Passed all safety audits.', 'Support always available.', 'Mohamed El-Shamy', 'Operations Manager', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58'),
(6, 3, 2, 'مجمع بتروكيمياويات - السويس', 'نظام ديلوج عالي الخطورة', 'نظام ديلوج متقدم مع مراقبة SCADA لمصنع كيميائي.', 'تخزين كيميائي عالي الخطورة تطلب نظام ديلوج خاص.', 'تركيب نظام ديلوج مع تكامل SCADA.', 'اجتياز جميع اختبارات السلامة.', 'الدعم متوفر دائمًا.', 'محمد الشامي', 'مدير العمليات', NULL, NULL, NULL, '2025-07-24 20:09:58', '2025-07-24 20:09:58');

-- --------------------------------------------------------

--
-- Stand-in structure for view `published_blog_posts`
-- (See below for the actual view)
--
CREATE TABLE `published_blog_posts` (
`id` int(11)
,`slug` varchar(100)
,`category_id` int(11)
,`category_slug` varchar(100)
,`language_id` int(11)
,`title` varchar(255)
,`excerpt` text
,`content` longtext
,`featured_image` varchar(255)
,`reading_time` int(11)
,`views_count` int(11)
,`published_at` timestamp
,`author_first_name` varchar(50)
,`author_last_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section_type` varchar(50) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `page_id`, `section_type`, `section_key`, `sort_order`, `is_active`, `settings`, `created_at`, `updated_at`) VALUES
(1, 1, 'hero', 'home-hero-slide-1', 1, 1, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(2, 1, 'hero', 'home-hero-slide-2', 2, 1, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(3, 1, 'hero', 'home-hero-slide-3', 3, 1, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(7, 2, 'about_value', 'about-vision', 2, 1, NULL, '2025-07-24 22:10:29', '2025-07-25 02:23:41'),
(8, 2, 'about_value', 'about-mission', 3, 1, NULL, '2025-07-24 22:10:29', '2025-07-25 02:23:41'),
(11, 4, 'hero', 'projects-hero', 1, 1, NULL, '2025-07-24 21:56:32', '2025-07-24 21:56:32'),
(12, 5, 'hero', 'blog-hero', 1, 1, NULL, '2025-07-24 21:56:32', '2025-07-24 21:56:32'),
(13, 6, 'hero', 'contact-hero', 1, 1, NULL, '2025-07-24 21:56:33', '2025-07-24 21:56:33'),
(16, 2, 'overview', 'about-overview', 1, 1, NULL, '2025-07-25 02:23:41', '2025-07-25 02:23:41'),
(19, 2, 'about_value', 'about-values', 4, 1, NULL, '2025-07-25 02:23:41', '2025-07-25 02:23:41'),
(20, 2, 'process_step', 'about-process-step-1', 5, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(21, 2, 'process_step', 'about-process-step-2', 6, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(22, 2, 'process_step', 'about-process-step-3', 7, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(23, 2, 'process_step', 'about-process-step-4', 8, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(24, 2, 'process_step', 'about-process-step-5', 9, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(25, 2, 'process_step', 'about-process-step-6', 10, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(26, 2, 'partners', 'about-partners', 11, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(27, 2, 'certifications', 'about-certifications', 12, 1, NULL, '2025-07-25 02:26:46', '2025-07-25 02:26:46'),
(30, 3, 'hero', 'services-hero', 1, 1, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(31, 3, 'services', 'services-grid', 2, 1, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(32, 3, 'advantage', 'services-advantage-strip', 3, 1, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(33, 3, 'technical', 'services-technical-details', 4, 1, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(34, 3, 'faq', 'services-faq', 5, 1, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(35, 3, 'cta', 'services-conversion', 6, 1, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(41, 4, 'stats', 'projects-stats', 2, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(42, 4, 'filter', 'projects-filter', 3, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(43, 4, 'projects', 'projects-grid', 4, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(44, 4, 'highlight', 'projects-highlighted', 5, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(45, 4, 'cta', 'projects-cta-strip', 6, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(51, 5, 'filter', 'blog-search-filter', 2, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(52, 5, 'featured', 'blog-featured', 3, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(53, 5, 'articles', 'blog-articles-grid', 4, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(54, 5, 'cta', 'blog-newsletter-cta', 5, 1, NULL, '2025-07-25 02:46:23', '2025-07-25 02:46:23'),
(55, 6, 'options', 'contact-options', 2, 1, NULL, '2025-07-25 03:00:10', '2025-07-25 03:00:10'),
(56, 6, 'form', 'contact-form', 3, 1, NULL, '2025-07-25 03:00:10', '2025-07-25 03:00:10'),
(57, 6, 'location', 'contact-location', 4, 1, NULL, '2025-07-25 03:00:10', '2025-07-25 03:00:10'),
(58, 6, 'cta', 'contact-final-cta', 5, 1, NULL, '2025-07-25 03:00:10', '2025-07-25 03:00:10'),
(63, 60, 'hero', 'blog-article-hero', 1, 1, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(64, 60, 'cta', 'blog-article-cta', 2, 1, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(65, 60, 'highlight', 'blog-article-highlight', 3, 1, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(66, 60, 'final-cta', 'blog-article-final-cta', 4, 1, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(67, 70, 'hero', 'project-article-hero', 1, 1, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(68, 70, 'cta', 'project-article-cta', 2, 1, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(69, 70, 'highlight', 'project-article-highlight', 3, 1, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(70, 70, 'final-cta', 'project-article-final-cta', 4, 1, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(71, 71, 'hero', 'service-article-hero', 1, 1, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(72, 71, 'cta', 'service-article-cta', 2, 1, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(73, 71, 'highlight', 'service-article-highlight', 3, 1, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(74, 71, 'final-cta', 'service-article-final-cta', 4, 1, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11');

-- --------------------------------------------------------

--
-- Table structure for table `section_translations`
--

CREATE TABLE `section_translations` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_translations`
--

INSERT INTO `section_translations` (`id`, `section_id`, `language_id`, `title`, `subtitle`, `content`, `button_text`, `button_url`, `background_image`, `created_at`, `updated_at`) VALUES
(31, 11, 1, 'Real Safety. Real Sites. Real Impact.', 'Explore our recent projects and see how we bring industrial safety to life.', 'From design to installation to approval - witness our proven track record across Egypt\'s industrial facilities.', 'View All Projects', NULL, NULL, '2025-07-24 21:56:32', '2025-07-25 02:56:47'),
(32, 11, 2, 'سلامة حقيقية. مواقع حقيقية. تأثير حقيقي.', 'استكشف مشاريعنا الأخيرة وشاهد كيف نجسد السلامة الصناعية.', 'من التصميم إلى التركيب إلى الاعتماد - شاهد سجلنا المثبت في منشآت مصر الصناعية.', 'عرض كل المشاريع', NULL, NULL, '2025-07-24 21:56:32', '2025-07-25 02:56:47'),
(33, 12, 1, 'Insights That Keep Your Facility Safe', 'Expert fire protection guides, updates, and industrial safety knowledge.', 'Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.', 'Read Latest Articles', NULL, NULL, '2025-07-24 21:56:32', '2025-07-25 02:57:14'),
(34, 12, 2, 'رؤى تحافظ على سلامة منشأتك', 'أدلة ونصائح خبراء الحماية من الحريق والمعرفة الصناعية.', 'ابقَ على اطلاع دائم على اللوائح مع نصائح مهندسي وخبراء الحماية من الحريق المعتمدين.', 'اقرأ أحدث المقالات', NULL, NULL, '2025-07-24 21:56:33', '2025-07-25 02:57:14'),
(35, 13, 1, 'Let\'s Talk Safety.', 'We\'re Just a Call Away.', 'We respond fast because your facility can\'t wait. Contact Sphinx Fire today.', 'Book Free Visit', NULL, NULL, '2025-07-24 21:56:33', '2025-07-24 21:56:33'),
(36, 13, 2, 'دعنا نتحدث عن السلامة.', 'نحن على بعد مكالمة واحدة فقط.', 'نستجيب بسرعة لأن منشأتك لا تحتمل الانتظار. تواصل مع سفينكس فاير اليوم.', 'احجز زيارة مجانية', NULL, NULL, '2025-07-24 21:56:33', '2025-07-25 03:02:32'),
(37, 7, 1, 'Vision', NULL, 'To lead Egypt\'s industrial safety transformation with smart, reliable, and integrated systems.', NULL, NULL, NULL, '2025-07-24 22:10:29', '2025-07-24 22:10:29'),
(38, 8, 1, 'Mission', NULL, 'Deliver complete fire safety solutions that combine engineering excellence, fast response, and compliance.', NULL, NULL, NULL, '2025-07-24 22:10:29', '2025-07-24 22:10:29'),
(39, 7, 2, 'الرؤية', NULL, 'قيادة تحول السلامة الصناعية في مصر بأنظمة ذكية وموثوقة ومتكاملة.', NULL, NULL, NULL, '2025-07-24 22:10:29', '2025-07-24 22:10:29'),
(40, 8, 2, 'الرسالة', NULL, 'تقديم حلول سلامة متكاملة تجمع بين الهندسة والسرعة والامتثال.', NULL, NULL, NULL, '2025-07-24 22:10:29', '2025-07-24 22:10:29'),
(41, 16, 1, 'Built for Industry. Backed by Experience.', NULL, 'Based in the heart of Egypt\'s industrial zone in Sadat City, Sphinx Fire has emerged as the leading provider of comprehensive fire safety solutions for industrial facilities across the region. Our specialty lies in the complete spectrum of fire safety—from initial consultation and risk assessment to system design, certified equipment supply, professional installation, and ongoing maintenance. Led by certified engineers and safety consultants with decades of combined experience, we serve B2B clients across diverse industries including manufacturing plants, chemical facilities, warehouses, shopping centers, and high-risk industrial operations. Every system we design and install is backed by international certifications and compliance with the highest safety standards, ensuring your facility is protected and your business is secure.', NULL, NULL, NULL, '2025-07-25 02:23:41', '2025-07-25 02:23:41'),
(42, 16, 2, 'مصمم للصناعة. مدعوم بالخبرة.', NULL, 'يقع مقر سفينكس فاير في قلب المنطقة الصناعية بمدينة السادات، وقد أصبحت الشركة المزود الرائد لحلول السلامة من الحريق للمنشآت الصناعية في المنطقة. تتخصص الشركة في جميع جوانب السلامة من الحريق بدءًا من الاستشارات والتقييم وحتى التصميم والتوريد والتركيب والصيانة الدورية. يقود الفريق مهندسون ومستشارون معتمدون بخبرة عقود، ونخدم عملاء B2B في قطاعات متنوعة مثل المصانع والمنشآت الكيميائية والمخازن والمراكز التجارية والعمليات الصناعية عالية الخطورة. كل نظام نصممه ونركبه مدعوم بشهادات دولية وامتثال لأعلى معايير السلامة لضمان حماية منشأتك وأمان عملك.', NULL, NULL, NULL, '2025-07-25 02:23:41', '2025-07-25 02:23:41'),
(47, 30, 1, 'Complete Fire Safety Services', NULL, 'From design to installation to maintenance - we deliver comprehensive protection solutions.', NULL, NULL, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(48, 30, 2, 'خدمات السلامة من الحريق المتكاملة', NULL, 'من التصميم إلى التركيب إلى الصيانة - نقدم حلول حماية شاملة.', NULL, NULL, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(49, 31, 1, 'Complete Fire Safety Solutions', NULL, 'Every system designed, installed, and maintained by certified experts.', NULL, NULL, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(50, 31, 2, 'حلول السلامة من الحريق المتكاملة', NULL, 'كل نظام يتم تصميمه وتركيبه وصيانته بواسطة خبراء معتمدين.', NULL, NULL, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(51, 32, 1, 'Smart Integration. Proven Results.', NULL, 'UL/FM Certified, SCADA & BMS Integrated, Custom for High-Risk Facilities, Full Extinguisher Library, Expert Consultation Team.', NULL, NULL, NULL, '2025-07-25 02:31:22', '2025-07-25 02:31:22'),
(52, 32, 2, 'تكامل ذكي. نتائج مثبتة.', NULL, 'معتمد UL/FM، تكامل SCADA وBMS، مخصص للمنشآت عالية الخطورة، مكتبة طفايات كاملة، فريق استشاري خبير.', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(53, 33, 1, 'Explore the Components Behind Each System', NULL, 'Technical specifications and detailed breakdowns for engineering teams.', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(54, 33, 2, 'استكشف مكونات كل نظام', NULL, 'المواصفات الفنية والتفاصيل الهندسية لفِرق العمل.', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(55, 34, 1, 'Frequently Asked Questions', NULL, 'How long does installation take? What if I already have partial systems? Do you offer training? Is civil defense approval included?', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(56, 34, 2, 'الأسئلة الشائعة', NULL, 'كم يستغرق التركيب؟ ماذا لو كان لدي أنظمة جزئية بالفعل؟ هل تقدمون تدريبًا؟ هل يشمل الموافقة من الدفاع المدني؟', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(57, 35, 1, 'Every day without protection increases your risk. Let\'s fix that.', NULL, 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety solutions. Response within 24 hours • Free consultation • No obligation.', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(58, 35, 2, 'كل يوم بدون حماية يزيد من المخاطر. دعنا نصلح ذلك.', NULL, 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير لحلول السلامة من الحريق الكاملة. استجابة خلال 24 ساعة • استشارة مجانية • بدون التزام.', NULL, NULL, NULL, '2025-07-25 02:31:23', '2025-07-25 02:31:23'),
(82, 55, 1, 'Multiple Ways to Reach Us', 'Choose the method that works best for your urgent needs', NULL, NULL, NULL, NULL, '2025-07-25 03:01:11', '2025-07-25 03:01:11'),
(83, 56, 1, 'Request a Callback or Consultation', 'Fill out this form and our expert team will contact you within 24 hours to discuss your fire safety requirements and schedule a free site assessment.', NULL, NULL, NULL, NULL, '2025-07-25 03:01:11', '2025-07-25 03:01:11'),
(84, 57, 1, 'Find Us in Sadat City', 'Strategically located in Egypt\'s industrial hub for fast response', NULL, NULL, NULL, NULL, '2025-07-25 03:01:11', '2025-07-25 03:01:11'),
(85, 58, 1, 'We\'re based inside the zone. We\'re closer than you think.', 'Don\'t wait for an emergency. Get professional fire safety assessment today.', NULL, NULL, NULL, NULL, '2025-07-25 03:01:11', '2025-07-25 03:01:11'),
(87, 55, 2, 'طرق متعددة للتواصل معنا', 'اختر الطريقة الأنسب لاحتياجاتك العاجلة', NULL, NULL, NULL, NULL, '2025-07-25 03:02:32', '2025-07-25 03:02:32'),
(88, 56, 2, 'اطلب مكالمة أو استشارة', 'املأ هذا النموذج وسيتواصل معك فريقنا المتخصص خلال 24 ساعة لمناقشة متطلبات السلامة من الحريق وتحديد موعد لتقييم مجاني للموقع.', NULL, NULL, NULL, NULL, '2025-07-25 03:02:32', '2025-07-25 03:02:32'),
(89, 57, 2, 'ابحث عنا في مدينة السادات', 'موجودون استراتيجيًا في قلب المنطقة الصناعية في مصر لاستجابة سريعة', NULL, NULL, NULL, NULL, '2025-07-25 03:02:32', '2025-07-25 03:02:32'),
(90, 58, 2, 'نحن في قلب المنطقة الصناعية. أقرب مما تتوقع.', 'لا تنتظر حالة طوارئ. احصل على تقييم احترافي للسلامة من الحريق اليوم.', NULL, NULL, NULL, NULL, '2025-07-25 03:02:32', '2025-07-25 03:02:32'),
(91, 63, 1, 'Article Title', 'Compliance Guide', 'This is a dynamic blog article page. Content is loaded from blog_posts.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(92, 63, 2, 'عنوان المقال', 'دليل الامتثال', 'هذه صفحة مقالة ديناميكية. يتم تحميل المحتوى من جدول blog_posts.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(93, 64, 1, 'Want a Free Pre-Inspection Consultation?', NULL, 'Our certified consultants can assess your facility and identify potential issues before the official inspection.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(94, 64, 2, 'هل تريد استشارة مجانية قبل الفحص؟', NULL, 'يمكن لمستشارينا المعتمدين تقييم منشأتك وتحديد المشكلات المحتملة قبل الفحص الرسمي.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(95, 65, 1, 'Critical Statistic', NULL, 'Over 40% of factories fail their first inspection due to missing technical documents and inadequate fire suppression systems.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(96, 65, 2, 'إحصائية حرجة', NULL, 'أكثر من 40% من المصانع تفشل في الفحص الأول بسبب نقص المستندات الفنية وأنظمة الإطفاء غير الكافية.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(97, 66, 1, 'Need Help Preparing for Your Civil Defense Inspection?', NULL, 'Our certified consultants ensure you pass on the first try. Free assessment • Expert guidance • 24-hour response guarantee.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(98, 66, 2, 'تحتاج مساعدة في التحضير لفحص الدفاع المدني؟', NULL, 'يضمن لك مستشارونا المعتمدون النجاح من أول مرة. تقييم مجاني • إرشاد خبير • استجابة خلال 24 ساعة.', NULL, NULL, NULL, '2025-07-25 03:12:22', '2025-07-25 03:12:22'),
(99, 67, 1, 'Project Title', 'Project Category', 'This is a dynamic project article page. Content is loaded from projects table.', NULL, NULL, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(100, 67, 2, 'عنوان المشروع', 'تصنيف المشروع', 'هذه صفحة مشروع ديناميكية. يتم تحميل المحتوى من جدول المشاريع.', NULL, NULL, NULL, '2025-07-25 03:14:38', '2025-07-25 03:14:38'),
(101, 68, 1, 'Want a Similar Project?', NULL, 'Contact us to discuss your requirements and get a custom solution.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(102, 68, 2, 'تريد مشروعًا مماثلًا؟', NULL, 'تواصل معنا لمناقشة متطلباتك والحصول على حل مخصص.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(103, 69, 1, 'Project Highlights', NULL, 'Key achievements, certifications, and client feedback.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(104, 69, 2, 'أهم إنجازات المشروع', NULL, 'الإنجازات الرئيسية، الشهادات، وآراء العميل.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(105, 70, 1, 'Ready to Start Your Project?', NULL, 'Get in touch for a free consultation and site assessment.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(106, 70, 2, 'جاهز لبدء مشروعك؟', NULL, 'تواصل معنا لاستشارة مجانية وتقييم موقعك.', NULL, NULL, NULL, '2025-07-25 03:14:39', '2025-07-25 03:14:39'),
(107, 71, 1, 'Service Title', 'Service Category', 'This is a dynamic service article page. Content is loaded from services table.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(108, 71, 2, 'عنوان الخدمة', 'تصنيف الخدمة', 'هذه صفحة خدمة ديناميكية. يتم تحميل المحتوى من جدول الخدمات.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(109, 72, 1, 'Need This Service?', NULL, 'Contact us to discuss your requirements and get a custom solution.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(110, 72, 2, 'تحتاج هذه الخدمة؟', NULL, 'تواصل معنا لمناقشة متطلباتك والحصول على حل مخصص.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(111, 73, 1, 'Service Highlights', NULL, 'Key features, certifications, and client benefits.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(112, 73, 2, 'أهم مميزات الخدمة', NULL, 'المميزات الرئيسية، الشهادات، وفوائد العميل.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(113, 74, 1, 'Ready to Book This Service?', NULL, 'Get in touch for a free consultation and service assessment.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(114, 74, 2, 'جاهز لحجز الخدمة؟', NULL, 'تواصل معنا لاستشارة مجانية وتقييم الخدمة.', NULL, NULL, NULL, '2025-07-25 03:16:11', '2025-07-25 03:16:11'),
(115, 1, 1, 'Fire Protection Is Not a Product.', 'It\'s a System. It\'s a Promise.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get a Quote', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(116, 1, 2, 'الحماية من الحريق ليست منتجًا', 'إنها نظام. إنها وعد.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(117, 2, 1, 'Early Detection Saves Lives.', 'Smart Alarm Systems That Never Sleep.', 'Advanced detection technology with SCADA integration for industrial facilities. 24/7 monitoring and instant alerts.', 'Request Demo', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(118, 2, 2, 'الكشف المبكر ينقذ الأرواح', 'أنظمة إنذار ذكية لا تنام أبدًا', 'تقنية كشف متقدمة مع تكامل SCADA للمنشآت الصناعية. مراقبة 24/7 وتنبيهات فورية.', 'طلب عرض توضيحي', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(119, 3, 1, 'Protect Your Most Valuable Asset.', 'Your People.', 'Complete PPE solutions for industrial fire safety teams. Professional-grade equipment with certified training.', 'Training Programs', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08'),
(120, 3, 2, 'احمِ أثمن أصولك', 'أشخاصك', 'حلول معدات حماية شخصية متكاملة لفرق السلامة الصناعية. معدات احترافية مع تدريب معتمد.', 'برامج التدريب', NULL, NULL, '2025-07-25 03:38:08', '2025-07-25 03:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `seo_metrics`
--

CREATE TABLE `seo_metrics` (
  `id` int(11) NOT NULL,
  `page_url` varchar(500) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `title_length` int(11) DEFAULT NULL,
  `meta_description_length` int(11) DEFAULT NULL,
  `h1_count` int(11) DEFAULT NULL,
  `h2_count` int(11) DEFAULT NULL,
  `image_count` int(11) DEFAULT NULL,
  `images_without_alt` int(11) DEFAULT NULL,
  `internal_links` int(11) DEFAULT NULL,
  `external_links` int(11) DEFAULT NULL,
  `word_count` int(11) DEFAULT NULL,
  `readability_score` decimal(5,2) DEFAULT NULL,
  `lighthouse_seo_score` int(11) DEFAULT NULL,
  `last_crawled` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `price_range` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `category_id`, `slug`, `icon`, `featured_image`, `gallery`, `price_range`, `duration`, `sort_order`, `is_featured`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'fire-network', 'fas fa-fire-extinguisher', 'fire-network.jpg', NULL, 'EGP 50,000-200,000', '2-4 weeks', 0, 1, 1, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 2, 'alarm-system', 'fas fa-bell', 'alarm-system.jpg', NULL, 'EGP 20,000-80,000', '1-2 weeks', 0, 0, 1, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 3, 'ppe-suits', 'fas fa-hard-hat', 'ppe-suits.jpg', NULL, 'EGP 5,000-30,000', '1 week', 0, 0, 1, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(4, 4, 'risk-consulting', 'fas fa-clipboard-check', 'risk-consulting.jpg', NULL, 'EGP 10,000-50,000', '1-2 weeks', 0, 0, 1, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(5, 5, 'annual-maintenance', 'fas fa-tools', 'annual-maintenance.jpg', NULL, 'EGP 8,000-40,000', 'Annual', 0, 1, 1, 1, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(7, 1, 'updated-service-1984', 'fas fa-fire-extinguisher', 'demo.jpg', NULL, 'EGP 1000-2000', '1 week', 0, 0, 1, 1, NULL, '2025-07-24 20:18:13', '2025-07-24 20:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#DC2626',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `slug`, `icon`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'firefighting-systems', 'fas fa-fire-extinguisher', '#DC2626', 1, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 'fire-alarms', 'fas fa-bell', '#DC2626', 2, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 'ppe-equipment', 'fas fa-hard-hat', '#DC2626', 3, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(4, 'consulting', 'fas fa-clipboard-check', '#DC2626', 4, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(5, 'maintenance', 'fas fa-tools', '#DC2626', 5, 1, '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `service_category_translations`
--

CREATE TABLE `service_category_translations` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_category_translations`
--

INSERT INTO `service_category_translations` (`id`, `category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(5, 1, 1, 'Firefighting Systems', 'Complete firefighting systems with UL/FM certified equipment', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(6, 1, 2, 'أنظمة مكافحة الحريق', 'أنظمة مكافحة حريق متكاملة بمعدات معتمدة UL/FM', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(7, 2, 1, 'Fire Alarm Systems', 'Smart fire detection and alarm systems', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(8, 2, 2, 'أنظمة إنذار الحريق', 'أنظمة ذكية لكشف وإنذار الحريق', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(9, 3, 1, 'PPE Equipment', 'Personal protective equipment for fire safety teams', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(10, 3, 2, 'معدات الوقاية الشخصية', 'معدات وقاية شخصية لفرق مكافحة الحريق', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(11, 4, 1, 'Consulting', 'Fire safety consulting and compliance guidance', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(12, 4, 2, 'استشارات', 'استشارات سلامة الحريق ودعم الامتثال', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(13, 5, 1, 'Maintenance', 'Preventive and emergency maintenance services', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(14, 5, 2, 'صيانة', 'خدمات صيانة وقائية وطوارئ', NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `service_translations`
--

CREATE TABLE `service_translations` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` longtext DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`benefits`)),
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_translations`
--

INSERT INTO `service_translations` (`id`, `service_id`, `language_id`, `name`, `short_description`, `full_description`, `features`, `benefits`, `specifications`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Firefighting Network', 'Complete network with certified pumps and risers', 'Full design and installation of firefighting networks for industrial facilities.', '[\"UL/FM certified pumps\",\"Steel risers\",\"Sprinkler systems\"]', NULL, NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(2, 1, 2, 'شبكة مكافحة الحريق', 'شبكة متكاملة بمضخات وشهادات معتمدة', 'تصميم وتنفيذ كامل لشبكات مكافحة الحريق للمنشآت الصناعية.', '[\"مضخات معتمدة UL/FM\",\"رايزرات حديد\",\"أنظمة رش آلي\"]', NULL, NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(3, 2, 1, 'Fire Alarm System', 'Smart detection and notification', 'Advanced fire alarm systems with SCADA integration.', '[\"Smoke detectors\",\"Control panels\",\"SCADA ready\"]', NULL, NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(4, 2, 2, 'نظام إنذار الحريق', 'كشف ذكي وتنبيه فوري', 'أنظمة إنذار متقدمة مع تكامل SCADA.', '[\"كواشف دخان\",\"لوحات تحكم\",\"جاهز للتكامل مع SCADA\"]', NULL, NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(5, 3, 1, 'PPE Suits', 'Certified fire-resistant suits and helmets', 'Supply of certified PPE suits, helmets, gloves, and boots for fire teams.', '[\"Fire-resistant suits\",\"Helmets\",\"Gloves\",\"Boots\"]', '[\"Certified to EN469\",\"Comfort fit\",\"High durability\"]', '{\"material\":\"Nomex\",\"certification\":\"EN469\"}', NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(6, 3, 2, 'بدل وقاية شخصية', 'بدل وخوذات مقاومة للحريق معتمدة', 'توريد بدل وقاية شخصية معتمدة، خوذات، قفازات، وأحذية لفرق الحريق.', '[\"بدل مقاومة للحريق\",\"خوذات\",\"قفازات\",\"أحذية\"]', '[\"معتمدة EN469\",\"راحة عالية\",\"متانة\"]', '{\"المادة\":\"نوميكس\",\"الشهادة\":\"EN469\"}', NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(7, 4, 1, 'Risk Consulting', 'Site risk assessment and compliance audit', 'Comprehensive risk assessment and compliance audit for industrial facilities.', '[\"On-site assessment\",\"Compliance report\",\"Action plan\"]', '[\"Reduce risk\",\"Ensure compliance\"]', NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(8, 4, 2, 'استشارات المخاطر', 'تقييم مخاطر وفحص امتثال', 'تقييم شامل للمخاطر وفحص امتثال للمنشآت الصناعية.', '[\"تقييم ميداني\",\"تقرير امتثال\",\"خطة عمل\"]', '[\"تقليل المخاطر\",\"ضمان الامتثال\"]', NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(9, 5, 1, 'Annual Maintenance', 'Preventive and emergency maintenance', 'Annual contract for preventive and emergency maintenance of fire systems.', '[\"Scheduled visits\",\"24/7 emergency support\",\"Spare parts included\"]', '[\"Reduce downtime\",\"Priority support\"]', NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57'),
(10, 5, 2, 'صيانة سنوية', 'صيانة وقائية وطوارئ', 'عقد سنوي لصيانة وقائية وطوارئ لأنظمة الحريق.', '[\"زيارات مجدولة\",\"دعم طوارئ 24/7\",\"قطع غيار مشمولة\"]', '[\"تقليل الأعطال\",\"دعم أولوية\"]', NULL, NULL, NULL, NULL, '2025-07-24 20:09:57', '2025-07-24 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json','text') DEFAULT 'string',
  `category` varchar(50) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `is_public`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Sphinx Fire', 'string', 'general', 1, 'Website name', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(2, 'site_tagline', 'Fire Safety Solutions', 'string', 'general', 1, 'Website tagline', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(3, 'contact_phone', '+20 123 456 7890', 'string', 'contact', 1, 'Main contact phone number', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(4, 'contact_email', 'info@sphinxfire.com', 'string', 'contact', 1, 'Main contact email', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(5, 'office_address', 'Sadat City, Egypt', 'string', 'contact', 1, 'Office address', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(6, 'google_analytics_id', '', 'string', 'analytics', 0, 'Google Analytics tracking ID', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(7, 'facebook_pixel_id', '', 'string', 'analytics', 0, 'Facebook Pixel ID', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(8, 'enable_cache', 'true', 'boolean', 'performance', 0, 'Enable caching system', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(9, 'cache_duration', '3600', 'number', 'performance', 0, 'Cache duration in seconds', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(10, 'enable_compression', 'true', 'boolean', 'performance', 0, 'Enable GZIP compression', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(11, 'maintenance_mode', 'false', 'boolean', 'system', 0, 'Enable maintenance mode', '2025-07-21 21:10:41', '2025-07-21 21:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `setting_translations`
--

CREATE TABLE `setting_translations` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `language_id` int(11) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_translations`
--

INSERT INTO `setting_translations` (`id`, `setting_key`, `language_id`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 2, 'سفينكس فاير', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(2, 'site_tagline', 2, 'حلول السلامة من الحريق', '2025-07-21 21:10:41', '2025-07-21 21:10:41'),
(3, 'office_address', 2, 'مدينة السادات، مصر', '2025-07-21 21:10:41', '2025-07-21 21:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_position` varchar(100) DEFAULT NULL,
  `client_company` varchar(100) DEFAULT NULL,
  `client_avatar` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5 CHECK (`rating` >= 1 and `rating` <= 5),
  `project_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonial_translations`
--

CREATE TABLE `testimonial_translations` (
  `id` int(11) NOT NULL,
  `testimonial_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` enum('super_admin','admin','editor','author','contributor') DEFAULT 'contributor',
  `avatar_url` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `avatar_url`, `phone`, `is_active`, `last_login`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sphinxfire.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'super_admin', NULL, NULL, 1, NULL, NULL, '2025-07-21 21:10:40', '2025-07-21 21:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `ab_test_results`
--
DROP TABLE IF EXISTS `ab_test_results`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ab_test_results`  AS SELECT `cat`.`id` AS `test_id`, `clp`.`campaign_name` AS `campaign_name`, `cat`.`test_name` AS `test_name`, `cat`.`status` AS `status`, `catv`.`variant_key` AS `variant_key`, `catv`.`variant_name` AS `variant_name`, `catv`.`is_control` AS `is_control`, `catv`.`impressions` AS `impressions`, `catv`.`conversions` AS `conversions`, `catv`.`conversion_rate` AS `conversion_rate`, `cat`.`winning_variant` AS `winning_variant`, `cat`.`start_date` AS `start_date`, `cat`.`end_date` AS `end_date` FROM ((`campaign_ab_tests` `cat` join `campaign_landing_pages` `clp` on(`cat`.`campaign_id` = `clp`.`id`)) join `campaign_ab_test_variants` `catv` on(`cat`.`id` = `catv`.`test_id`)) ORDER BY `cat`.`id` ASC, `catv`.`variant_key` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `active_campaigns`
--
DROP TABLE IF EXISTS `active_campaigns`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_campaigns`  AS SELECT `clp`.`id` AS `id`, `clp`.`slug` AS `slug`, `clp`.`campaign_name` AS `campaign_name`, `clp`.`campaign_type` AS `campaign_type`, `clp`.`template` AS `template`, `clpt`.`language_id` AS `language_id`, `clpt`.`title` AS `title`, `clpt`.`subtitle` AS `subtitle`, `clpt`.`offer_headline` AS `offer_headline`, `clpt`.`meta_title` AS `meta_title`, `clpt`.`meta_description` AS `meta_description`, `clp`.`start_date` AS `start_date`, `clp`.`end_date` AS `end_date`, `clp`.`offer_details` AS `offer_details`, `clp`.`conversion_goal` AS `conversion_goal`, `clp`.`utm_parameters` AS `utm_parameters`, `clp`.`is_ab_test` AS `is_ab_test` FROM (`campaign_landing_pages` `clp` join `campaign_landing_page_translations` `clpt` on(`clp`.`id` = `clpt`.`page_id`)) WHERE `clp`.`status` = 'published' AND (`clp`.`start_date` is null OR `clp`.`start_date` <= current_timestamp()) AND (`clp`.`end_date` is null OR `clp`.`end_date` >= current_timestamp()) ;

-- --------------------------------------------------------

--
-- Structure for view `active_pages`
--
DROP TABLE IF EXISTS `active_pages`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_pages`  AS SELECT `p`.`id` AS `id`, `p`.`slug` AS `slug`, `p`.`template` AS `template`, `p`.`status` AS `status`, `pt`.`language_id` AS `language_id`, `pt`.`title` AS `title`, `pt`.`meta_title` AS `meta_title`, `pt`.`meta_description` AS `meta_description`, `pt`.`content` AS `content`, `pt`.`featured_image` AS `featured_image`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at` FROM (`pages` `p` join `page_translations` `pt` on(`p`.`id` = `pt`.`page_id`)) WHERE `p`.`status` = 'published' ;

-- --------------------------------------------------------

--
-- Structure for view `active_services`
--
DROP TABLE IF EXISTS `active_services`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_services`  AS SELECT `s`.`id` AS `id`, `s`.`slug` AS `slug`, `s`.`category_id` AS `category_id`, `sc`.`slug` AS `category_slug`, `st`.`language_id` AS `language_id`, `st`.`name` AS `name`, `st`.`short_description` AS `short_description`, `st`.`full_description` AS `full_description`, `s`.`featured_image` AS `featured_image`, `s`.`is_featured` AS `is_featured`, `s`.`sort_order` AS `sort_order` FROM ((`services` `s` join `service_categories` `sc` on(`s`.`category_id` = `sc`.`id`)) join `service_translations` `st` on(`s`.`id` = `st`.`service_id`)) WHERE `s`.`is_active` = 1 AND `sc`.`is_active` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `campaign_performance`
--
DROP TABLE IF EXISTS `campaign_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `campaign_performance`  AS SELECT `clp`.`id` AS `id`, `clp`.`campaign_name` AS `campaign_name`, `clp`.`campaign_type` AS `campaign_type`, `clp`.`conversion_goal` AS `conversion_goal`, count(distinct `cl`.`id`) AS `total_leads`, count(distinct case when `cl`.`status` = 'converted' then `cl`.`id` end) AS `converted_leads`, count(distinct case when `cc`.`event_name` = 'ViewContent' then `cc`.`id` end) AS `page_views`, count(distinct case when `cc`.`event_name` = 'Lead' then `cc`.`id` end) AS `form_submissions`, CASE WHEN count(distinct case when `cc`.`event_name` = 'ViewContent' then `cc`.`id` end) > 0 THEN round(count(distinct case when `cc`.`event_name` = 'Lead' then `cc`.`id` end) * 100.0 / count(distinct case when `cc`.`event_name` = 'ViewContent' then `cc`.`id` end),2) ELSE 0 END AS `conversion_rate`, `clp`.`start_date` AS `start_date`, `clp`.`end_date` AS `end_date` FROM ((`campaign_landing_pages` `clp` left join `campaign_leads` `cl` on(`clp`.`id` = `cl`.`campaign_id`)) left join `campaign_conversions` `cc` on(`clp`.`id` = `cc`.`campaign_id`)) GROUP BY `clp`.`id`, `clp`.`campaign_name`, `clp`.`campaign_type`, `clp`.`conversion_goal`, `clp`.`start_date`, `clp`.`end_date` ;

-- --------------------------------------------------------

--
-- Structure for view `published_blog_posts`
--
DROP TABLE IF EXISTS `published_blog_posts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `published_blog_posts`  AS SELECT `bp`.`id` AS `id`, `bp`.`slug` AS `slug`, `bp`.`category_id` AS `category_id`, `bc`.`slug` AS `category_slug`, `bpt`.`language_id` AS `language_id`, `bpt`.`title` AS `title`, `bpt`.`excerpt` AS `excerpt`, `bpt`.`content` AS `content`, `bp`.`featured_image` AS `featured_image`, `bp`.`reading_time` AS `reading_time`, `bp`.`views_count` AS `views_count`, `bp`.`published_at` AS `published_at`, `u`.`first_name` AS `author_first_name`, `u`.`last_name` AS `author_last_name` FROM (((`blog_posts` `bp` join `blog_categories` `bc` on(`bp`.`category_id` = `bc`.`id`)) join `blog_post_translations` `bpt` on(`bp`.`id` = `bpt`.`post_id`)) join `users` `u` on(`bp`.`author_id` = `u`.`id`)) WHERE `bp`.`status` = 'published' AND `bp`.`published_at` <= current_timestamp() ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_blog_category_language` (`category_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_blog_posts_category` (`category_id`),
  ADD KEY `idx_blog_posts_status` (`status`),
  ADD KEY `idx_blog_posts_published` (`published_at`);

--
-- Indexes for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_post_language` (`post_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`cache_key`),
  ADD KEY `idx_expiry` (`expiry_time`);

--
-- Indexes for table `campaign_ab_tests`
--
ALTER TABLE `campaign_ab_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_campaign_ab_tests_campaign` (`campaign_id`),
  ADD KEY `idx_campaign_ab_tests_status` (`status`);

--
-- Indexes for table `campaign_ab_test_variants`
--
ALTER TABLE `campaign_ab_test_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `campaign_conversions`
--
ALTER TABLE `campaign_conversions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_campaign_conversions_campaign` (`campaign_id`),
  ADD KEY `idx_campaign_conversions_lead` (`lead_id`),
  ADD KEY `idx_campaign_conversions_event` (`event_name`),
  ADD KEY `idx_campaign_conversions_created` (`created_at`);

--
-- Indexes for table `campaign_landing_pages`
--
ALTER TABLE `campaign_landing_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_campaign_landing_pages_slug` (`slug`),
  ADD KEY `idx_campaign_landing_pages_status` (`status`),
  ADD KEY `idx_campaign_landing_pages_dates` (`start_date`,`end_date`);

--
-- Indexes for table `campaign_landing_page_translations`
--
ALTER TABLE `campaign_landing_page_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `campaign_leads`
--
ALTER TABLE `campaign_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_campaign_leads_campaign` (`campaign_id`),
  ADD KEY `idx_campaign_leads_email` (`email`),
  ADD KEY `idx_campaign_leads_status` (`status`),
  ADD KEY `idx_campaign_leads_created` (`created_at`);

--
-- Indexes for table `contact_forms`
--
ALTER TABLE `contact_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `form_key` (`form_key`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_event_name` (`event_name`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_events_name_date` (`event_name`,`created_at`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `location_content`
--
ALTER TABLE `location_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `location_content_translations`
--
ALTER TABLE `location_content_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_location_content_language` (`content_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `location_translations`
--
ALTER TABLE `location_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_location_language` (`location_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `maintenance_log`
--
ALTER TABLE `maintenance_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_media_mime_type` (`mime_type`),
  ADD KEY `idx_media_folder` (`folder`);

--
-- Indexes for table `media_translations`
--
ALTER TABLE `media_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_media_language` (`media_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slug` (`slug`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_pages_slug` (`slug`),
  ADD KEY `idx_pages_status` (`status`),
  ADD KEY `idx_pages_published_at` (`published_at`);

--
-- Indexes for table `page_translations`
--
ALTER TABLE `page_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_language` (`page_id`,`language_id`),
  ADD KEY `idx_page_translations_language` (`language_id`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_page_url` (`page_url`),
  ADD KEY `idx_viewed_at` (`viewed_at`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_page_views_url_date` (`page_url`,`viewed_at`);

--
-- Indexes for table `performance_metrics`
--
ALTER TABLE `performance_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_metric` (`page_url`,`metric_type`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_projects_category` (`category_id`),
  ADD KEY `idx_projects_status` (`status`),
  ADD KEY `idx_projects_featured` (`is_featured`);

--
-- Indexes for table `project_categories`
--
ALTER TABLE `project_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `project_category_translations`
--
ALTER TABLE `project_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_category_language` (`category_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `project_translations`
--
ALTER TABLE `project_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_language` (`project_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_section` (`page_id`,`section_key`),
  ADD KEY `idx_sections_page_type` (`page_id`,`section_type`);

--
-- Indexes for table `section_translations`
--
ALTER TABLE `section_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_section_language` (`section_id`,`language_id`),
  ADD KEY `idx_section_translations_language` (`language_id`);

--
-- Indexes for table `seo_metrics`
--
ALTER TABLE `seo_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_code` (`language_code`),
  ADD KEY `idx_page_language` (`page_url`,`language_code`),
  ADD KEY `idx_last_crawled` (`last_crawled`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_services_category` (`category_id`),
  ADD KEY `idx_services_featured` (`is_featured`),
  ADD KEY `idx_services_active` (`is_active`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `service_category_translations`
--
ALTER TABLE `service_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_language` (`category_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `service_translations`
--
ALTER TABLE `service_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_service_language` (`service_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_setting_language` (`setting_key`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `testimonial_translations`
--
ALTER TABLE `testimonial_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_testimonial_language` (`testimonial_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_forms`
--
ALTER TABLE `contact_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `location_content`
--
ALTER TABLE `location_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_content_translations`
--
ALTER TABLE `location_content_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_translations`
--
ALTER TABLE `location_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `maintenance_log`
--
ALTER TABLE `maintenance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media_translations`
--
ALTER TABLE `media_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `page_translations`
--
ALTER TABLE `page_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_metrics`
--
ALTER TABLE `performance_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_categories`
--
ALTER TABLE `project_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_category_translations`
--
ALTER TABLE `project_category_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_translations`
--
ALTER TABLE `project_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `section_translations`
--
ALTER TABLE `section_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `seo_metrics`
--
ALTER TABLE `seo_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_category_translations`
--
ALTER TABLE `service_category_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `service_translations`
--
ALTER TABLE `service_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonial_translations`
--
ALTER TABLE `testimonial_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_category_translations`
--
ALTER TABLE `blog_category_translations`
  ADD CONSTRAINT `blog_category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_category_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`),
  ADD CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  ADD CONSTRAINT `blog_post_translations_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_post_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `campaign_ab_tests`
--
ALTER TABLE `campaign_ab_tests`
  ADD CONSTRAINT `campaign_ab_tests_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign_landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaign_ab_test_variants`
--
ALTER TABLE `campaign_ab_test_variants`
  ADD CONSTRAINT `campaign_ab_test_variants_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `campaign_ab_tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaign_conversions`
--
ALTER TABLE `campaign_conversions`
  ADD CONSTRAINT `campaign_conversions_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign_landing_pages` (`id`),
  ADD CONSTRAINT `campaign_conversions_ibfk_2` FOREIGN KEY (`lead_id`) REFERENCES `campaign_leads` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `campaign_landing_page_translations`
--
ALTER TABLE `campaign_landing_page_translations`
  ADD CONSTRAINT `campaign_landing_page_translations_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `campaign_landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaign_leads`
--
ALTER TABLE `campaign_leads`
  ADD CONSTRAINT `campaign_leads_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign_landing_pages` (`id`);

--
-- Constraints for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD CONSTRAINT `contact_submissions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `contact_forms` (`id`),
  ADD CONSTRAINT `contact_submissions_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `location_content`
--
ALTER TABLE `location_content`
  ADD CONSTRAINT `location_content_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `location_content_translations`
--
ALTER TABLE `location_content_translations`
  ADD CONSTRAINT `location_content_translations_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `location_content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_content_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `location_translations`
--
ALTER TABLE `location_translations`
  ADD CONSTRAINT `location_translations_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `maintenance_log`
--
ALTER TABLE `maintenance_log`
  ADD CONSTRAINT `maintenance_log_ibfk_1` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `media_translations`
--
ALTER TABLE `media_translations`
  ADD CONSTRAINT `media_translations_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pages_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `page_translations`
--
ALTER TABLE `page_translations`
  ADD CONSTRAINT `page_translations_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `page_views`
--
ALTER TABLE `page_views`
  ADD CONSTRAINT `page_views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `project_category_translations`
--
ALTER TABLE `project_category_translations`
  ADD CONSTRAINT `project_category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_category_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `project_translations`
--
ALTER TABLE `project_translations`
  ADD CONSTRAINT `project_translations_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_translations`
--
ALTER TABLE `section_translations`
  ADD CONSTRAINT `section_translations_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `seo_metrics`
--
ALTER TABLE `seo_metrics`
  ADD CONSTRAINT `seo_metrics_ibfk_1` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `services_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `services_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_category_translations`
--
ALTER TABLE `service_category_translations`
  ADD CONSTRAINT `service_category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_category_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `service_translations`
--
ALTER TABLE `service_translations`
  ADD CONSTRAINT `service_translations_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD CONSTRAINT `setting_translations_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `testimonials_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `testimonials_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `testimonial_translations`
--
ALTER TABLE `testimonial_translations`
  ADD CONSTRAINT `testimonial_translations_ibfk_1` FOREIGN KEY (`testimonial_id`) REFERENCES `testimonials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `testimonial_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
