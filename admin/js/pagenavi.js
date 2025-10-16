let products = [];
let currentPage = 1;
const itemsPerPage = 10;

function fetchAllProducts() {
    fetch('../controllers/get_all_products.php')
        .then(response => response.json())
        .then(data => {
            products = data;
            const savedPage = parseInt(localStorage.getItem('currentPage'), 10);
            if (!isNaN(savedPage) && savedPage > 0) {
                currentPage = savedPage;
            } else {
                currentPage = 1;
            }
            displayProducts(currentPage);
        })
        .catch(error => {
            console.error("Lỗi khi lấy danh sách sản phẩm:", error);
            alert("Không thể tải sản phẩm từ máy chủ.");
        });
}

function displayProducts(page) {
    const productTable = document.getElementById("productTable");
    productTable.innerHTML = "";

    // Hiển thị tất cả sản phẩm
    let start = (page - 1) * itemsPerPage;
    let end = start + itemsPerPage;
    let paginatedItems = products.slice(start, end);

    paginatedItems.forEach(product => {
        const safeImage = product.product_image ? product.product_image : '';
        const safeCategory = product.category_name ? product.category_name : product.category_id;
        const safeSupplier = product.supplier_name ? product.supplier_name : product.supplier_id;
        const row = `
            <tr>
                <td>${product.product_id}</td>
                <td>${product.product_name}</td>
                <td>${safeCategory}</td>
                <td>${safeImage ? `<img src="../img/${safeImage}" alt="${product.product_name}" width="50">` : ''}</td>
                <td>${product.product_price}</td>
                <td>${product.product_status}</td>
                <td>
                    <button class="btn btn-outline-danger btn-sm trash" title="Xóa" onclick="deleteProduct('${product.product_id}')"><i class="fas fa-trash-alt"></i></button>
                    <button class="btn btn-outline-warning btn-sm edit" title="Sửa" onclick="editProduct('${product.product_id}')"><i class="fa fa-edit"></i></button>
                </td>
            </tr>`;
        productTable.innerHTML += row;
    });

    setupPagination();
}

// Xóa sản phẩm
function deleteProduct(productId) {
    const product = products.find(p => p.product_id === productId);
    if (!product) {
        alert("Sản phẩm không tồn tại.");
        return;
    }

    let modalMessage = '';
    if (product.product_status === 'Hiển thị') {
        modalMessage = `
            <div class="alert alert-warning">
                <strong>Cảnh báo!</strong> Bạn có chắc chắn muốn xóa sản phẩm này:
            </div>
            <h4><strong>${product.product_name}</strong></h4>
            <small>Mã sản phẩm: ${product.product_id}</small>
        `;
    } else {
        modalMessage = `
            <div class="alert alert-info">
                <strong>Thông báo:</strong> Sẽ ẩn khỏi giao diện
            </div>
            <p>Sản phẩm:</p>
            <h4><strong>${product.product_name}</strong></h4>
            <small>Mã sản phẩm: ${product.product_id}</small>
        `;
    }

    $('#ModalRM .modal-body').html(modalMessage);
    $('#ModalRM').modal('show');

    $('#confirmDelete').off('click').on('click', function() {
        console.log("Xóa sản phẩm với trạng thái:", product.product_status);
        
        fetch('../controllers/delete_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&status=${encodeURIComponent(product.product_status)}`
        })
        .then(response => response.json())
        .then(data => {
            console.log("Phản hồi từ server:", data);
            
            if (data.success) {
                const index = products.findIndex(p => p.product_id === productId);
                if (index !== -1) {
                    if (product.product_status === 'Ẩn') {
                        // Nếu sản phẩm đang ẩn, cập nhật trạng thái hidden
                        console.log("Cập nhật hidden = 1 cho sản phẩm:", productId);
                        products[index].hidden = 1;
                    } else {
                        // Xóa sản phẩm khỏi mảng hiển thị
                        console.log("Xóa sản phẩm khỏi mảng:", productId);
                        products.splice(index, 1);
                    }
                }

                $('#ModalRM').modal('hide');
                displayProducts(currentPage);
            } else {
                alert('Xử lý thất bại: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi khi gửi yêu cầu:', error);
            alert('Đã có lỗi xảy ra.');
        });
    });
}

// Sửa sản phẩm
function editProduct(productId) {
    fetch(`../controllers/get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById("product-code").value = data.product_id;
            document.getElementById("product-name").value = data.product_name;
            document.getElementById("product-price").value = data.product_price;
            document.getElementById("product-status").value = data.product_status;
            document.getElementById("product-image").src = `../img/${data.product_image}`;

            $("#editModal").modal("show");
        })
        .catch(error => {
            console.error("Lỗi khi lấy thông tin sản phẩm:", error);
            alert("Có lỗi xảy ra khi tải dữ liệu sản phẩm.");
        });
}

function setupPagination() {
    const pagination = document.getElementById("pagination");
    const totalPages = Math.ceil(products.length / itemsPerPage);

    pagination.innerHTML = "";

    pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}" style="${currentPage === 1 ? 'display: none;' : ''}">
            <a class="page-link" href="#" aria-label="Lùi" onclick="changePage(${currentPage - 1})">
                <span aria-hidden="true">&lt;</span>
            </a>
        </li>`;

    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>`;
    }

    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}" style="${currentPage === totalPages ? 'display: none;' : ''}">
            <a class="page-link" href="#" aria-label="Tiếp" onclick="changePage(${currentPage + 1})">
                <span aria-hidden="true">&gt;</span>
            </a>
        </li>`;
}

function changePage(page) {
    const totalPages = Math.ceil(products.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    localStorage.setItem('currentPage', currentPage);
    displayProducts(currentPage);
}

// Chỉ gọi fetchAllProducts khi trang load xong
document.addEventListener("DOMContentLoaded", fetchAllProducts);