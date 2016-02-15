CREATE TABLE `systemlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time` float DEFAULT NULL,
  `memory` float DEFAULT NULL,
  `message` varchar(4000) DEFAULT NULL,
  `url` varchar(4000) NOT NULL,
  `userid` int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
