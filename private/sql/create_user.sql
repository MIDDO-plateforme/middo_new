USE middo_db;
DELETE FROM user WHERE email='test@middo.app';
INSERT INTO user (email, roles, password, first_name, last_name, created_at, updated_at) 
VALUES ('test@middo.app', '["ROLE_USER"]', '$2y$13$tUY2/sP1G4t.te1l.F10kOHQuV8xZ80JFmayfCch8L2YOdaHb0ovO', 'Test', 'MIDDO', NOW(), NOW());
SELECT CONCAT('Utilisateur cree : ', email, ' | ID : ', id) as Result FROM user WHERE email='test@middo.app';