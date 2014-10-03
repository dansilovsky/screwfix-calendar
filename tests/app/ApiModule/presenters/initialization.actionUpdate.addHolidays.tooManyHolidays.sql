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
(1, 'dans', 'admin', 'dan@post.cz', 'daAyVm43mRrWw'),
(2, 'pavelp', 'editor', 'pavelp@post.cz', 'paxV.KnG78cmk'),
(3, 'beab', 'member', 'beab@post.cz', 'be/9/xYh.XNKU');

-- --------------------------------------------------------

--
-- Dumping data for table `bank_holiday`
--

INSERT INTO `bank_holiday` (`date`, `name`) VALUES
('2014-02-07', 'Bank holiday 1'),
('2014-04-01', 'April bankholiday'),
('2014-08-25', 'Late Summer Holiday');

-- --------------------------------------------------------

--
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`date`, `halfday`, `user_id`) VALUES
('2014-02-03', '0', 1),
('2014-09-08', '1', 1),
('2014-09-09', '0', 1),
('2014-09-10', '0', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `note`
--

INSERT INTO `note` (`id`, `note`, `date`, `user_id`) VALUES
(1, 'Note 1', '2014-02-03', 1),
(2, 'Note 2', '2014-04-02', 1),
(3, 'Note 3', '2014-04-04', 1),
(5, 'Note 4', '2014-09-01', 1),
(6, 'Note 5', '2014-09-01', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `pattern`
--

INSERT INTO `pattern` (`user_id`, `pattern`) VALUES
(1, 'O:27:"Screwfix\\ShiftPatternFilter":5:{s:8:"\0*\0_name";s:5:"shift";s:37:"\0Screwfix\\ShiftPatternFilter\0_pattern";a:3:{i:0;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:1;a:7:{i:0;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:1;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:2;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:3;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:4;N;i:5;N;i:6;N;}i:2;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;N;}}s:44:"\0Screwfix\\ShiftPatternFilter\0_weeksInPattern";i:3;s:41:"\0Screwfix\\ShiftPatternFilter\0_patternDate";O:25:"Screwfix\\ShiftPatternDate":6:{s:33:"\0Screwfix\\ShiftPatternDate\0_start";O:20:"Nette\\Utils\\DateTime":3:{s:4:"date";s:19:"1970-01-05 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:34:"\0Screwfix\\DateTime\0_oneDayInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:0;s:1:"d";i:1;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:36:"\0Screwfix\\DateTime\0_oneMonthInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:1;s:1:"d";i:0;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:4:"date";s:19:"2014-08-04 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:14:"\0*\0_overwrites";a:0:{}}'),
(2, 'O:27:"Screwfix\\ShiftPatternFilter":5:{s:8:"\0*\0_name";s:5:"shift";s:37:"\0Screwfix\\ShiftPatternFilter\0_pattern";a:3:{i:0;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:1;a:7:{i:0;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:1;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:2;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:3;a:2:{i:0;s:5:"12:00";i:1;s:5:"20:00";}i:4;N;i:5;N;i:6;N;}i:2;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;N;}}s:44:"\0Screwfix\\ShiftPatternFilter\0_weeksInPattern";i:3;s:41:"\0Screwfix\\ShiftPatternFilter\0_patternDate";O:25:"Screwfix\\ShiftPatternDate":4:{s:33:"\0Screwfix\\ShiftPatternDate\0_start";O:14:"Nette\\DateTime":3:{s:4:"date";s:19:"1970-01-05 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:4:"date";s:19:"2013-10-19 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:14:"\0*\0_overwrites";a:0:{}}'),
(3, 'O:27:"Screwfix\\ShiftPatternFilter":5:{s:8:"\0*\0_name";s:5:"shift";s:37:"\0Screwfix\\ShiftPatternFilter\0_pattern";a:6:{i:0;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;N;}i:1;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:2;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;N;i:5;N;i:6;N;}i:3;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;N;}i:4;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:5;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;N;i:5;N;i:6;N;}}s:44:"\0Screwfix\\ShiftPatternFilter\0_weeksInPattern";i:6;s:41:"\0Screwfix\\ShiftPatternFilter\0_patternDate";O:25:"Screwfix\\ShiftPatternDate":4:{s:33:"\0Screwfix\\ShiftPatternDate\0_start";O:14:"Nette\\DateTime":3:{s:4:"date";s:19:"1970-01-05 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:4:"date";s:19:"2014-05-20 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:14:"\0*\0_overwrites";a:0:{}}');
-- --------------------------------------------------------

--
-- Dumping data for table `sys_note`
--

INSERT INTO `sys_note` (`id`, `note`, `date`) VALUES
(1, 'Sys note 1', '2014-04-02'),
(2, 'Sys note 2', '2014-04-02'),
(3, 'Sys note 3', '2014-04-05');

-- --------------------------------------------------------

--
-- Dumping data for table `sys_pattern`
--

INSERT INTO `sys_pattern` (`id`, `name`, `pattern`) VALUES
(1, 'Team 1', 'O:27:"Screwfix\\ShiftPatternFilter":5:{s:8:"\0*\0_name";s:5:"shift";s:37:"\0Screwfix\\ShiftPatternFilter\0_pattern";a:6:{i:0;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;N;}i:1;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:2;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;N;i:5;N;i:6;N;}i:3;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;N;}i:4;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:5;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;N;i:5;N;i:6;N;}}s:44:"\0Screwfix\\ShiftPatternFilter\0_weeksInPattern";i:6;s:41:"\0Screwfix\\ShiftPatternFilter\0_patternDate";O:25:"Screwfix\\ShiftPatternDate":6:{s:33:"\0Screwfix\\ShiftPatternDate\0_start";O:20:"Nette\\Utils\\DateTime":3:{s:4:"date";s:19:"1970-01-05 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:34:"\0Screwfix\\DateTime\0_oneDayInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:0;s:1:"d";i:1;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:36:"\0Screwfix\\DateTime\0_oneMonthInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:1;s:1:"d";i:0;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:4:"date";s:19:"2014-06-14 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:14:"\0*\0_overwrites";a:0:{}}'),
(2, 'Team 4', 'O:27:"Screwfix\\ShiftPatternFilter":5:{s:8:"\0*\0_name";s:5:"shift";s:37:"\0Screwfix\\ShiftPatternFilter\0_pattern";a:6:{i:0;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;N;}i:1;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:2;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;N;i:5;N;i:6;N;}i:3;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:5;N;i:6;N;}i:4;a:7:{i:0;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:1;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:2;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:3;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:4;a:2:{i:0;s:5:"07:00";i:1;s:5:"15:00";}i:5;N;i:6;a:2:{i:0;s:5:"09:30";i:1;s:5:"17:30";}}i:5;a:7:{i:0;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:1;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:2;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:3;a:2:{i:0;s:5:"15:00";i:1;s:5:"23:00";}i:4;N;i:5;N;i:6;N;}}s:44:"\0Screwfix\\ShiftPatternFilter\0_weeksInPattern";i:6;s:41:"\0Screwfix\\ShiftPatternFilter\0_patternDate";O:25:"Screwfix\\ShiftPatternDate":6:{s:33:"\0Screwfix\\ShiftPatternDate\0_start";O:20:"Nette\\Utils\\DateTime":3:{s:4:"date";s:19:"1970-01-05 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:34:"\0Screwfix\\DateTime\0_oneDayInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:0;s:1:"d";i:1;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:36:"\0Screwfix\\DateTime\0_oneMonthInterval";O:12:"DateInterval":15:{s:1:"y";i:0;s:1:"m";i:1;s:1:"d";i:0;s:1:"h";i:0;s:1:"i";i:0;s:1:"s";i:0;s:7:"weekday";i:0;s:16:"weekday_behavior";i:0;s:17:"first_last_day_of";i:0;s:6:"invert";i:0;s:4:"days";b:0;s:12:"special_type";i:0;s:14:"special_amount";i:0;s:21:"have_weekday_relative";i:0;s:21:"have_special_relative";i:0;}s:4:"date";s:19:"2014-06-14 00:00:00";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/London";}s:14:"\0*\0_overwrites";a:0:{}}');


INSERT INTO `settings` (`id`, `value`) VALUES
('holiday.credits', 'i:6;'),
('holiday.yearStart', 's:5:"04-01";');
