USE middo_db;
DELETE FROM user WHERE email='test@middo.app';
INSERT INTO user (email, roles, password, first_name, last_name, created_at, updated_at) 
VALUES ('test@middo.app', '["ROLE_USER"]', '', 'Test', 'MIDDO', NOW(), NOW());
SELECT 'SUCCES : Utilisateur cree' as status, id, email, LEFT(password, 30) as password_hash FROM user WHERE email='test@middo.app';