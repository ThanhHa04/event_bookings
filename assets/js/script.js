document.addEventListener("DOMContentLoaded", function () {
    var loginModalEl = document.getElementById("loginModal");
    var registerModalEl = document.getElementById("registerModal");

    var loginModal = new bootstrap.Modal(loginModalEl, { backdrop: "static" });
    var registerModal = new bootstrap.Modal(registerModalEl, { backdrop: "static" });

    // Chuyển từ đăng nhập sang đăng ký (giữ nguyên backdrop)
    document.getElementById("openRegister").addEventListener("click", function () {
        loginModalEl.addEventListener("hidden.bs.modal", function () {
            registerModal.show();
        }, { once: true });

        loginModal.hide();
    });

    // Chuyển từ đăng ký về đăng nhập (giữ nguyên backdrop)
    document.getElementById("openLogin").addEventListener("click", function () {
        registerModalEl.addEventListener("hidden.bs.modal", function () {
            loginModal.show();
        }, { once: true });

        registerModal.hide();
    });

    // Khi đóng modal cuối cùng (đăng nhập hoặc đăng ký), backdrop sẽ bị xóa
    function removeBackdrop() {
        if (!loginModalEl.classList.contains("show") && !registerModalEl.classList.contains("show")) {
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
        }
    }

    // Xóa backdrop chỉ khi modal cuối cùng đóng lại
    loginModalEl.addEventListener("hidden.bs.modal", removeBackdrop);
    registerModalEl.addEventListener("hidden.bs.modal", removeBackdrop);
});
