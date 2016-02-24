drop view servicesview;
create table servicesview as 
select s.*,'consys' as instance from cez_dbconsys.servicesview s
union all
select s.*,'art' as instance from cez_dbart.servicesview s
union all
select s.*,'edar' as instance from cez_dbedar.servicesview s
union all
select s.*,'eska' as instance from cez_dbeska.servicesview s
union all
select s.*,'lublin' as instance from cez_dblublin.servicesview s
union all
select s.*,'cnt1' as instance from cez_dbcnt1.servicesview s
union all
select s.*,'cnt2' as instance from cez_dbcnt2.servicesview s;
alter table servicesview ADD INDEX `id` (`id`,`instance`);

drop view servicecodesview;
create table servicecodesview as 
select s.*,'consys' as instance from cez_dbconsys.servicecodesview s
union all
select s.*,'art' as instance from cez_dbart.servicecodesview s
union all
select s.*,'edar' as instance from cez_dbedar.servicecodesview s
union all
select s.*,'eska' as instance from cez_dbeska.servicecodesview s
union all
select s.*,'lublin' as instance from cez_dblublin.servicecodesview s
union all
select s.*,'cnt1' as instance from cez_dbcnt1.servicecodesview s
union all
select s.*,'cnt2' as instance from cez_dbcnt2.servicesview s;
alter table servicecodesview ADD INDEX `id` (`id`,`instance`);
alter table servicecodesview ADD INDEX `serviceid` (`serviceid`);
alter table servicecodesview ADD INDEX `attributeacronym` (`attributeacronym`);
alter table servicecodesview ADD INDEX `codeacronym` (`codeacronym`);

ALTER TABLE services ADD COLUMN instance varchar(64);