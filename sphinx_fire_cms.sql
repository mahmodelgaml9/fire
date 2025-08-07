-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 03:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`activity_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `admin_dashboard_widgets`
--

CREATE TABLE `admin_dashboard_widgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `widget_type` varchar(50) NOT NULL,
  `widget_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`widget_config`)),
  `position` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

----------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `admin_quick_actions`
--

CREATE TABLE `admin_quick_actions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT 'primary',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `last_used` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `backup_logs`
--

CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL,
  `backup_type` enum('full','partial','files','database') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `status` enum('success','failed','in_progress') NOT NULL,
  `error_message` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `meta_title` varchar(255) DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `shares_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `content_analytics`
--

CREATE TABLE `content_analytics` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `views` int(11) DEFAULT 0,
  `unique_views` int(11) DEFAULT 0,
  `time_spent` int(11) DEFAULT 0,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_approvals`
--

CREATE TABLE `content_approvals` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_scheduling`
--

CREATE TABLE `content_scheduling` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `scheduled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action` enum('publish','unpublish','archive') NOT NULL,
  `created_by` int(11) NOT NULL,
  `is_executed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_versions`
--

CREATE TABLE `content_versions` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `content_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`content_data`)),
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` enum('sent','failed','pending') NOT NULL,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `is_featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `meta_title` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
-- Table structure for table `seo_optimizations`
--

CREATE TABLE `seo_optimizations` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `content_id` int(11) NOT NULL,
  `seo_score` int(11) DEFAULT 0,
  `title_length` int(11) DEFAULT 0,
  `description_length` int(11) DEFAULT 0,
  `keyword_density` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keyword_density`)),
  `internal_links` int(11) DEFAULT 0,
  `external_links` int(11) DEFAULT 0,
  `image_alt_tags` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `meta_title` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed','pending') NOT NULL,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_health`
--

CREATE TABLE `system_health` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` decimal(10,2) NOT NULL,
  `metric_unit` varchar(20) DEFAULT NULL,
  `status` enum('healthy','warning','critical') DEFAULT 'healthy',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `testimonial_translations`
--

CREATE TABLE `testimonial_translations` (
  `id` int(11) NOT NULL,
  `testimonial_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_position` varchar(100) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `testimonial_text` text DEFAULT NULL,
  `rating` int(1) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `webhook_endpoints`
--

CREATE TABLE `webhook_endpoints` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `secret_key` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_triggered` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_dashboard_widgets`
--
ALTER TABLE `admin_dashboard_widgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_widget_type` (`widget_type`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_module` (`module`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permission` (`module`,`action`);

--
-- Indexes for table `admin_quick_actions`
--
ALTER TABLE `admin_quick_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role_id`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_api_key` (`api_key`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_backup_type` (`backup_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `content_analytics`
--
ALTER TABLE `content_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type_id` (`content_type`,`content_id`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `content_approvals`
--
ALTER TABLE `content_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_content_type_id` (`content_type`,`content_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `content_scheduling`
--
ALTER TABLE `content_scheduling`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_content_type_id` (`content_type`,`content_id`),
  ADD KEY `idx_scheduled_at` (`scheduled_at`),
  ADD KEY `idx_is_executed` (`is_executed`);

--
-- Indexes for table `content_versions`
--
ALTER TABLE `content_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_content_type_id` (`content_type`,`content_id`),
  ADD KEY `idx_version_number` (`version_number`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `idx_to_email` (`to_email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_is_active` (`is_active`);

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
  ADD KEY `idx_pages_published_at` (`published_at`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_is_featured` (`is_featured`);

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
-- Indexes for table `seo_optimizations`
--
ALTER TABLE `seo_optimizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type_id` (`content_type`,`content_id`),
  ADD KEY `idx_seo_score` (`seo_score`);

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
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_phone_number` (`phone_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Indexes for table `system_health`
--
ALTER TABLE `system_health`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_metric_name` (`metric_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `webhook_endpoints`
--
ALTER TABLE `webhook_endpoints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_dashboard_widgets`
--
ALTER TABLE `admin_dashboard_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `admin_quick_actions`
--
ALTER TABLE `admin_quick_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `blog_post_translations`
--
ALTER TABLE `blog_post_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `content_analytics`
--
ALTER TABLE `content_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_approvals`
--
ALTER TABLE `content_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_scheduling`
--
ALTER TABLE `content_scheduling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_versions`
--
ALTER TABLE `content_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `section_translations`
--
ALTER TABLE `section_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312;

--
-- AUTO_INCREMENT for table `seo_metrics`
--
ALTER TABLE `seo_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo_optimizations`
--
ALTER TABLE `seo_optimizations`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `service_translations`
--
ALTER TABLE `service_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_health`
--
ALTER TABLE `system_health`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `testimonial_translations`
--
ALTER TABLE `testimonial_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `webhook_endpoints`
--
ALTER TABLE `webhook_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD CONSTRAINT `api_keys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD CONSTRAINT `backup_logs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `content_approvals`
--
ALTER TABLE `content_approvals`
  ADD CONSTRAINT `content_approvals_ibfk_1` FOREIGN KEY (`requested_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_approvals_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `content_scheduling`
--
ALTER TABLE `content_scheduling`
  ADD CONSTRAINT `content_scheduling_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_versions`
--
ALTER TABLE `content_versions`
  ADD CONSTRAINT `content_versions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD CONSTRAINT `email_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `webhook_endpoints`
--
ALTER TABLE `webhook_endpoints`
  ADD CONSTRAINT `webhook_endpoints_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
