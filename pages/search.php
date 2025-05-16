<?php

include '../includes/header.php';
include '../includes/db_connect.php';

$query = $_GET['query'] ?? '';

echo '<div class="container mt-4">';

if (empty($query)) {
    echo "<h4>Vui lòng nhập   từ khóa tìm kiếm.</h4>";
} else {
    $searchTerm = "%" . $query . "%";

    $tables = [
        'music_events' => ['label' => 'Sự kiện Âm nhạc', 'payment' => '../pages/music_payment.php'],
        'featured_events' => ['label' => 'Sự kiện Nổi bật', 'payment' => '../pages/featured_payment.php'],
        'special_events' => ['label' => 'Sự kiện Đặc biệt', 'payment' => '../pages/special_payment.php'],
        'visit_events' => ['label' => 'Sự kiện Tham quan', 'payment' => '../pages/visit_payment.php']
    ];

    echo "<h3>Kết quả tìm kiếm cho: <strong>" . htmlspecialchars($query) . "</strong></h3><hr>";

    $found = false;

    foreach ($tables as $table => $info) {
        $label = $info['label'];
        $paymentPage = $info['payment'];

        $sql = "SELECT id, name, date, location, image FROM $table WHERE name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h4 class='mt-4 mb-3'>$label</h4><div class='row'>";
            while ($row = $result->fetch_assoc()) {
                $link = "{$paymentPage}?event_id={$row['id']}";
                echo "
                <div class='col-md-3 mb-4'>
                    <a href='$link' class='text-decoration-none text-dark'>
                        <div class='card h-100 shadow-sm hover-shadow' style='transition: 0.3s'>
                            <img src='{$row['image']}' class='card-img-top' style='height: 180px; object-fit: cover;' alt='{$row['name']}'>
                            <div class='card-body'>
                                <p class='card-title fw-bold'>{$row['name']}</p>
                                <p class='card-text text-muted' style='font-size: 14px'>{$row['date']}</p>
                            </div>
                        </div>
                    </a>
                </div>";
            }
            echo "</div>";
            $found = true;
        }
    }

    if (!$found) {
        echo "<h5>Không tìm thấy sự kiện nào phù hợp.</h5>";
    }
}

echo '</div>';
include '../includes/footer.php';
?>
