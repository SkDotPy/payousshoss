-- ================================================
-- PAW CONNECT - SCHÉMA DE BASE DE DONNÉES
-- Version: 1.0
-- Date: 2025-10-09
-- ================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `pawconnect` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pawconnect`;

-- ================================================
-- TABLE: utilisateur
-- ================================================
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `age` INT(11) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `actif` BOOLEAN DEFAULT 1,
  `security` VARCHAR(255) DEFAULT NULL,
  `Role` VARCHAR(50) DEFAULT 'user',
  `date_logout` DATETIME DEFAULT NULL,
  `DateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `nbAdoptions` INT(11) DEFAULT 0,
  `nbAccueils` INT(11) DEFAULT 0,
  `avatar_color` VARCHAR(7) DEFAULT '#1E3A8A',
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: refuge
-- ================================================
CREATE TABLE IF NOT EXISTS `refuge` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `adresse` VARCHAR(255),
  `ville` VARCHAR(100),
  `CodePostal` VARCHAR(10),
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `actif` BOOLEAN DEFAULT 1,
  `security` VARCHAR(255) DEFAULT NULL,
  `Role` VARCHAR(50) DEFAULT 'refuge',
  `dateLogout` DATETIME DEFAULT NULL,
  `DateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `number` VARCHAR(20),
  `capacity` INT(11) DEFAULT 0,
  `nbAdoptions` INT(11) DEFAULT 0,
  `nbAcceil` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ville` (`ville`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: animal
-- ================================================
CREATE TABLE IF NOT EXISTS `animal` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `IDuser` VARCHAR(50) DEFAULT NULL,
  `IDRefuge` INT(11) DEFAULT NULL,
  `nom` VARCHAR(100) NOT NULL,
  `age` INT(11) DEFAULT NULL,
  `description` TEXT,
  `species` VARCHAR(50),
  `race` VARCHAR(100),
  `image` VARCHAR(255),
  `state` VARCHAR(50) DEFAULT 'disponible',
  `IMGnom` VARCHAR(255),
  `IMGtype` VARCHAR(50),
  `situation` TEXT,
  `adoption` BOOLEAN DEFAULT 0,
  `favori` BOOLEAN DEFAULT 0,
  `signatureRefuge` VARCHAR(255),
  `signatureUtilisateur` VARCHAR(255),
  `CarteIdentite` VARCHAR(255),
  `sex` VARCHAR(10),
  `color` VARCHAR(50),
  `DateCreation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_refuge` (`IDRefuge`),
  KEY `idx_species` (`species`),
  KEY `idx_state` (`state`),
  KEY `idx_adoption` (`adoption`),
  FOREIGN KEY (`IDRefuge`) REFERENCES `refuge`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: historique
-- ================================================
CREATE TABLE IF NOT EXISTS `historique` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idUser` INT(11) DEFAULT NULL,
  `idRefuge` INT(11) DEFAULT NULL,
  `changIn` TEXT,
  `temps` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `type` VARCHAR(50),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`idUser`),
  KEY `idx_refuge` (`idRefuge`),
  KEY `idx_type` (`type`),
  KEY `idx_temps` (`temps`),
  FOREIGN KEY (`idUser`) REFERENCES `utilisateur`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`idRefuge`) REFERENCES `refuge`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: message
-- ================================================
CREATE TABLE IF NOT EXISTS `message` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idUser` INT(11) DEFAULT NULL,
  `idRefuge` INT(11) DEFAULT NULL,
  `message` TEXT,
  `nom` VARCHAR(100),
  `messagerie` VARCHAR(255),
  `date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `chat` VARCHAR(255),
  `lu` BOOLEAN DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`idUser`),
  KEY `idx_refuge` (`idRefuge`),
  KEY `idx_date` (`date`),
  FOREIGN KEY (`idUser`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`idRefuge`) REFERENCES `refuge`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: newsletter
-- ================================================
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `nom` VARCHAR(100) DEFAULT NULL,
  `actif` BOOLEAN DEFAULT 1,
  `date_inscription` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_desinscription` DATETIME DEFAULT NULL,
  `raison_desinscription` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: captcha
-- ================================================
CREATE TABLE IF NOT EXISTS `captcha` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `question` TEXT NOT NULL,
  `reponse` VARCHAR(255) NOT NULL,
  `actif` BOOLEAN DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: adoption
-- ================================================
CREATE TABLE IF NOT EXISTS `adoption` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idUser` INT(11) NOT NULL,
  `idAnimal` INT(11) NOT NULL,
  `idRefuge` INT(11) DEFAULT NULL,
  `date_demande` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_validation` DATETIME DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT 'en_attente',
  `commentaire` TEXT,
  `signature_utilisateur` TEXT,
  `signature_refuge` TEXT,
  `contrat_pdf` VARCHAR(255),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`idUser`),
  KEY `idx_animal` (`idAnimal`),
  KEY `idx_refuge` (`idRefuge`),
  KEY `idx_statut` (`statut`),
  FOREIGN KEY (`idUser`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`idAnimal`) REFERENCES `animal`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`idRefuge`) REFERENCES `refuge`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLE: favoris
