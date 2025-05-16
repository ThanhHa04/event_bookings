<?php
require_once "../config.php";
require_once "../includes/db_connect.php";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gợi ý tìm kiếm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .suggestion-box {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }
        .suggestion-box div:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body class="p-5">

<div class="container">
    <h2 class="mb-4">Tìm kiếm sự kiện</h2>
    <div class="position-relative">
        <input type="text" id="searchInput" class="form-control" placeholder="Nhập tên sự kiện..." autocomplete="off">
        <div id="suggestions" class="suggestion-box"></div>
    </div>
</div>

<script>
document.getElementById("searchInput").addEventListener("input", function () {
    const query = this.value.trim();
    const suggestionBox = document.getElementById("suggestions");

    if (query.length === 0) {
        suggestionBox.innerHTML = "";
        return;
    }

    fetch("../pages/search_suggest.php?term=" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            suggestionBox.innerHTML = "";

            if (data.length === 0) {
                suggestionBox.innerHTML = "<div class='p-2 text-muted'>Không có gợi ý.</div>";
                return;
            }

            data.forEach(item => {
                const div = document.createElement("div");
                div.classList.add("p-2", "border-bottom");
                div.style.cursor = "pointer";
                div.textContent = item.name + " (" + item.type + ")";
                div.addEventListener("click", () => {
                    document.getElementById("searchInput").value = item.name;
                    suggestionBox.innerHTML = "";
                });
                suggestionBox.appendChild(div);
            });
        })
        .catch(err => {
            suggestionBox.innerHTML = "<div class='p-2 text-danger'>Lỗi: không thể lấy gợi ý.</div>";
        });
});
</script>

</body>
</html>
