DELIMITER $$
CREATE TRIGGER servicesinsert AFTER INSERT 
ON services
FOR EACH ROW BEGIN  
INSERT INTO cez_dbdev.servicesview select * from services where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicesupdate AFTER UPDATE 
ON services
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicesview WHERE id = NEW.id;
INSERT INTO cez_dbdev.servicesview select * from services where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicesdelete AFTER DELETE 
ON services
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicesview WHERE id = OLD.id;
END$$
DELIMITER ;
