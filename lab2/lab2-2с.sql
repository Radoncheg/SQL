USE npz;

/*Выбрать все НПЗ с глубиной переработки в диапазоне от 40 до 70%*/
SELECT * FROM factory f
WHERE 
 processing_depth 
BETWEEN 40 AND 70;
