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

# user must be created first due to foreign key constraints
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `role`, `email`, `password`) VALUES
(1, 'dans', 'admin', 'dan@post.cz', 'daAyVm43mRrWw');


-- --------------------------------------------------------

--
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`date`, `halfday`, `user_id`) VALUES
('2013-02-05', '1', 1),
('2014-02-03', '1', 1),
('2014-09-08', '1', 1),
('2014-09-09', '0', 1),
('2014-09-10', '0', 1),
('2014-09-11', '0', 1),
('2014-09-12', '0', 1),
('2014-09-13', '0', 1),
('2014-09-14', '0', 1),
('2014-09-15', '0', 1),
('2014-09-16', '0', 1),
('2014-09-17', '0', 1),
('2014-09-18', '0', 1);

-- --------------------------------------------------------

INSERT INTO `settings` (`id`, `value`) VALUES
('holiday.credits', 'i:33;'),
('holiday.yearStart', 's:5:"04-01";');
