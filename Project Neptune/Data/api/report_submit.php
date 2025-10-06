<?php
/**
 * Report submit and weight calculation
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/../db.php';
$pdo = getPDO();

function pctOrNull($v){
  if ($v === null) return null;
  if ($v === '')   return null;
  if (!is_numeric($v)) return null;
  $x = (int)$v;
  return max(0, min(100, $x));
}
function colorFromPercent($p){
  if (!is_int($p)) return null;
  if ($p <= 50) return 'RED';
  if ($p <= 78) return 'AMBER';
  return 'GREEN';
}
function coalesce($new, $old){
  return ($new === null) ? $old : $new;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body) || !isset($body['assets']) || !is_array($body['assets'])) {
  http_response_code(400);
  echo json_encode(['error' => 'JSON must contain "assets": [ ... ]']);
  exit;
}

$pdo->beginTransaction();
try {
  $sel = $pdo->prepare("
    SELECT a.kind, r.fuel, r.crew, r.ammunition, r.weapons_go, r.comms_go, r.overall_percent, r.color
    FROM assets a
    LEFT JOIN asset_rollup r ON r.asset_id = a.id
    WHERE a.id = ?
  ");

  $up  = $pdo->prepare("
    INSERT INTO asset_rollup
      (asset_id, color, fuel, crew, ammunition, weapons_go, comms_go, overall_percent, remarks, updated_at)
    VALUES
      (:aid,:color,:fuel,:crew,:ammo,:wep,:com,:overall,:rmk,NOW())
    ON DUPLICATE KEY UPDATE
      color=VALUES(color),
      fuel=VALUES(fuel),
      crew=VALUES(crew),
      ammunition=VALUES(ammunition),
      weapons_go=VALUES(weapons_go),
      comms_go=VALUES(comms_go),
      overall_percent=VALUES(overall_percent),
      remarks=VALUES(remarks),
      updated_at=NOW()
  ");

  $snap= $pdo->prepare("
    INSERT INTO readiness_snapshot
      (asset_id,color,fuel,crew,ammunition,weapons_go,comms_go,notes)
    VALUES (:aid,:color,:fuel,:crew,:ammo,:wep,:com,:notes)
  ");

  $applied = [];

  foreach ($body['assets'] as $a) {
    if (!isset($a['id'])) continue;
    $aid = (int)$a['id'];

    $fuel_in = pctOrNull($a['fuel'] ?? null);
    $crew_in = pctOrNull($a['crew'] ?? null);
    $ammo_in = pctOrNull($a['ammunition'] ?? null);
    $wep_in  = isset($a['weapons_go']) ? (int)(!!$a['weapons_go']) : null;
    $com_in  = isset($a['comms_go'])   ? (int)(!!$a['comms_go'])   : null;
    $overall_in = pctOrNull($a['overall_percent'] ?? null);
    $rmk_in  = isset($a['remarks']) ? trim((string)$a['remarks']) : null;

    $sel->execute([$aid]);
    $cur = $sel->fetch();
    if (!$cur) continue;

    $kind     = $cur['kind'];
    $fuel_cur = is_null($cur['fuel']) ? null : (int)$cur['fuel'];
    $crew_cur = is_null($cur['crew']) ? null : (int)$cur['crew'];
    $ammo_cur = is_null($cur['ammunition']) ? null : (int)$cur['ammunition'];
    $wep_cur  = is_null($cur['weapons_go']) ? 1 : (int)$cur['weapons_go'];
    $com_cur  = is_null($cur['comms_go'])   ? 1 : (int)$cur['comms_go'];
    $overall_cur = is_null($cur['overall_percent']) ? null : (int)$cur['overall_percent'];

    $fuel = coalesce($fuel_in, $fuel_cur);
    $crew = coalesce($crew_in, $crew_cur);
    $ammo = coalesce($ammo_in, $ammo_cur);
    $wep  = coalesce($wep_in,  $wep_cur);
    $com  = coalesce($com_in,  $com_cur);
    $overall = coalesce($overall_in, $overall_cur);
    $rmk  = coalesce($rmk_in, ($cur['remarks'] ?? ''));

    if ($kind === 'CARRIER' && is_int($overall)) {
      $color = colorFromPercent($overall) ?: 'AMBER';
    } else {
      $vals = array_values(array_filter([$fuel, $crew, $ammo], 'is_int'));
      if (count($vals) === 0) {
        $color = $cur['color'] ?: 'AMBER';
      } else {
        $min = min($vals);
        $color = colorFromPercent($min) ?: 'AMBER';
      }
    }

    // Upsert
    $up->execute([
      ':aid'     => $aid,
      ':color'   => $color,
      ':fuel'    => $fuel,
      ':crew'    => $crew,
      ':ammo'    => $ammo,
      ':wep'     => $wep,
      ':com'     => $com,
      ':overall' => $overall,
      ':rmk'     => $rmk
    ]);

    // Snapshot
    $snap->execute([
      ':aid'   => $aid,
      ':color' => $color,
      ':fuel'  => $fuel,
      ':crew'  => $crew,
      ':ammo'  => $ammo,
      ':wep'   => $wep,
      ':com'   => $com,
      ':notes' => $rmk
    ]);

    $applied[] = [
      'id' => $aid,
      'color' => $color,
      'fuel' => $fuel, 'crew' => $crew, 'ammunition' => $ammo,
      'weapons_go' => (bool)$wep, 'comms_go' => (bool)$com,
      'overall_percent' => $overall, 'remarks' => $rmk
    ];
  }

  $pdo->commit();
  echo json_encode(['ok' => true, 'updated' => $applied]);
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['error' => 'CSG report submit failed', 'details' => $e->getMessage()]);
}