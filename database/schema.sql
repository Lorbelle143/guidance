-- Database Schema for Guidance Office Inventory System
-- Run this SQL to set up the database

CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

-- Users table (admin accounts)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin','user') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin accounts (password: admin123)
INSERT INTO users (email, password, full_name, role, is_active)
VALUES
  ('lorbelleganzan@gmail.com', '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'Lorbelle Ganzan',     'admin', 1),
  ('gco@nbsc.edu.ph',          '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'GCO Admin',           'admin', 1),
  ('jacorpuz@nbsc.edu.ph',     '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'Jo Augustine Corpuz', 'admin', 1)
ON DUPLICATE KEY UPDATE email=email;

-- Students table (with login credentials)
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    email VARCHAR(100),
    photo VARCHAR(255),
    -- Personal Info
    program_year VARCHAR(100),
    birth_date DATE,
    gender ENUM('Male','Female','Other'),
    ethnicity VARCHAR(100),
    religion VARCHAR(100),
    civil_status VARCHAR(50),
    -- Contact
    mobile_number VARCHAR(20),
    personal_email VARCHAR(100),
    institutional_email VARCHAR(100),
    permanent_address TEXT,
    current_address TEXT,
    -- Family
    mother_name VARCHAR(150),
    mother_birthday DATE,
    mother_ethnicity VARCHAR(100),
    mother_religion VARCHAR(100),
    mother_education VARCHAR(100),
    mother_occupation VARCHAR(100),
    mother_company VARCHAR(100),
    mother_income VARCHAR(50),
    mother_contact VARCHAR(20),
    father_name VARCHAR(150),
    father_birthday DATE,
    father_ethnicity VARCHAR(100),
    father_religion VARCHAR(100),
    father_education VARCHAR(100),
    father_occupation VARCHAR(100),
    father_company VARCHAR(100),
    father_income VARCHAR(50),
    father_contact VARCHAR(20),
    parent_status VARCHAR(100),
    num_siblings INT DEFAULT 0,
    guardian_name VARCHAR(150),
    guardian_address TEXT,
    guardian_contact VARCHAR(20),
    -- Interests
    hobbies TEXT,
    talents TEXT,
    sports TEXT,
    socio_civic TEXT,
    school_org TEXT,
    -- Health
    hospitalized TINYINT(1) DEFAULT 0,
    hospitalized_details TEXT,
    had_operation TINYINT(1) DEFAULT 0,
    operation_details TEXT,
    has_illness TINYINT(1) DEFAULT 0,
    illness_details TEXT,
    common_illness TEXT,
    last_doctor_visit VARCHAR(100),
    doctor_visit_reason TEXT,
    -- Life Circumstances (checkboxes)
    concern_fear TINYINT(1) DEFAULT 0,
    concern_communication TINYINT(1) DEFAULT 0,
    concern_shyness TINYINT(1) DEFAULT 0,
    concern_loneliness TINYINT(1) DEFAULT 0,
    concern_stress TINYINT(1) DEFAULT 0,
    concern_anger TINYINT(1) DEFAULT 0,
    concern_self_confidence TINYINT(1) DEFAULT 0,
    concern_academic TINYINT(1) DEFAULT 0,
    concern_career TINYINT(1) DEFAULT 0,
    concern_financial TINYINT(1) DEFAULT 0,
    concern_others TEXT,
    -- Meta
    qr_code VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_last_name (last_name),
    INDEX idx_first_name (first_name),
    INDEX idx_created_at (created_at),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student documents table
CREATE TABLE IF NOT EXISTS student_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    document_type ENUM('inventory_form', 'whodas', 'pid5', 'consent_form', 'other') NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX idx_student_id (student_id),
    INDEX idx_document_type (document_type),
    INDEX idx_uploaded_at (uploaded_at),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
-- Username: admin
-- Password: admin123 (CHANGE THIS IMMEDIATELY IN PRODUCTION!)
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES ('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIR.yvjLK6', 'System Administrator', 'admin@example.com', 'admin', 1)
ON DUPLICATE KEY UPDATE username=username;

-- Insert default regular user
-- Username: user
-- Password: user123
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES ('user', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 'user@example.com', 'user', 1)
ON DUPLICATE KEY UPDATE username=username;

-- Activity log table (optional, for audit trail)
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
