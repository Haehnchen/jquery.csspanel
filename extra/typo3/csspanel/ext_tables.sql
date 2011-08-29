#
# Table structure for table 'tx_csspanel_configs'
#
CREATE TABLE tx_csspanel_configs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	name tinytext,
	config text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);