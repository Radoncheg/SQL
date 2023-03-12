USE npz;

/*Выбрать все НПЗ, которые производят авиакеросин*/
SELECT
 f.*, p.production 
FROM
 factory f
INNER JOIN 
 production p 
ON 
 f.id = p.factory_id 
WHERE 
 p.production = 'авиакеросин';
