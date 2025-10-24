<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/'); // sửa đúng tên folder gốc của bạn
}
include __DIR__ . '/../layout/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>asset/styles/news.css">
  <div class="container">
        <div class="news-container">
            <h1 class="news-title">Tin Tức Mới Nhất</h1>
            
            <div class="news-grid">
                <!-- Tin tức 1 -->
                <div class="news-card">
                    <div class="news-image">
                        <img src="<?= BASE_URL ?>asset/img/trai-cherry-Uc.jpg" alt="Cherry Úc">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Cherry Úc - Nữ hoàng của các loại trái cây nhập khẩu</h2>
                        <p class="news-excerpt">Khám phá hương vị ngọt ngào và lợi ích sức khỏe tuyệt vời từ Cherry Úc cao cấp</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 120</span>
                            <span><i class="fas fa-comment"></i> 5</span>
                            <span><i class="fas fa-heart"></i> 15</span>
                        </div>
                    </div>
                </div>

                <!-- Tin tức 2 -->
                <div class="news-card">
                    <div class="news-image">
                     <img src="<?= BASE_URL ?>asset/img/trai-tao.jpg" alt="Táo Envy">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Táo Envy New Zealand - Vị ngọt tinh tế từ xứ sở Kiwi</h2>
                        <p class="news-excerpt">Táo Envy với vẻ ngoài bắt mắt cùng hương vị đặc biệt đang chinh phục người tiêu dùng sành điệu</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 95</span>
                            <span><i class="fas fa-comment"></i> 3</span>
                            <span><i class="fas fa-heart"></i> 12</span>
                        </div>
                    </div>
                </div>

                <!-- Tin tức 3 -->
                <div class="news-card">
                    <div class="news-image">
                        <img src="<?= BASE_URL ?>asset/img/trai-nho-My.jpg" alt="Nho Mỹ">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Nho xanh không hạt Úc - Vị ngọt mát từ Nam bán cầu</h2>
                        <p class="news-excerpt">Nho xanh Úc với độ giòn đặc trưng và vị ngọt thanh đang là lựa chọn hàng đầu của người tiêu dùng</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 110</span>
                            <span><i class="fas fa-comment"></i> 4</span>
                            <span><i class="fas fa-heart"></i> 18</span>
                        </div>
                    </div>
                </div>

                <!-- Tin tức 4 -->
                <div class="news-card">
                    <div class="news-image">
                        <img src="<?= BASE_URL ?>asset/img/leTrungQuoc.jpg" alt="Lê Trung Quốc">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Lê Trung Quốc - Hương vị tinh tế từ xứ sở tỷ dân</h2>
                        <p class="news-excerpt">Lê Trung Quốc với vẻ ngoài sang trọng và hương vị độc đáo đang được ưa chuộng trên thị trường</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 85</span>
                            <span><i class="fas fa-comment"></i> 2</span>
                            <span><i class="fas fa-heart"></i> 10</span>
                        </div>
                    </div>
                </div>

                <!-- Tin tức 5 -->
                <div class="news-card">
                    <div class="news-image">
                        <img src="<?= BASE_URL ?>asset/img/trai-kiwi.jpg" alt="Kiwi vàng">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Kiwi vàng New Zealand - Kho báu dinh dưỡng từ thiên nhiên</h2>
                        <p class="news-excerpt">Kiwi vàng với hương vị tropical độc đáo và giá trị dinh dưỡng cao đang được ưa chuộng</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 100</span>
                            <span><i class="fas fa-comment"></i> 3</span>
                            <span><i class="fas fa-heart"></i> 14</span>
                        </div>
                    </div>
                </div>

                <!-- Tin tức 6 -->
                <div class="news-card">
                    <div class="news-image">
                        <img src="<?= BASE_URL ?>asset/img/dua-mini-ThaiLan.jpg" alt="Dứa mini Thái Lan">
                    </div>
                    <div class="news-content">
                        <h2 class="news-card-title">Dứa mini Thái Lan - Siêu thực phẩm đến từ Thái Lan</h2>
                        <p class="news-excerpt">Thịt dứa mini Thái Lan màu vàng ươm, mọng nước, ngọt thanh, có vị chua nhẹ và thơm mùi dứa đặc trưng.</p>
                        <div class="news-meta">
                            <span class="news-category">Trái Cây Nhập Khẩu</span>
                            <span class="news-date">27/04/2025</span>
                        </div>
                        <div class="news-stats">
                            <span><i class="fas fa-eye"></i> 90</span>
                            <span><i class="fas fa-comment"></i> 4</span>
                            <span><i class="fas fa-heart"></i> 16</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layout/footer.php'; ?>