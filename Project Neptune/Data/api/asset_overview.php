<?php
/**
 * Asset overview API
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

function carrierColorFromPercent(?int $p): ?string {
    if ($p === null) return null;
    if ($p <= 50) return 'RED';
    if ($p <= 78) return 'AMBER';
    return 'GREEN';
}

function groupColorForDestroyerOrSquadron(int $red, int $amber, int $total): string {
    if ($total <= 0) return 'GREEN';
    if ($red === 0) return ($amber > 0) ? 'AMBER' : 'GREEN';
    $frac = $red / $total;
    return ($frac <= 0.10) ? 'AMBER' : 'RED';
}

function groupColorForSubmarines(int $red, int $amber, int $total): string {
    if ($total === 2) {
        if     ($red === 2) return 'RED';
        elseif ($red === 1) return 'AMBER';
        else /* 0 red */    return ($amber > 0) ? 'AMBER' : 'GREEN';
    }
    return groupColorForDestroyerOrSquadron($red, $amber, max(1, $total));
}

function goFromColor(string $color): bool { return $color !== 'RED'; }

$kindParam = $_GET['kind'] ?? '';
if ($kindParam === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Query param "kind" is required.']);
    exit;
}
$kind = mapPluralToSingular($kindParam);

$sql = "
  SELECT a.id, a.kind, a.vessel_type, a.vessel_name, a.hull_number,
         r.color, r.fuel, r.crew, r.ammunition, r.weapons_go, r.comms_go, r.overall_percent, r.remarks
  FROM assets a
  JOIN asset_rollup r ON r.asset_id = a.id
  WHERE a.kind = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$kind]);
$rows = $stmt->fetchAll();

if (!$rows) {
    http_response_code(404);
    echo json_encode(['error' => 'No assets found for kind: '.$kindParam]);
    exit;
}

/* ---------- group rollup ---------- */

$red=0; $amber=0; $total=count($rows);
foreach ($rows as $row) {
    if ($row['color'] === 'RED') $red++;
    elseif ($row['color'] === 'AMBER') $amber++;
}

if ($kind === 'CARRIER') {
    $c = $rows[0];
    $derived = carrierColorFromPercent(isset($c['overall_percent']) ? (int)$c['overall_percent'] : null);
    $groupColor = $derived ?: $c['color'];
} elseif ($kind === 'DESTROYER' || $kind === 'SQUADRON') {
    $groupColor = groupColorForDestroyerOrSquadron($red, $amber, $total);
} elseif ($kind === 'SUBMARINE') {
    $groupColor = groupColorForSubmarines($red, $amber, $total);
} else {
    $groupColor = 'GREEN';
}
$groupGo = goFromColor($groupColor);

/* ---------- limiting asset ---------- */

usort($rows, function($a, $b){
    $rank = ['RED'=>0,'AMBER'=>1,'GREEN'=>2];
    $ra = $rank[$a['color']] ?? 3;
    $rb = $rank[$b['color']] ?? 3;
    if ($ra !== $rb) return $ra - $rb;

    $ago = (($a['weapons_go']?0:1) + ($a['comms_go']?0:1));
    $bgo = (($b['weapons_go']?0:1) + ($b['comms_go']?0:1));
    if ($ago !== $bgo) return $bgo - $ago; // more NO-GO first

    $amin = min(
        is_numeric($a['fuel']) ? (int)$a['fuel'] : 101,
        is_numeric($a['crew']) ? (int)$a['crew'] : 101,
        is_numeric($a['ammunition']) ? (int)$a['ammunition'] : 101
    );
    $bmin = min(
        is_numeric($b['fuel']) ? (int)$b['fuel'] : 101,
        is_numeric($b['crew']) ? (int)$b['crew'] : 101,
        is_numeric($b['ammunition']) ? (int)$b['ammunition'] : 101
    );
    if ($amin !== $bmin) return $amin - $bmin;

    return strcmp($a['hull_number'] ?? '', $b['hull_number'] ?? '');
});

$lim = $rows[0];

$assetColor = $lim['color'];
if ($kind === 'CARRIER') {
    $derived = carrierColorFromPercent(isset($lim['overall_percent']) ? (int)$lim['overall_percent'] : null);
    if ($derived) $assetColor = $derived;
}
$assetGo = goFromColor($assetColor);

/* ---------- output ---------- */

echo json_encode([
    'group' => [
        'color'       => $groupColor,
        'go'          => $groupGo,
        'red_count'   => $red,
        'amber_count' => $amber,
        'total'       => $total
    ],
    'asset' => [
        'vessel_type'  => $lim['vessel_type'],
        'vessel_name'  => $lim['vessel_name'],
        'hull_number'  => $lim['hull_number'],
        'status_color' => $assetColor,
        'status_go'    => $assetGo,
        'fuel'         => is_null($lim['fuel']) ? null : (int)$lim['fuel'],
        'crew'         => is_null($lim['crew']) ? null : (int)$lim['crew'],
        'ammunition'   => is_null($lim['ammunition']) ? null : (int)$lim['ammunition'],
        'weapons_go'   => (bool)$lim['weapons_go'],
        'comms_go'     => (bool)$lim['comms_go'],
        'remarks'      => $lim['remarks'] ?? ''
    ]
]);