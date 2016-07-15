--
-- Table structure for table `xtsync_wo_table`
--

CREATE TABLE IF NOT EXISTS `xtsync_wo_table` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `wono` varchar(10) NOT NULL UNIQUE KEY,
  `sono` varchar(10) NOT NULL,
  `quantity` int(12) unsigned NOT NULL,
  `due` int(10) unsigned NOT NULL DEFAULT '1',
  `unique_key` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `xtsync_assembly_table`
--

CREATE TABLE IF NOT EXISTS `xtsync_assembly_table` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `number` varchar(250) DEFAULT NULL,
  `revision` varchar(10) DEFAULT '',
  `unique_key` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `xtsync_customer_table`
--

CREATE TABLE IF NOT EXISTS `xtsync_customer_table` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL UNIQUE KEY,
  `pono` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `acct_date` int(11) NOT NULL,
  `time_stamp` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `xtsync_query_stocking_table`
--

CREATE TABLE IF NOT EXISTS `xtsync_query_stocking_table` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `query_text` varchar(1200) DEFAULT '',
  `remark` varchar(250) DEFAULT '',
  `created_by` int(10) unsigned NOT NULL,
  `time_stamp` int(10) unsigned NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `approved_by` int(10) unsigned NOT NULL DEFAULT '0',
  `approved_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;