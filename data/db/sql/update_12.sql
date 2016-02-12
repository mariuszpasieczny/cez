DELIMITER $$
CREATE TRIGGER servicesinsert AFTER INSERT 
ON services
FOR EACH ROW BEGIN  
INSERT INTO cez_dbdev.servicesview select s.*,'lublin' from servicesview s where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicesupdate AFTER UPDATE 
ON services
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicesview WHERE id = NEW.id and instance = 'lublin';
INSERT INTO cez_dbdev.servicesview select s.*,'lublin' from servicesview s where id = NEW.id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicesdelete AFTER DELETE 
ON services
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicesview WHERE id = OLD.id and instance = 'lublin';
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicecodesinsert AFTER INSERT 
ON servicecodes
FOR EACH ROW BEGIN  
INSERT INTO cez_dbdev.servicecodesview select s.*,'lublin' from servicecodesview s where serviceid = NEW.serviceid;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicecodesupdate AFTER UPDATE 
ON servicecodes
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicecodesview WHERE serviceid = NEW.serviceid and instance = 'lublin';
INSERT INTO cez_dbdev.servicecodesview select s.*,'lublin' from servicecodesview s where serviceid = NEW.serviceid;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER servicecodesdelete AFTER DELETE 
ON servicecodes
FOR EACH ROW BEGIN  
DELETE FROM cez_dbdev.servicecodesview WHERE serviceid = OLD.serviceid and instance = 'lublin';
END$$
DELIMITER ;