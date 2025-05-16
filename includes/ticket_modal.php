<!-- MODAL MUA VÉ DÙNG CHUNG CHO TẤT CẢ SỰ KIỆN -->
<div class="modal fade" id="infoModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="min-height: 550px;">
      <form id="infoForm">
        <div class="modal-header">
          <h5 class="modal-title">Thông tin mua vé</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="event_id" id="modalEventId">
          <input type="hidden" name="type" id="modalEventType">

          <div class="row">
            <!-- Cột nhập thông tin -->
            <div class="col-md-7">
              <!-- Họ và tên -->
              <div class="mb-3">
                <label>Họ và tên</label>
                <input class="form-control" name="full_name" required>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label>Email</label>
                <input class="form-control" name="email" required>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Số điện thoại -->
              <div class="mb-3">
                <label>Số điện thoại</label>
                <input class="form-control" name="phone" required>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Số lượng vé -->
              <div class="mb-3">
                <label>Số lượng vé</label>
                <input type="number" min="1" value="1" class="form-control" name="quantity" id="ticketQty" required>
                <div class="invalid-feedback"></div>
              </div>

              <!-- Phương thức thanh toán -->
              <div class="mb-3">
                <label>Phương thức thanh toán</label>
                <select class="form-select" name="payment_method" required>
                  <option value="momo">MoMo</option>
                  <option value="bank">Chuyển khoản ngân hàng</option>
                </select>
              </div>
            </div>

            <!-- Cột hiển thị QR -->
            <div class="col-md-5 d-flex align-items-center justify-content-center">
              <img src="../assets/images/gaudeptrai2.jpg" alt="QR Code" class="img-fluid rounded" style="max-width: 200px;">
            </div>
          </div>
        </div>

        <div class="text-center mb-3">
          <button type="submit" class="btn" style="background-color: #ff5722; color: white;">Xác nhận mua vé</button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- MODAL: Thành công -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">

    <!-- Bỏ modal-content -->
    <div class="success-box text-center">

      <!-- Tích xanh -->
      <div class="success-icon-box mb-2 mt-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="white" class="bi bi-check-lg" viewBox="0 0 16 16">
          <path d="M13.485 1.929a.75.75 0 0 1 1.06 1.06l-8.25 8.25a.75.75 0 0 1-1.06 0L1.454 7.517a.75.75 0 1 1 1.06-1.06l3.721 3.72 7.25-7.25z"/>
        </svg>
      </div>

      <h5 class="text-success fw-bold mt-2 mb-1">Đặt vé thành công!</h5>
      <p id="successMessage" class="success-message-text">Cảm ơn bạn đã mua vé.</p>

      <button type="button" class="btn btn-outline-success btn-sm mt-2 mb-3" data-bs-dismiss="modal">Đóng</button>
    </div>

  </div>
</div>

<script>
$(document).ready(function () {
  // Mở modal
  $(".openModalBuy").click(function () {
    const id = $(this).data("id");
    const type = $(this).data("type");

    $("#modalEventId").val(id);
    $("#modalEventType").val(type);
    $("#ticketQty").val(1); // reset

    // Reset lại các lỗi nếu có
    $("#infoForm input").removeClass("is-invalid");
    $("#infoForm .invalid-feedback").text("");

    $("#infoModal").modal("show");
  });

  // Gửi form
  $("#infoForm").submit(function (e) {
    e.preventDefault();

    // Reset lỗi cũ
    $("#infoForm input").removeClass("is-invalid");
    $("#infoForm .invalid-feedback").text("");

    const name = $("input[name='full_name']").val().trim();
    const email = $("input[name='email']").val().trim();
    const phone = $("input[name='phone']").val().trim();
    const quantity = parseInt($("#ticketQty").val());

    let hasError = false;

    // Họ tên
    if (!/^[A-Za-zÀ-ỹ\s]+$/.test(name)) {
      $("input[name='full_name']").addClass("is-invalid");
      $("input[name='full_name']").next(".invalid-feedback").text("Tên không hợp lệ (không chứa số/ký tự đặc biệt).");
      hasError = true;
    }

    // Email
    

    // SĐT
    if (!/^\d{9,12}$/.test(phone)) {
      $("input[name='phone']").addClass("is-invalid");
      $("input[name='phone']").next(".invalid-feedback").text("Số điện thoại phải từ 9–12 chữ số.");
      hasError = true;
    }

    // Vé
    if (isNaN(quantity) || quantity < 1) {
      $("input[name='quantity']").addClass("is-invalid");
      $("input[name='quantity']").next(".invalid-feedback").text("Số lượng vé phải lớn hơn 0.");
      hasError = true;
    }

    if (hasError) return;

    let formData = $(this).serialize();

    $.post("../process/process_ticket.php", formData, function (res) {
      if (res.status === "success") {
        $("#infoModal").modal("hide");
        $("#successMessage").text(res.message || "Bạn đã đặt vé thành công.");
        const modal = new bootstrap.Modal(document.getElementById("successModal"));
        modal.show();
      } else {
        alert(res.message || "Có lỗi xảy ra.");
      }
    }, "json").fail(function () {
      alert("Không thể gửi dữ liệu. Vui lòng thử lại.");
    });
  });
});
</script>
