
<link rel="stylesheet" href="../assets/css/style.css">
<footer class="text-white py-4">
    <div class="container">
        <div class="row align-items-center text-center text-lg-start">

            <!-- Logo & Thông tin bản quyền -->
            <div class="col-lg-4 mb-3 mb-lg-0">
                <a href="index.php" class="text-white text-decoration-none">
                    <img src="../assets/images/logo.png" alt="Logo" height="30px">
                </a>
                <p class="mt-2">© 2025 TicketBox. All rights reserved.</p>
                <h6>Email</h6>
                <p><i class="bi bi-envelope"></i> support@ticketve.vn</p>
                <h6>Văn phòng</h6>
                <p><i class="bi bi-geo-alt-fill"></i> Lầu 0, 0 Bà Huyện Thanh Quan, Phường 0, Quận 0, TP. NO CITY</p>
            </div>

            <!-- Điều hướng nhanh -->
            <div class="col-lg-4 mb-3 mb-lg-0">
                <h6>Dành cho khách hàng</h6>
                <p><i class="bi bi-card-list"></i> Điều khoản sử dụng cho khách hàng</p>

                <h6>Dành cho ban tổ chức</h6>
                <p><i class="bi bi-card-list"></i> Điều khoản sử dụng cho ban tổ chức</p>

                <h6>Đăng ký nhận email về các sự kiện hot</h6>
                <form id="subscribeForm">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" id="emailInput" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                        <button type="submit" class="btn1"><i class="bi bi-send"></i></button>
                    </div>
                </form>
            </div>

            <!-- Về công ty chúng tôi -->
            <div class="col-lg-4">
                <h6>Về công ty chúng tôi</h6>
                <p>Quy chế hoạt động</p>
                <p>Chính sách bảo mật thông tin</p>
                <p>Cơ chế giải quyết tranh chấp/ khiếu nại</p>
                <p>Chính sách bảo mật thanh toán</p>
                <p>Chính sách đổi trả và kiểm hàng</p>
                <p>Điều kiện vận chuyển và giao nhận</p>
                <p>Phương thức thanh toán</p>
            </div>

        </div>

        <!-- Gạch ngang ngăn cách -->
        <hr class="my-4">

        <!-- Theo dõi chúng tôi -->
        <div class="text-center">
            <h6>Theo dõi chúng tôi</h6>
            <div class="d-flex justify-content-center">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
            </div>
        </div>

    </div>
</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    $("#subscribeForm").submit(function(event) {
        event.preventDefault(); // Ngăn chặn load lại trang
        console.log("Sự kiện submit đã kích hoạt!");

        var email = $("#emailInput").val();
        console.log("Email nhập: ", email);

        $.ajax({
            url: "../assets/actions/subscribe.php",
            type: "POST",
            data: { email: email },
            dataType: "json",
            success: function(response) {
                console.log("Phản hồi từ server:", response);

                if (response.status === "success") {
                    $("#successMessage").text(response.message);
                    var successModal = new bootstrap.Modal(document.getElementById("successModal"));
                    successModal.show();
                } else {
                    $("#errorMessage").text(response.message); // Cập nhật nội dung trước khi mở modal
                    var errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
                    errorModal.show();
                }
            },
            error: function(xhr, status, error) {
                console.error("Lỗi AJAX:", error);
                $("#errorMessage").text("Lỗi hệ thống: " + error);
                var errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
                errorModal.show();
            }
        });
    });
});
</script>