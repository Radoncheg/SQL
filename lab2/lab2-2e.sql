USE npz;

/*Выбрать информацию о НПЗ Томской области, название (полное и сокращённое), адрес (фактический и юридический), приказ о внесении в реестр */
SELECT full_name, short_name, legal_address, actual_address, register_info
FROM
 factory f
WHERE 
 legal_address LIKE ('%Томск%')
OR
 actual_address LIKE ('%Томск%');


