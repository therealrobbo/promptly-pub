-- --------------------------------------------------------

--
-- Table structure for table `prompts`
--

DROP TABLE IF EXISTS `prompts`;
CREATE TABLE IF NOT EXISTS `prompts` (
`prompt_id` int(11) NOT NULL,
  `prmopt_text` text COLLATE utf8_unicode_ci NOT NULL,
  `prompt_date_added` datetime NOT NULL,
  `prompt_date_updated` datetime NOT NULL,
  `prompt_last_used` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prompts`
--
ALTER TABLE `prompts`
 ADD PRIMARY KEY (`prompt_id`), ADD KEY `prompt_date_added` (`prompt_date_added`,`prompt_date_updated`,`prompt_last_used`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `prompts`
--
ALTER TABLE `prompts`
MODIFY `prompt_id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------

--
-- Table structure for table `samples`
--

DROP TABLE IF EXISTS `samples`;
CREATE TABLE IF NOT EXISTS `samples` (
`sample_id` int(11) NOT NULL,
  `sample_user_id` int(11) NOT NULL,
  `sample_prompt_id` int(11) NOT NULL,
  `sample_text` text COLLATE utf8_unicode_ci NOT NULL,
  `sample_date_added` datetime NOT NULL,
  `sample_date_updated` datetime NOT NULL,
  `sample_public` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `samples`
--
ALTER TABLE `samples`
 ADD PRIMARY KEY (`sample_id`), ADD KEY `sample_user_id` (`sample_user_id`), ADD KEY `sample_prompt_id` (`sample_prompt_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `samples`
--
ALTER TABLE `samples`
MODIFY `sample_id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL,
  `user_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_date_joined` datetime NOT NULL,
  `user_privilege` int(11) NOT NULL,
  `user_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_url` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` ADD `user_active` INT NOT NULL DEFAULT '0' , ADD INDEX (`user_active`) ;

ALTER TABLE `prompts` CHANGE `prmopt_text` `prompt_text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE prompts DROP INDEX prompt_date_added;
ALTER TABLE `prompts` ADD INDEX(`prompt_date_added`);
ALTER TABLE `prompts` ADD INDEX(`prompt_last_used`);
ALTER TABLE `prompts` ADD INDEX(`prompt_date_updated`);

ALTER TABLE `prompts` ADD `prompt_deleted` INT NOT NULL DEFAULT '0' , ADD INDEX (`prompt_deleted`) ;

ALTER TABLE `prompts` CHANGE `prompt_last_used` `prompt_use_date` DATETIME NOT NULL;

--
-- Table structure for table `partner_codes`
--

CREATE TABLE IF NOT EXISTS `partner_codes` (
`pc_id` int(11) NOT NULL,
  `pc_name` varchar(128) NOT NULL,
  `pc_code` text NOT NULL,
  `pc_location` smallint(6) NOT NULL,
  `pc_order` smallint(6) NOT NULL,
  `pc_exclusions` varchar(64) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `partner_codes`
--
ALTER TABLE `partner_codes`
 ADD PRIMARY KEY (`pc_id`), ADD KEY `pc_location` (`pc_location`,`pc_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `partner_codes`
--
ALTER TABLE `partner_codes`
MODIFY `pc_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `samples` ADD `sample_session_id` INT NOT NULL AFTER `sample_user_id`, ADD INDEX (`sample_session_id`) ;


--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
`session_id` int(11) NOT NULL,
  `session_hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `session_ip` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `session_date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `session_ip` (`session_ip`), ADD KEY `session_hash` (`session_hash`), ADD KEY `session_date_created` (`session_date_created`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;