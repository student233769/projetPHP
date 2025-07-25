-- 4) Insertion de donnée
INSERT INTO Personne (matricule, mdp, nom, prenom, avatar, administrateur) VALUES
('U001', 'mdp123', 'Durand', 'Alice', "xBoxAntonin.jpg", 0),
('U002', 'mdp123', 'Martin', 'Bob', "xBoxLastTest.jpg", 0),
('U003', 'mdp123', 'Dupont', 'Clara', NULL, 0),
('A001', 'mdp123', 'Lemoine', 'David', "xBoxLiveRobin.jpg", 1),
('A002', 'mdp123', 'Moreau', 'Emma', NULL, 1);

INSERT INTO Cours (titre, bloc, section) VALUES
('Algo 1', 'Bloc1', 'Info'),
('Programmation C', 'Bloc1', 'Info'),
('Systèmes', 'Bloc1', 'Info'),
('POO Java', 'Bloc2', 'Info'),
('BDD', 'Bloc2', 'Info'),
('Web', 'Bloc2', 'Info');

INSERT INTO Cours (titre, bloc, section) VALUES
('Introduction à la comptabilité', 'Bloc1', 'Comptabilité'),
('Comptabilité générale', 'Bloc1', 'Comptabilité'),
('Mathématiques financières', 'Bloc1', 'Comptabilité'),
('Comptabilité analytique', 'Bloc2', 'Comptabilité'),
('Fiscalité des entreprises', 'Bloc2', 'Comptabilité'),
('Droit comptable', 'Bloc2', 'Comptabilité');

INSERT INTO Ressources (titre, type, etat, cours_id, personne_id,cheminRelatif) VALUES
('Introduction à l’algorithmique', 'URL', 'VALIDE', 1, 'U001','https://www.youtube.com/watch?v=xvFZjo5PgG0&list=RDxvFZjo5PgG0&start_radio=1&ab_channel=Duran'),
('Structures conditionnelles en C', 'URL', 'VALIDE', 2, 'U002', 'https://www.youtube.com/watch?v=xvFZjo5PgG0&list=RDxvFZjo5PgG0&start_radio=1&ab_channel=Duran'),
('Modélisation de bases de données', 'URL', 'EN_ATTENTE', 5, 'U003', 'https://www.youtube.com/watch?v=xvFZjo5PgG0&list=RDxvFZjo5PgG0&start_radio=1&ab_channel=Duran');

INSERT INTO LectureRessource (personne_id, ressource_id) VALUES
('U001', 1),
('U002', 1),
('U003', 2),
('U001', 2);


