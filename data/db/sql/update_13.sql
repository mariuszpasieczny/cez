create table cez.servicesview as 
select s.*,'warszawa' as instance from cez_warszawa.servicesview s
union all
select s.*,'lublin' as instance from cez_lublin.servicesview s; 
alter table ADD INDEX `id` (`id`);

create table cez.servicecodesview as 
select s.*,'warszawa' as instance from cez_warszawa.servicecodesview s
union all
select s.*,'lublin' as instance from cez_lublin.servicecodesview s; 
alter table ADD INDEX `id` (`id`);
alter table ADD INDEX `serviceid` (`serviceid`);
alter table ADD INDEX `attributeacronym` (`attributeacronym`);
alter table ADD INDEX `codeacronym` (`codeacronym`);

DELIMITER $$
CREATE TRIGGER cez_warszawa.servicesinsert AFTER INSERT 
ON cez_warszawa.services
FOR EACH ROW BEGIN  
INSERT INTO cez.servicesview select * from cez_warszawa.services where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER cez_warszawa.servicesupdate AFTER UPDATE 
ON cez_warszawa.services
FOR EACH ROW BEGIN  
DELETE FROM cez.servicesview WHERE id = NEW.id;
INSERT INTO cez.servicesview select * from cez_warszawa.services where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER cez_warszawa.servicesdelete AFTER DELETE 
ON cez_warszawa.services
FOR EACH ROW BEGIN  
DELETE FROM cez.servicesview WHERE id = OLD.id;
END$$
DELIMITER ;