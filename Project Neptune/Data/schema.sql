-- Project Neptune - Database Schema & Seed Data for startup

CREATE DATABASE IF NOT EXISTS neptune CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE neptune;
DROP TABLE IF EXISTS readiness_snapshot;
DROP TABLE IF EXISTS asset_rollup;
DROP TABLE IF EXISTS assets;

CREATE TABLE assets (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  kind         ENUM('CARRIER','DESTROYER','SUBMARINE','SQUADRON') NOT NULL,
  vessel_type  VARCHAR(32)  NOT NULL,    
  vessel_name  VARCHAR(64)  NULL,         
  hull_number  VARCHAR(32)  NULL,         
  UNIQUE KEY uk_kind_hull (kind, hull_number)
) ENGINE=InnoDB;

-- Current status per asset
CREATE TABLE asset_rollup (
  asset_id        INT PRIMARY KEY,
  color           ENUM('GREEN','AMBER','RED') NOT NULL, 
  fuel            TINYINT UNSIGNED NULL,             
  crew            TINYINT UNSIGNED NULL,            
  ammunition      TINYINT UNSIGNED NULL,          
  weapons_go      TINYINT(1) NOT NULL DEFAULT 1,      
  comms_go        TINYINT(1) NOT NULL DEFAULT 1,        
  overall_percent TINYINT UNSIGNED NULL,                 
  remarks         VARCHAR(255) NULL,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_rollup_asset FOREIGN KEY (asset_id) REFERENCES assets(id)
) ENGINE=InnoDB;

-- ============================================================
-- Seed data so I can display something on startup
-- ============================================================

-- CARRIER (CVN-70)
INSERT INTO assets (kind, vessel_type, vessel_name, hull_number)
VALUES ('CARRIER','Carrier','USS Carl Vinson','CVN-70');

-- DESTROYERS
INSERT INTO assets (kind, vessel_type, vessel_name, hull_number) VALUES
('DESTROYER','Destroyer','USS Hopper','DDG-70'),
('DESTROYER','Destroyer','USS Kidd','DDG-100'),
('DESTROYER','Destroyer','USS Sterett','DDG-104'),
('DESTROYER','Destroyer','USS William P. Lawrence','DDG-110');

-- SUBMARINES
INSERT INTO assets (kind, vessel_type, vessel_name, hull_number) VALUES
('SUBMARINE','Submarine',NULL,'SSN-775'),
('SUBMARINE','Submarine',NULL,'SSN-788');

-- CARRIER AIR WING TWO
INSERT INTO assets (kind, vessel_type, vessel_name, hull_number) VALUES
('SQUADRON','Squadron',NULL,'VFA-2'),
('SQUADRON','Squadron',NULL,'VFA-97'),
('SQUADRON','Squadron',NULL,'VFA-113'),
('SQUADRON','Squadron',NULL,'VFA-192'),
('SQUADRON','Squadron',NULL,'VAW-113'),
('SQUADRON','Squadron',NULL,'VAQ-136'),
('SQUADRON','Squadron',NULL,'VRM-30'),
('SQUADRON','Squadron',NULL,'HSC-4'),
('SQUADRON','Squadron',NULL,'HSM-78');

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'GREEN', 92, 95, 88, 1, 1, 90, 'CVN-70 fully mission capable.'
FROM assets WHERE kind='CARRIER' AND hull_number='CVN-70';

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'GREEN', 88, 91, 85, 1, 1, NULL, 'Systems nominal.'
FROM assets WHERE kind='DESTROYER' AND hull_number IN ('DDG-70','DDG-100','DDG-110');

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'AMBER', 71, 87, 76, 1, 1, NULL, 'Awaiting parts for SPY radar coolant pump.'
FROM assets WHERE kind='DESTROYER' AND hull_number='DDG-104';

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'GREEN', 89, 93, 90, 1, 1, NULL, 'Ready.'
FROM assets WHERE kind='SUBMARINE';

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'GREEN', 85, 90, 82, 1, 1, NULL, 'Sortie-ready.'
FROM assets WHERE kind='SQUADRON' AND hull_number IN ('VFA-2','VFA-97','VFA-113','VAW-113','VAQ-136','VRM-30','HSC-4','HSM-78');

INSERT INTO asset_rollup (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks)
SELECT id, 'AMBER', 68, 84, 70, 1, 1, NULL, 'Parts backlog impacting sortie rate.'
FROM assets WHERE kind='SQUADRON' AND hull_number='VFA-192';

CREATE INDEX idx_assets_kind ON assets(kind);