-- ================================================
CREATE TABLE IF NOT EXISTS `favoris` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idUser` INT(11) NOT NULL,
  `idAnimal` INT(11) NOT NULL,
  `date_ajout` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favori` (`idUser`, `idAnimal`),
  KEY `idx_user` (`idUser`),
  KEY `idx_animal` (`idAnimal`),
  FOREIGN KEY (`idUser`) REFERENCES `utilisateur`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`idAnimal`) REFERENCES `animal`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERTION DES DONNÉES PAR DÉFAUT
-- ================================================

-- Admin par défaut
INSERT INTO `utilisateur` (`nom`, `age`, `email`, `password`, `actif`, `Role`, `DateCreation`, `avatar_color`) 
VALUES 
('Administrateur', 30, 'admin@pawconnect.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin', NOW(), '#1E3A8A');
-- Mot de passe: Admin@2025

-- Questions captcha par défaut
INSERT INTO `captcha` (`question`, `reponse`, `actif`) VALUES
('Quelle est la capitale de la France ?', 'Paris', 1),
('Combien font 2 + 2 ?', '4', 1),
('Quelle est la couleur du ciel ?', 'Bleu', 1),
('Quel animal aboie ?', 'Chien', 1),
('Combien de pattes a un chat ?', '4', 1),
('Quelle est la couleur d\'une orange ?', 'Orange', 1),
('Combien de jours dans une semaine ?', '7', 1),
('Quel animal miaule ?', 'Chat', 1);

-- ================================================
-- TRIGGERS
-- ================================================

-- Trigger pour incrémenter nbAdoptions
DELIMITER //
CREATE TRIGGER after_adoption_insert
AFTER INSERT ON adoption
FOR EACH ROW
BEGIN
  IF NEW.statut = 'validee' THEN
    UPDATE utilisateur SET nbAdoptions = nbAdoptions + 1 WHERE id = NEW.idUser;
    UPDATE refuge SET nbAdoptions = nbAdoptions + 1 WHERE id = NEW.idRefuge;
    UPDATE animal SET adoption = 1, state = 'adopte' WHERE id = NEW.idAnimal;
  END IF;
END//
DELIMITER ;

-- Trigger pour mettre à jour nbAdoptions lors de la validation
DELIMITER //
CREATE TRIGGER after_adoption_update
AFTER UPDATE ON adoption
FOR EACH ROW
BEGIN
  IF NEW.statut = 'validee' AND OLD.statut != 'validee' THEN
    UPDATE utilisateur SET nbAdoptions = nbAdoptions + 1 WHERE id = NEW.idUser;
    UPDATE refuge SET nbAdoptions = nbAdoptions + 1 WHERE id = NEW.idRefuge;
    UPDATE animal SET adoption = 1, state = 'adopte' WHERE id = NEW.idAnimal;
  END IF;
END//
DELIMITER ;

-- ================================================
-- VUES
-- ================================================

-- Vue des animaux disponibles
CREATE OR REPLACE VIEW animaux_disponibles AS
SELECT 
  a.*,
  r.nom AS refuge_name,
  r.ville AS refuge_ville,
  r.email AS refuge_email
FROM animal a
LEFT JOIN refuge r ON a.IDRefuge = r.id
WHERE a.adoption = 0 AND a.state = 'disponible';

-- Vue des statistiques globales
CREATE OR REPLACE VIEW statistiques_globales AS
SELECT
  (SELECT COUNT(*) FROM utilisateur WHERE actif = 1) AS total_utilisateurs,
  (SELECT COUNT(*) FROM refuge WHERE actif = 1) AS total_refuges,
  (SELECT COUNT(*) FROM animal WHERE adoption = 0) AS animaux_disponibles,
  (SELECT COUNT(*) FROM animal WHERE adoption = 1) AS adoptions_realisees,
  (SELECT COUNT(*) FROM adoption WHERE statut = 'en_attente') AS adoptions_en_attente,
  (SELECT COUNT(*) FROM newsletter WHERE actif = 1) AS abonnes_newsletter;

-- ================================================
-- INDEX SUPPLÉMENTAIRES POUR PERFORMANCE
-- ================================================

-- Index fulltext pour la recherche
ALTER TABLE animal ADD FULLTEXT INDEX idx_search (nom, race, description);

-- ================================================
-- FIN DU SCHÉMA
-- ================================================