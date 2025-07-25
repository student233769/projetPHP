DROP TABLE IF EXISTS LectureRessource;
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
  type ENUM('JPG', 'PNG', 'URL', 'PDF') NOT NULL,
  dateValidationAjout DATETIME,
  etat ENUM('EN_ATTENTE','VALIDE','REJETE')
       NOT NULL DEFAULT 'EN_ATTENTE',
  cheminRelatif VARCHAR(255),
  cours_id INT             NOT NULL,
  personne_id VARCHAR(50)          NOT NULL,
  CONSTRAINT fk_ressources_cours FOREIGN KEY (cours_id) REFERENCES Cours(id),
  CONSTRAINT fk_ressources_personne FOREIGN KEY (personne_id) REFERENCES Personne(matricule)
); 

-- 4) Table intermédiaire pour savoir qui à lu quoi

CREATE TABLE LectureRessource (
  personne_id VARCHAR(50),
  ressource_id INT,
  
  PRIMARY KEY (personne_id, ressource_id),
  FOREIGN KEY (personne_id) REFERENCES Personne(matricule),
  FOREIGN KEY (ressource_id) REFERENCES Ressources(id)
);






