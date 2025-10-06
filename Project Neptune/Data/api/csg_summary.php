<?php
/**
 * Overall CSG status and asset breakdown
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';
$pdo = getPDO();

$sql = "
  SELECT a.id, a.kind, a.vessel_type, a.vessel_name, a.hull_number,
         r.color, r.fuel, r.crew, r.ammunition, r.weapons_go, r.comms_go,
         r.overall_percent, r.remarks
  FROM assets a
  JOIN asset_rollup r ON r.asset_id = a.id
  ORDER BY a.kind, a.hull_number
";
$rows = $pdo->query($sql)->fetchAll();

if (!$rows) {
  echo json_encode([
    'csg'=>['color'=>'AMBER','go'=>true,'red_count'=>0,'amber_count'=>0,'total'=>0],
    'assets'=>[]
  ]);
  exit;
}

$red=0; $amber=0; $total=count($rows);
foreach ($rows as $row) {
  if ($row['color']==='RED') $red++;
  elseif ($row['color']==='AMBER') $amber++;
}
$csgColor = 'GREEN';
if ($red>0) $csgColor = ($red/$total <= 0.10) ? 'AMBER' : 'RED';
elseif ($amber>0) $csgColor = 'AMBER';
$go = ($csgColor !== 'RED');

$assets = [];
foreach ($rows as $r) {
  $assets[] = [
    'id'          => (int)$r['id'],
    'kind'        => $r['kind'],
    'name'        => $r['vessel_name'] ?: $r['vessel_type'],
    'hull'        => $r['hull_number'],
    'color'       => $r['color'],
    'fuel'        => is_null($r['fuel']) ? null : (int)$r['fuel'],
    'crew'        => is_null($r['crew']) ? null : (int)$r['crew'],
    'ammunition'  => is_null($r['ammunition']) ? null : (int)$r['ammunition'],
    'weapons_go'  => (bool)$r['weapons_go'],
    'comms_go'    => (bool)$r['comms_go'],
    'remarks'     => $r['remarks']
  ];
}

echo json_encode([
  'csg'    => ['color'=>$csgColor,'go'=>$go,'red_count'=>$red,'amber_count'=>$amber,'total'=>$total],
  'assets' => $assets
]);