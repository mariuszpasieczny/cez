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