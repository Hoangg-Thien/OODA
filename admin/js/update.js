document.getElementById("saveBtn").addEventListener("click", function () {
    const formData = new FormData();

    // Input cơ bản
    formData.append("product_id", document.getElementById("product-code").value);
    formData.append("product_name", document.getElementById("product-name").value);
    formData.append("product_price", document.getElementById("product-price").value);
    formData.append("product_status", document.getElementById("product-status").value);
    formData.append("product_type", document.getElementById("product-type").value);

    // Kiểm tra ảnh
    const isImageDeleted = document.getElementById("product-image").getAttribute("data-deleted");
    if (isImageDeleted === "true") {
        formData.append("delete_image", "true");
    }

    const imageFile = document.getElementById("image-upload").files[0];
    if (imageFile) {
        formData.append("product_image", imageFile);
    }

    // Gửi dữ liệu
    fetch("update_product.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Cập nhật thành công!");
            location.reload();
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Lỗi kết nối server!");
    });
});
