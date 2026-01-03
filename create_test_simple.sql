USE middo_db;
DELETE FROM user WHERE email='test@middo.app';
INSERT INTO user (
    email, roles, password, first_name, last_name, user_type,
    disponible_pour_missions, recherche_emploi, recherche_investisseurs,
    recherche_partenaires, sans_emploi, pret_a_se_former, pret_avous_exporter
) VALUES (
    'test@middo.app', '["ROLE_USER"]', '$2y$10$MueVr5V8iPWaRsYGy14VOO2eAN4zUdWC8cQkb52NupDKPHfScuc7y', 'Test', 'MIDDO', 'particulier',
    0, 0, 0, 0, 0, 0, 0
);
SELECT 'SUCCES' as status, id, email, first_name, last_name FROM user WHERE email='test@middo.app';