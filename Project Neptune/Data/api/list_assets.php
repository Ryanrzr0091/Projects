<?php
/**
 * Asset list for a given group
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../db.php';
$pdo = getPDO();

function mapPluralToSingular(string $k): string {
    $k = strtoupper(trim($k));
    return match ($k) {
        'DESTROYERS' => 'DESTROYER',
        'SUBMARINES' => 'SUBMARINE',
        'SQUADRONS'  => 'SQUADRON',
        'CARRIERS', 'CARRIER' => 'CARRIER',
        default => $k
    };
}

$kindParam = $_GET['kind'] ?? '';
if ($kindParam === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Query param "kind" is required.']);
    exit;
}
$kind = mapPluralToSingular($kindParam);

$sql = "
  SELECT a.id, a.vessel_name, a.hull_number, a.vessel_type, r.color
  FROM assets a
  LEFT JOIN asset_rollup r ON r.asset_id = a.id
  WHERE a.kind = ?
  ORDER BY a.hull_number IS NULL, a.hull_number, a.vessel_name
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$kind]);

$out = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $name = $row['vessel_name'] ?: $row['vessel_type'];
    $hn   = $row['hull_number'] ?: '';
    $label = trim($name . ' ' . $hn);
    $out[] = [
        'id'           => (int)$row['id'],
        'label'        => $label,
        'hull_number'  => $hn,
        'vessel_name'  => $row['vessel_name'],
        'vessel_type'  => $row['vessel_type'],
        'color'        => $row['color'] ?? null
    ];
}

echo json_encode($out);