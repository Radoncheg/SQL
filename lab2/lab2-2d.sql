USE npz;

/*Выбрать все НПЗ строящиеся и проектируемые*/
SELECT
 f.*, s.status_text 
FROM
 factory f
INNER JOIN
 status s 
ON
 f.status_id = s.id 
WHERE 
 s.status_text = 'строящийся' 
OR
 s.status_text = 'проектируемый';


