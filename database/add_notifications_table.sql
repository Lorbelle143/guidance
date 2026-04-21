-- Notifications system
-- Run this SQL to add notification support

USE inventory_db;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('admin_to_student','student_activity') NOT NULL,
    -- For admin→student: who sent it
    sent_by INT DEFAULT NULL COMMENT 'users.id of admin who sent',
    -- Target: NULL = all students, or specific student id
    student_id INT DEFAULT NULL COMMENT 'students.id — NULL means broadcast to all',
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    -- Read tracking per student (for broadcast, each student has own read row)
    is_read TINYINT(1) DEFAULT 0,
    -- For student_activity notifications (admin bell)
    admin_read TINYINT(1) DEFAULT 0,
    -- Extra context
    link VARCHAR(255) DEFAULT NULL COMMENT 'optional URL to link to',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_admin_read (admin_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
