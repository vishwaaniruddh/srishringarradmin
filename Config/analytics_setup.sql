-- Database tables for Sri Shringarr user activity tracking

-- Table for generic visitor activity events
CREATE TABLE IF NOT EXISTS `analytics_events` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(64) NOT NULL,
  `event_type` VARCHAR(50) NOT NULL, -- e.g. page_view, category_view, cart_add, checkout_funnel, wishlist_toggle
  `page_path` VARCHAR(255) NOT NULL,
  `target_id` INT(11) DEFAULT NULL, -- product_id or category_id if applicable
  `target_type` VARCHAR(50) DEFAULT NULL, -- 'jewellery' or 'garments'
  `metadata` TEXT DEFAULT NULL, -- JSON string for dynamic payload (e.g. search keyword, rental days)
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for tracking user search terms and result counts
CREATE TABLE IF NOT EXISTS `analytics_searches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `query` VARCHAR(255) NOT NULL,
  `results_count` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
