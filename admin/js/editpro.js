$(document).ready(function() {
    $('.btn-outline-warning.edit').click(function() {
        $('#editModal').modal('show');

    });

    $('#saveBtn').click(function() {
        $('#ModalUP').modal('hide');
    });

    $('#cancelBtn').click(function() {
        $('#ModalUP').modal('hide');
    });

});

// Xử lý chỉnh sửa ảnh
document.getElementById('edit-image').addEventListener('click', function() {
    document.getElementById('image-input').click();
});

document.getElementById('image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('product-image').src = e.target.result;
            // Có thể thêm code để lưu ảnh mới vào server ở đây
        }
        reader.readAsDataURL(file);
    }
});

// Xử lý xóa ảnh
document.getElementById('delete-image').addEventListener('click', function() {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        document.getElementById('product-image').src = '../img/no-image.jpg'; // Ảnh mặc định khi không có ảnh
        // Có thể thêm code để xóa ảnh trên server ở đây
    }
});