<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function out($d, $code = 200) { http_response_code($code); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $a = $_GET['a'] ?? '';

    // ---- LIST (with cache version) -------------------------------------
    if ($a === 'list') {
        $r = $pdo->query('SELECT COUNT(*) c, COALESCE(MAX(id),0) i, COALESCE(MAX(updated_at),0) m FROM trips')->fetch();
        $ver = md5($r['c'] . '|' . $r['i'] . '|' . $r['m']);
        if (($_GET['v'] ?? '') === $ver) out(['unchanged' => true, 'version' => $ver]);

        $trips = $pdo->query('SELECT * FROM trips ORDER BY updated_at DESC, id DESC LIMIT 200')->fetchAll();
        $byId = [];
        foreach ($trips as &$t) { $t['members'] = $t['members'] ? json_decode($t['members'], true) : []; $t['items'] = []; $byId[$t['id']] = &$t; }
        unset($t);
        if ($byId) {
            $in = implode(',', array_map('intval', array_keys($byId)));
            foreach ($pdo->query("SELECT * FROM trip_items WHERE trip_id IN ($in) ORDER BY id") as $it) {
                $byId[$it['trip_id']]['items'][] = $it;
            }
        }
        out(['version' => $ver, 'trips' => $trips]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['error' => 'POST required'], 405);
    $in = json_decode(file_get_contents('php://input'), true);
    if (!is_array($in)) out(['error' => 'Invalid JSON'], 400);

    // ---- SAVE (insert or update by client_uid) ------------------------
    if ($a === 'save') {
        $uid  = substr(preg_replace('/[^A-Za-z0-9\-]/', '', (string)($in['client_uid'] ?? '')), 0, 64);
        $name = trim(mb_substr((string)($in['name'] ?? ''), 0, 120));
        if ($uid === '' || $name === '') out(['error' => 'Trip name is required'], 422);
        $trav = max(1, (int)($in['travelers'] ?? 1));
        $days = max(1, (int)($in['days'] ?? 1));
        $mem = [];
        foreach ((array)($in['members'] ?? []) as $m) {
            $m = trim(mb_substr((string)$m, 0, 60));
            if ($m !== '') $mem[] = $m;
            if (count($mem) >= 50) break;
        }
        if ($mem) $trav = count($mem);   // names decide the head count

        $items = []; $total = 0;
        foreach (($in['items'] ?? []) as $it) {
            $qty = max(0, (float)($it['qty'] ?? 0));
            $unit = max(0, (float)($it['unit_cost'] ?? 0));
            $amt = round($qty * $unit, 2);
            $total += $amt;
            $items[] = [mb_substr((string)($it['category'] ?? 'Other'), 0, 40),
                        mb_substr((string)($it['description'] ?? ''), 0, 160), $qty, $unit, $amt];
        }

        $pdo->beginTransaction();
        $s = $pdo->prepare('SELECT id FROM trips WHERE client_uid = ?');
        $s->execute([$uid]);
        $id = $s->fetchColumn();
        $f = [$name, mb_substr((string)($in['route_from'] ?? ''), 0, 80), mb_substr((string)($in['route_to'] ?? ''), 0, 80), $trav, $days, round($total, 2), $mem ? json_encode($mem, JSON_UNESCAPED_UNICODE) : null];
        if ($id) {
            $pdo->prepare('UPDATE trips SET name=?, route_from=?, route_to=?, travelers=?, days=?, total=?, members=? WHERE id=?')->execute([...$f, $id]);
            $pdo->prepare('DELETE FROM trip_items WHERE trip_id=?')->execute([$id]);
        } else {
            $pdo->prepare('INSERT INTO trips (name, route_from, route_to, travelers, days, total, members, client_uid) VALUES (?,?,?,?,?,?,?,?)')->execute([...$f, $uid]);
            $id = $pdo->lastInsertId();
        }
        $ins = $pdo->prepare('INSERT INTO trip_items (trip_id, category, description, qty, unit_cost, amount) VALUES (?,?,?,?,?,?)');
        foreach ($items as $r) $ins->execute([$id, ...$r]);
        $pdo->commit();
        out(['ok' => true, 'id' => (int)$id]);
    }

    // ---- DELETE -------------------------------------------------------
    if ($a === 'delete') {
        $pdo->prepare('DELETE FROM trips WHERE client_uid = ?')->execute([(string)($in['client_uid'] ?? '')]);
        out(['ok' => true]);
    }

    out(['error' => 'Unknown action'], 400);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage());
    out(['error' => 'Server error'], 500);
}
