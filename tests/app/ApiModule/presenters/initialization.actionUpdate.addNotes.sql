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
-- Dumping data for table `note`
--

INSERT INTO `note` (`id`, `note`, `date`, `user_id`) VALUES
(1, 'Note 1', '2014-08-01', 1),
(2, 'Note 2', '2014-09-01', 1),
(3, 'Note 3', '2014-09-01', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `sys_note`
--

INSERT INTO `sys_note` (`id`, `note`, `date`) VALUES
(1, 'Sys note 1', '2014-04-02'),
(2, 'Sys note 2', '2014-04-02'),
(3, 'Sys note 3', '2014-04-05');

-- --------------------------------------------------------

INSERT INTO `settings` (`id`, `value`) VALUES
('holiday.credits', 'i:33;'),
('holiday.yearStart', 's:5:"04-01";');