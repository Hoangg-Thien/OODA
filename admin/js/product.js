function previewImage(input) {
    var preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<p>Hình ảnh</p>';
    }
}

function validateAndSubmit(event) {
    event.preventDefault();
    
    // Kiểm tra các trường bắt buộc
    const productCode = document.getElementById('product-code').value;
    const productName = document.getElementById('product-name').value;
    const supplier = document.getElementById('supplier').value;
    const category = document.getElementById('category').value;
    const price = document.getElementById('price').value;
    const status = document.getElementById('status').value;
    const productImage = document.getElementById('product-image').files[0];
    
    if (!productCode || !productName || !supplier || !category || !price || !status) {
        alert('Vui lòng điền đầy đủ thông tin sản phẩm!');
        return;
    }
    
    if (!productImage) {
        alert('Vui lòng chọn hình ảnh cho sản phẩm!');
        return;
    }
    
    // Nếu tất cả đều hợp lệ, submit form
    document.querySelector('form').submit();
}

function resetForm() {
    document.querySelector('form').reset();
    document.getElementById('image-preview').innerHTML = '<p>Hình ảnh</p>';
}