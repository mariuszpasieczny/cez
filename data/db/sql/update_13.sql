drop view servicesview;
create table servicesview as 
select s.*,'lublin' as instance from cez_dbtest.servicesview s;
alter table servicesview ADD INDEX `id` (`id`);

drop view servicecodesview;
create table servicecodesview as 
select s.*,'lublin' as instance from cez_dbtest.servicecodesview s;
alter table servicecodesview ADD INDEX `id` (`id`);
alter table servicecodesview ADD INDEX `serviceid` (`serviceid`);
alter table servicecodesview ADD INDEX `attributeacronym` (`attributeacronym`);
alter table servicecodesview ADD INDEX `codeacronym` (`codeacronym`);
