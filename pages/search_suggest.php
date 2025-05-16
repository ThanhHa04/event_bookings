
<?php
require_once "../config.php";
require_once "../includes/db_connect.php";

header("Content-Type: application/json");

$searchTerm = $_GET['term'] ?? '';
$searchTerm = trim($searchTerm);
if (strlen($searchTerm) < 1) {
    echo json_encode([]);
    exit;
}

function fetchSuggestions($pdo, $table, $type, $term) {
    $stmt = $pdo->prepare("SELECT id, name, date, location, image FROM $table WHERE name LIKE :term OR location LIKE :term LIMIT 5");
    $stmt->execute(['term' => '%' . $term . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as &$event) {
        $event['type'] = $type;
    }
    return $results;
}

$tables = [
    'events' => 'events',
    'music_events' => 'music',
    'special_events' => 'special',
    'featured_events' => 'featured',
    'visit_events' => 'visit'
];

$suggestions = [];
foreach ($tables as $table => $type) {
    $suggestions = array_merge($suggestions, fetchSuggestions($pdo, $table, $type, $searchTerm));
}

echo json_encode($suggestions);
?>
