document.addEventListener("DOMContentLoaded", function () {
    let editImage = document.getElementById("edit-image");
    let imageUpload = document.getElementById("image-upload");
    let saveImage = document.getElementById("save-image");
    let productImage = document.getElementById("product-image");

    if (editImage && imageUpload) {
        editImage.addEventListener("click", function () {
            imageUpload.click();
        });

        imageUpload.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (productImage) {
                        productImage.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (saveImage && imageUpload) {
        saveImage.addEventListener("click", function () {
            if (imageUpload.files.length > 0) {
                const formData = new FormData();
                formData.append("image", imageUpload.files[0]);
                formData.append("product_id", 1); // Thay ID sản phẩm bằng ID thực tế

                fetch("update_image.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Hiển thị thông báo thành công hoặc lỗi
                })
                .catch(error => console.error("Lỗi:", error));
            } else {
                alert("Vui lòng chọn một hình ảnh trước khi lưu!");
            }
        });
    }
});
