-- إصلاح البيانات المتضررة من مشكلة cp850
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE turkey_mosque;

-- تحديث بيانات المدير
UPDATE user_info 
SET Name = 'مدير النظام', Address = 'الجامعة'
WHERE u_no = 2 AND u_name = 'admin';

-- إعادة إدراج بيانات الصالات بالترميز الصحيح
DELETE FROM mosque_info WHERE no IN (1,2);

INSERT INTO mosque_info (no, price, description) VALUES
(1, 500.00, 'قاعة الجامع الكبيرة - تتسع لـ 200 شخص - مجهزة بالتكييف والصوتيات'),
(2, 300.00, 'قاعة الجامع الصغيرة - تتسع لـ 100 شخص - مجهزة بالتكييف');
