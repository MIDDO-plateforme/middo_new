-- Corriger les encodages dans la table project
UPDATE project SET name = REPLACE(name, 'Ã©', 'é');
UPDATE project SET name = REPLACE(name, 'Ã¨', 'è');
UPDATE project SET name = REPLACE(name, 'Ãª', 'ê');
UPDATE project SET name = REPLACE(name, 'Ã ', 'à');
UPDATE project SET description = REPLACE(description, 'Ã©', 'é');
UPDATE project SET description = REPLACE(description, 'Ã¨', 'è');
UPDATE project SET description = REPLACE(description, 'Ãª', 'ê');
UPDATE project SET description = REPLACE(description, 'Ã ', 'à');

-- Afficher le résultat
SELECT id, name, description FROM project LIMIT 5;
