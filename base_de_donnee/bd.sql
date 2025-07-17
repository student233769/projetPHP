DROP TABLE IF EXISTS Ressources;
DROP TABLE IF EXISTS Cours;
DROP TABLE IF EXISTS Personne;

-- 1) Table personne
CREATE TABLE Personne (

  matricule VARCHAR(50) PRIMARY KEY,
  mdp VARCHAR(255) NOT NULL,
  nom VARCHAR(255) NOT NULL,
  prenom VARCHAR(255) NOT NULL,
  avatar VARCHAR(255),
  administrateur TINYINT(1) NOT NULL DEFAULT 0
);

-- 2) Table cours (hérite de card)
CREATE TABLE Cours (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(150) NOT NULL,
  bloc VARCHAR(50) NOT NULL,
  section VARCHAR(50) NOT NULL
);

-- 3) Table ressources
CREATE TABLE Ressources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dateAjout DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  titre VARCHAR(150)       NOT NULL,
  type VARCHAR(50)         NOT NULL,
  dateValidationAjout DATETIME,
  etat ENUM('EN_ATTENTE','VALIDE','REJETE')
       NOT NULL DEFAULT 'EN_ATTENTE',
  estDejaLue TINYINT(1)    NOT NULL DEFAULT 0,
  cheminRelatif VARCHAR(255),
  cours_id INT             NOT NULL,
  personne_id VARCHAR(50)          NOT NULL,
  CONSTRAINT fk_ressources_cours
    FOREIGN KEY (cours_id)
    REFERENCES Cours(id),
  CONSTRAINT fk_ressources_personne
    FOREIGN KEY (personne_id)
    REFERENCES Personne(matricule)
); 





-- 4) Insertion de donnée
INSERT INTO Personne (matricule, mdp, nom, prenom, avatar, administrateur) VALUES
('U001', 'mdp123', 'Durand', 'Alice', "buste.jpg", 0),
('U002', 'mdp456', 'Martin', 'Bob', NULL, 0),
('U003', 'mdp789', 'Dupont', 'Clara', NULL, 0),
('A001', 'admin123', 'Lemoine', 'David', NULL, 1),
('A002', 'admin456', 'Moreau', 'Emma', NULL, 1);

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
