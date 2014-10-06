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
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`date`, `halfday`, `user_id`) VALUES
('2014-02-03', '1', 1),
('2014-09-08', '0', 1),
('2014-09-09', '0', 1),
('2014-09-10', '0', 1);

-- --------------------------------------------------------

INSERT INTO `settings` (`id`, `value`) VALUES
('holiday.credits', 'i:33;'),
('holiday.yearStart', 's:5:"04-01";');