// Xoá ảnh khỏi giao diện
document.getElementById("delete-image").addEventListener("click", function () {
    const imageElement = document.getElementById("product-image");
    const imageInput = document.getElementById("image-upload");

    imageElement.src = "";
    imageElement.alt = "Chưa có ảnh";
    imageElement.style.display = "none";
    imageInput.value = "";
    imageElement.setAttribute("data-deleted", "true");
});

// Chọn ảnh mới
document.getElementById("image-upload").addEventListener("change", function (e) {
    const file = e.target.files[0];
    const imageElement = document.getElementById("product-image");

    if (file && (file.type === "image/png" || file.type === "image/jpeg")) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imageElement.src = e.target.result;
            imageElement.style.display = "block";
            imageElement.removeAttribute("data-deleted"); // không xóa nữa
        };
        reader.readAsDataURL(file);
    } else {
        alert("Vui lòng tải lên ảnh hợp lệ (PNG hoặc JPEG).");
    }
});
