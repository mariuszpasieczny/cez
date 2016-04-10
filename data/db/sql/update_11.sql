CREATE OR REPLACE
VIEW `dictionariesview` AS
    SELECT 
        `d`.`id` AS `id`,
        `d`.`parentid` AS `parentid`,
        (SELECT 
                `dp`.`name`
            FROM
                `dictionaries` `dp`
            WHERE
                (`dp`.`id` = `d`.`parentid`)) AS `parentname`,
        (SELECT 
                `dp`.`acronym`
            FROM
                `dictionaries` `dp`
            WHERE
                (`dp`.`id` = `d`.`parentid`)) AS `parentacronym`,
        `d`.`acronym` AS `acronym`,
        `d`.`name` AS `name`,
        `d`.`dateadd` AS `dateadd`,
        `d`.`statusid` AS `statusid`,
        `d`.`system` AS `system`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodeid`,
        (SELECT 
                `da`.`attributevalue`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodename`,
        (SELECT 
                `da`.`attributecode`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodeacronym`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'price'))) AS `price`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'datefrom'))) AS `datefrom`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'datetill'))) AS `datetill`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'instanceid'))) AS `instanceid`,
        (SELECT 
                `da`.`attributevalue`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'instanceid'))) AS `instancename`,
        (SELECT 
                `da`.`attributecode`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'instanceid'))) AS `instanceacronym`,
        (SELECT 
                `da`.`value`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'regionid'))) AS `regionid`,
        (SELECT 
                `da`.`attributevalue`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'regionid'))) AS `regionname`,
        (SELECT 
                `da`.`attributecode`
            FROM
                `dictionaryattributesview` `da`
            WHERE
                ((`da`.`entryid` = `d`.`id`)
                    AND (`da`.`attributeacronym` = 'regionid'))) AS `regionacronym`
    FROM
        `dictionaries` `d`;
insert into dictionaries (parentid,acronym,name) values (3,'system','Słowniki systemowe');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2889', 'territory', 'Obszar dyspozytorski');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'GD2', 'GD2');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'GD5', 'GD5');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'GD6', 'GD6');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'GD1', 'GD1');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L20', 'L20');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L50', 'L50');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L04', 'L04');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L00', 'L00');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L03', 'L03');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'L02', 'L02');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'P97', 'P97');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'P99', 'P99');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'P98', 'P98');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WSC', 'WSC');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WCC', 'WCC');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WWW', 'WWW');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WSM', 'WSM');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WSO', 'WSO');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WYY', 'WYY');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WGG', 'WGG');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WHH', 'WHH');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2982', 'WJJ', 'WJJ');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'BY1', 'BY1');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'BY2', 'BY2');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'BY3', 'BY3');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'BY5', 'BY5');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'E94', 'E94');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'E95', 'E95');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'E96', 'E96');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'E97', 'E97');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'E98', 'E98');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'G05', 'G05');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'G06', 'G06');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2890', 'G07', 'G07');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2889', 'calendar', 'Kalendarz serwisowy');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2898', 'region', 'Region');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2914', 'R1', 'R1');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2914', 'R2', 'R2');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2914', 'R3', 'R3');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2889', 'instance', 'Instancja');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'abmedia', 'A&Bmedia');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'art', 'ART.');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'consys', 'Konsys');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'edar', 'EDAR');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'lublin', 'Nplay Lublin');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'tav', 'Tav');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'abmedia', 'A&B Media');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'wawrzyniak', 'PW Wawrzyniak');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'net2b', 'Net2b');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2918', 'lucjad', 'Lucjad Bydgoszcz');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2', 'regionid', 'Region');
INSERT INTO dictionaries (parentid, acronym, name) VALUES ('2', 'instanceid', 'Instancja');