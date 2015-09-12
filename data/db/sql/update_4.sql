alter table products drop index serial;
insert into dictionaries (parentid,acronym,name) (select parentid,'new','Nowe' from dictionaries where acronym = 'instock');
ALTER TABLE products ADD pairedcard varchar(64);