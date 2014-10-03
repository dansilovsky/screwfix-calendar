TRUNCATE TABLE `bank_holiday`;

TRUNCATE TABLE `holiday`;

TRUNCATE TABLE `note`;

TRUNCATE TABLE `sys_note`;

TRUNCATE TABLE `pattern`;

TRUNCATE TABLE `sys_pattern`;

TRUNCATE TABLE `settings`;

DELETE FROM `user` WHERE 1;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";