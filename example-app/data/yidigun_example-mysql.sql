/* example data for YidigunExample application */

DROP TABLE IF EXISTS yidigun_example;
CREATE TABLE yidigun_example (
	no			INT NOT NULL auto_increment,
	name		VARCHAR(50) NOT NULL,
	company		VARCHAR(50),
	email		VARCHAR(50),
	phone		VARCHAR(20),
	sex			CHAR(1),
	reg_date	DATETIME,
	mod_date	DATETIME,
	PRIMARY KEY (no)
) Engine=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO yidigun_example VALUES (NULL, 'Cassandra Pentaghast', 'Seekers of Truth', 'cassandra@da2.example.com', '555-1234', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Cullen', 'Templar Order', 'cullen@dai.example.com', '555-1235', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Josephine Montilyet', 'Independant', 'josephine@dai.example.com', '555-1236', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Leliana', 'Left Hand of the Devine', 'leliana@dao.example.com', '555-1237', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Blackwall', 'Gray Warden', 'blackwall@dai.example.com', '555-1238', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Cole', 'Fade', 'cole@dai.example.com', '555-1239', 'S', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Iron Bull', 'Ben-Hassrath', 'iron.bull@dai.example.com', '555-1240', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Dorian', 'Altus Mages', 'dorian@dai.example.com', '555-1241', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Sera', 'Friends of Red Jenny', 'sera@dai.example.com', '555-1242', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Solas', 'Apostate Mage', 'solas@dai.example.com', '555-1243', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Varric Tethras', 'Marchant\'s Guild', 'varric@da2.example.com', '555-1244', 'M', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Vivienne', 'Montsimmard', 'vivienne@dai.example.com', '555-1245', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Morrigan', 'Witches of the Wilds', 'morrigan@dao.example.com', '555-1246', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Inna Yoo', 'YG Family', 'inna@ygf.example.com', '555-1247', 'F', SYSDATE(), SYSDATE());
INSERT INTO yidigun_example VALUES (NULL, 'Justinia V', 'Chantry', 'justinia5@dai.example.com', '555-1248', 'F', SYSDATE(), SYSDATE());
