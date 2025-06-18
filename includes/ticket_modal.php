<!-- MODAL MUA VÉ DÙNG CHUNG CHO TẤT CẢ SỰ KIỆN -->
<div class="modal fade" id="infoModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="min-height: 550px;">
      <form id="infoForm" method="POST" action="../process/confirm_booking.php">
        <div class="modal-header">
          <h5 class="modal-title">Thông tin mua vé</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="event_id" id="modalEventId">
          <input type="hidden" name="type" id="modalEventType">

          <input type="hidden" name="seats" id="selectedSeatsInput">

          <div class="row">
            <!-- Cột nhập thông tin -->
            <div class="col-md-7">
              <!-- Họ và tên -->
              <div class="mb-3">
                <label>Họ và tên</label>
                <input class="form-control" name="fullname" required placeholder="Điền đầy đủ họ tên của bạn">
                <div class="invalid-feedback"></div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label>Email</label>
                <input class="form-control" name="email" required placeholder="Điền email của bạn ">
                <div class="invalid-feedback"></div>
              </div>

              <!-- Số điện thoại -->
              <div class="mb-3">
                <label>Số điện thoại</label>
                <input class="form-control" name="phone" required placeholder="Điền số điện thoại của bạn" >
                <div class="invalid-feedback"></div>
              </div>

              <!-- Phương thức thanh toán -->
              <div class="mb-3">
                <label>Phương thức thanh toán</label>
                <select class="form-select" name="method" required>
                  <option value="vnpay">Vnpay</option>
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


<!-- Modal thông báo thành công -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="mx-auto mb-3" style="width: 80px;">
        <lord-icon
          src="https://cdn.lordicon.com/lupuorrc.json"
          trigger="loop"
          delay="1000"
          colors="primary:#0ab39c,secondary:#0ab39c"
          style="width:80px;height:80px">
        </lord-icon>
      </div>
      <h4 class="text-success fw-bold">Đặt vé thành công!</h4>
      <p>Bạn đã đặt vé thành công!</p>
      <button class="btn btn-outline-success" data-bs-dismiss="modal">Đóng</button>
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

    // Reset lại các lỗi nếu có
    $("#infoForm input").removeClass("is-invalid");
    $("#infoForm .invalid-feedback").text("");

    $("#infoModal").modal("show");
  });
});
</script>
