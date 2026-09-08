-- =====================================================================
-- PHStudio — Photography Booking & Client Management Platform
-- Database Schema (MySQL 8.0+ / MariaDB 10.4+)
-- =====================================================================
-- Portfolio Demo — Not a real photography booking service.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS phstudio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phstudio;

-- ---------------------------------------------------------------------
-- USERS (single table, role-based access)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role            ENUM('admin','photographer','client') NOT NULL DEFAULT 'client',
    full_name       VARCHAR(120) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    phone           VARCHAR(30)  DEFAULT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    avatar_path     VARCHAR(255) DEFAULT NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PHOTOGRAPHY CATEGORIES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(80) NOT NULL,
    slug            VARCHAR(80) NOT NULL UNIQUE,
    description     VARCHAR(255) DEFAULT NULL,
    cover_image     VARCHAR(255) DEFAULT NULL,
    sort_order      INT UNSIGNED DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PORTFOLIO PHOTOS (public gallery, organized by category)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS portfolio_photos;
CREATE TABLE portfolio_photos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED NOT NULL,
    title           VARCHAR(150) DEFAULT NULL,
    image_path      VARCHAR(255) NOT NULL,
    is_featured     TINYINT(1) NOT NULL DEFAULT 0,
    sort_order      INT UNSIGNED DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PACKAGES / PRICING
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS packages;
CREATE TABLE packages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED DEFAULT NULL,
    name            VARCHAR(120) NOT NULL,
    slug            VARCHAR(120) NOT NULL UNIQUE,
    description     TEXT,
    price           DECIMAL(10,2) NOT NULL,
    duration_hours  DECIMAL(4,1) DEFAULT NULL,
    deliverables    TEXT COMMENT 'JSON array of bullet points',
    is_popular      TINYINT(1) NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    sort_order      INT UNSIGNED DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PHOTOGRAPHER AVAILABILITY (working days / blocked dates)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS availability_slots;
CREATE TABLE availability_slots (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    photographer_id INT UNSIGNED NOT NULL,
    slot_date       DATE NOT NULL,
    start_time      TIME NOT NULL,
    end_time        TIME NOT NULL,
    is_blocked      TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = photographer marked unavailable',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (photographer_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_slot (photographer_id, slot_date, start_time)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- BOOKINGS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bookings;
CREATE TABLE bookings (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id           INT UNSIGNED NOT NULL,
    photographer_id     INT UNSIGNED DEFAULT NULL,
    package_id          INT UNSIGNED NOT NULL,
    category_id         INT UNSIGNED DEFAULT NULL,
    session_date        DATE NOT NULL,
    start_time          TIME NOT NULL,
    end_time            TIME NOT NULL,
    location            VARCHAR(255) DEFAULT NULL,
    event_details        TEXT,
    status              ENUM('pending','confirmed','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
    admin_notes         TEXT COMMENT 'Internal / shared notes from photographer to client',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY uniq_booking_slot (photographer_id, session_date, start_time),
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_date (session_date)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- BOOKING REFERENCE IMAGES (client-uploaded inspiration photos)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS booking_references;
CREATE TABLE booking_references (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED NOT NULL,
    file_path       VARCHAR(255) NOT NULL,
    uploaded_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- BOOKING STATUS HISTORY (audit trail)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS booking_status_history;
CREATE TABLE booking_status_history (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED NOT NULL,
    old_status      VARCHAR(20) DEFAULT NULL,
    new_status      VARCHAR(20) NOT NULL,
    changed_by      INT UNSIGNED DEFAULT NULL,
    changed_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- MESSAGES / NOTES THREAD (client <-> photographer per booking)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS booking_messages;
CREATE TABLE booking_messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED NOT NULL,
    sender_id       INT UNSIGNED NOT NULL,
    message         TEXT NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- INVOICES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS invoices;
CREATE TABLE invoices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(30) NOT NULL UNIQUE,
    amount_total    DECIMAL(10,2) NOT NULL,
    amount_paid     DECIMAL(10,2) NOT NULL DEFAULT 0,
    status          ENUM('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid',
    due_date        DATE DEFAULT NULL,
    issued_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at         DATETIME DEFAULT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PRIVATE CLIENT GALLERIES
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS galleries;
CREATE TABLE galleries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT UNSIGNED DEFAULT NULL,
    client_id       INT UNSIGNED NOT NULL,
    title           VARCHAR(150) NOT NULL COMMENT 'e.g. Juan & Maria — Wedding Gallery',
    cover_image     VARCHAR(255) DEFAULT NULL,
    photographer_notes TEXT,
    share_token     VARCHAR(64) NOT NULL UNIQUE,
    is_published    TINYINT(1) NOT NULL DEFAULT 0,
    allow_download  TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- GALLERY PHOTOS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS gallery_photos;
CREATE TABLE gallery_photos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gallery_id      INT UNSIGNED NOT NULL,
    file_path       VARCHAR(255) NOT NULL,
    caption         VARCHAR(255) DEFAULT NULL,
    sort_order      INT UNSIGNED DEFAULT 0,
    uploaded_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- GALLERY PHOTO FAVORITES (client hearts photos)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS gallery_favorites;
CREATE TABLE gallery_favorites (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gallery_photo_id INT UNSIGNED NOT NULL,
    client_id       INT UNSIGNED NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_photo_id) REFERENCES gallery_photos(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_fav (gallery_photo_id, client_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TESTIMONIALS
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS testimonials;
CREATE TABLE testimonials (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_name     VARCHAR(120) NOT NULL,
    client_photo    VARCHAR(255) DEFAULT NULL,
    category_id     INT UNSIGNED DEFAULT NULL,
    rating          TINYINT UNSIGNED NOT NULL DEFAULT 5,
    quote           TEXT NOT NULL,
    is_published    TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- FAQ
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS faqs;
CREATE TABLE faqs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question        VARCHAR(255) NOT NULL,
    answer          TEXT NOT NULL,
    sort_order      INT UNSIGNED DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- CONTACT MESSAGES (from public contact form)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS contact_messages;
CREATE TABLE contact_messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    subject         VARCHAR(200) DEFAULT NULL,
    message         TEXT NOT NULL,
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
