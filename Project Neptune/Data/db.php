<?php
/**
 * Project Neptune
 * Database Connection + First-Run Bootstrap + Randomized Seed
 */

function connectBare(): PDO {
  $dbHost = getenv('DB_HOST') ?: 'localhost';
  $dbUser = getenv('DB_USER') ?: 'root';
  $dbPass = getenv('DB_PASS') ?: '';
  $dsn = "mysql:host={$dbHost};charset=utf8mb4";
  return new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}

function connectNamed(string $dbName): PDO {
  $dbHost = getenv('DB_HOST') ?: 'localhost';
  $dbUser = getenv('DB_USER') ?: 'root';
  $dbPass = getenv('DB_PASS') ?: '';
  $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
  return new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}

function ensureDatabase(PDO $bare, string $dbName): void {
  $bare->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
}

function ensureTables(PDO $pdo): void {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS assets (
      id           INT AUTO_INCREMENT PRIMARY KEY,
      kind         ENUM('CARRIER','DESTROYER','SUBMARINE','SQUADRON') NOT NULL,
      vessel_type  VARCHAR(32)  NOT NULL,
      vessel_name  VARCHAR(64)  NULL,
      hull_number  VARCHAR(32)  NULL,
      UNIQUE KEY uk_kind_hull (kind, hull_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS asset_rollup (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS readiness_snapshot (
      id          INT AUTO_INCREMENT PRIMARY KEY,
      asset_id    INT NOT NULL,
      color       ENUM('GREEN','AMBER','RED') NOT NULL,
      fuel        TINYINT UNSIGNED NULL,
      crew        TINYINT UNSIGNED NULL,
      ammunition  TINYINT UNSIGNED NULL,
      weapons_go  TINYINT(1) NOT NULL DEFAULT 1,
      comms_go    TINYINT(1) NOT NULL DEFAULT 1,
      notes       VARCHAR(255) NULL,
      recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_snap_asset (asset_id, recorded_at),
      CONSTRAINT fk_snap_asset FOREIGN KEY (asset_id) REFERENCES assets(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
}

function tableEmpty(PDO $pdo, string $table): bool {
  return (int)$pdo->query("SELECT COUNT(*) AS c FROM {$table}")->fetch()['c'] === 0;
}

function seedIfEmpty(PDO $pdo): void {
  if (!tableEmpty($pdo, 'assets')) return;

  // Seed assets
  $assets = [
    ['CARRIER','Carrier','USS Carl Vinson','CVN-70'],
    ['DESTROYER','Destroyer','USS Hopper','DDG-70'],
    ['DESTROYER','Destroyer','USS Kidd','DDG-100'],
    ['DESTROYER','Destroyer','USS Sterett','DDG-104'],
    ['DESTROYER','Destroyer','USS William P. Lawrence','DDG-110'],
    ['SUBMARINE','Submarine',null,'SSN-775'],
    ['SUBMARINE','Submarine',null,'SSN-788'],
    ['SQUADRON','Squadron',null,'VFA-2'],
    ['SQUADRON','Squadron',null,'VFA-97'],
    ['SQUADRON','Squadron',null,'VFA-113'],
    ['SQUADRON','Squadron',null,'VFA-192'],
    ['SQUADRON','Squadron',null,'VAW-113'],
    ['SQUADRON','Squadron',null,'VAQ-136'],
    ['SQUADRON','Squadron',null,'VRM-30'],
    ['SQUADRON','Squadron',null,'HSC-4'],
    ['SQUADRON','Squadron',null,'HSM-78'],
  ];
  $insA = $pdo->prepare("INSERT INTO assets (kind, vessel_type, vessel_name, hull_number) VALUES (?, ?, ?, ?)");
  foreach ($assets as $a) $insA->execute($a);

  // Seed rollups with randomized but plausible values
  $ids = $pdo->query("SELECT id, kind FROM assets ORDER BY kind, id")->fetchAll();

  $insR = $pdo->prepare("
    INSERT INTO asset_rollup
      (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks, updated_at)
    VALUES
      (:id, :color, :fuel, :crew, :ammo, :wep, :com, :pct, :rmk, NOW())
    ON DUPLICATE KEY UPDATE
      color=VALUES(color), fuel=VALUES(fuel), crew=VALUES(crew),
      ammunition=VALUES(ammunition), weapons_go=VALUES(weapons_go),
      comms_go=VALUES(comms_go), overall_percent=VALUES(overall_percent),
      remarks=VALUES(remarks), updated_at=NOW()
  ");

  $pickPct = function(): int {
    $r = mt_rand(1,100);
    if ($r <= 10) return mt_rand(35,50);  // RED-ish
    if ($r <= 30) return mt_rand(60,78);  // AMBER-ish
    return mt_rand(79,96);                // GREEN-ish
  };
  $pickGO = fn()=> (mt_rand(1,100) <= 90) ? 1 : 0;
  $deriveColor = function(?int $p) {
    if ($p === null) return 'AMBER';
    if ($p <= 50) return 'RED';
    if ($p <= 78) return 'AMBER';
    return 'GREEN';
  };

  foreach ($ids as $row) {
    $id   = (int)$row['id'];
    $kind = $row['kind'];

    $fuel = $pickPct();
    $crew = $pickPct();
    $ammo = $pickPct();
    $wep  = $pickGO();
    $com  = $pickGO();
    $pct  = null;
    $rmk  = '';

    if ($kind === 'CARRIER') {
      $pct = (mt_rand(1,100) <= 15) ? mt_rand(50,78) : mt_rand(79,96);
      $color = $deriveColor($pct);
      $rmk = ($color === 'AMBER') ? 'Reduced sortie rate; routine maintenance.' : 'CVN mission capable.';
    } else {
      $min = min($fuel, $crew, $ammo);
      $color = $deriveColor($min);
      $rmk = ($color === 'RED') ? 'Critical deficiency under investigation.'
           : (($color === 'AMBER') ? 'Degradation noted; awaiting parts/crew.' : 'Systems nominal.');
    }

    $insR->execute([
      ':id'=>$id, ':color'=>$color, ':fuel'=>$fuel, ':crew'=>$crew, ':ammo'=>$ammo,
      ':wep'=>$wep, ':com'=>$com, ':pct'=>$pct, ':rmk'=>$rmk
    ]);
  }
}

function getPDO(): PDO {
  static $pdo = null;
  if ($pdo !== null) return $pdo;

  $dbName = getenv('DB_NAME') ?: 'neptune';
  try {
    $pdo = connectNamed($dbName);
  } catch (Throwable $e) {
    $bare = connectBare();
    ensureDatabase($bare, $dbName);
    $pdo = connectNamed($dbName);
  }

  ensureTables($pdo);
  seedIfEmpty($pdo);

  return $pdo;
}