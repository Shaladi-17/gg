-- ========================================
-- قاعدة بيانات نظام حجز قاعة الجامع
-- turkey_mosque
-- ========================================

CREATE DATABASE IF NOT EXISTS turkey_mosque
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE turkey_mosque;

-- ----------------------------------------
-- جدول المستخدمين
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS user_info (
    u_no       INT AUTO_INCREMENT PRIMARY KEY,
    u_name     VARCHAR(50)  NOT NULL UNIQUE,
    Password   VARCHAR(100) NOT NULL,
    Priv       TINYINT      NOT NULL DEFAULT 2  COMMENT '1=admin, 2=user',
    Name       VARCHAR(100) NOT NULL,
    Address    VARCHAR(200) NOT NULL,
    Telphone   VARCHAR(20)  NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------
-- جدول بيانات الصالة
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS mosque_info (
    no          INT AUTO_INCREMENT PRIMARY KEY,
    price       DECIMAL(10,2) NOT NULL DEFAULT 0,
    description TEXT          NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------
-- جدول الحجوزات
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS resv_Info (
    r_no    INT AUTO_INCREMENT PRIMARY KEY,
    u_no    INT            NOT NULL,
    Paid    DECIMAL(10,2)  NOT NULL DEFAULT 0,
    r_date  DATE           NOT NULL,
    Type    TINYINT        NOT NULL DEFAULT 0  COMMENT '0=مبدئي, 1=نهائي',
    no      INT            NOT NULL,
    FOREIGN KEY (u_no) REFERENCES user_info(u_no) ON DELETE CASCADE,
    FOREIGN KEY (no)   REFERENCES mosque_info(no)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------
-- جدول صور الصالة
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS Picture (
    pic_no   INT AUTO_INCREMENT PRIMARY KEY,
    Pic_path VARCHAR(255) NOT NULL,
    no       INT          NOT NULL,
    FOREIGN KEY (no) REFERENCES mosque_info(no) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- بيانات أولية
-- ========================================

-- مستخدم مدير افتراضي (Priv=1) كلمة المرور: admin123
INSERT INTO user_info (u_name, Password, Priv, Name, Address, Telphone, email)
VALUES ('admin', 'admin123', 1, 'مدير النظام', 'الجامعة', '0501234567', 'admin@mosque.com');

-- مستخدم عادي للتجربة (Priv=2) كلمة المرور: user123a
INSERT INTO user_info (u_name, Password, Priv, Name, Address, Telphone, email)
VALUES ('user1', 'user123a', 2, 'أحمد محمد', 'الرياض', '0551234567', 'user1@mosque.com');

-- بيانات صالة افتراضية
INSERT INTO mosque_info (price, description)
VALUES (500.00, 'قاعة الجامع الكبيرة - تتسع لـ 200 شخص - مجهزة بالتكييف والصوتيات');

INSERT INTO mosque_info (price, description)
VALUES (300.00, 'قاعة الجامع الصغيرة - تتسع لـ 100 شخص - مجهزة بالتكييف');
