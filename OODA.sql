CREATE TABLE `chitiethoadon` (
  `product_id` varchar(50) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  `total_amount` float NOT NULL,
  PRIMARY KEY (`product_id`, `order_id`)
);

CREATE TABLE `chitietphieunhap` (
  `receipt_detail_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL
);

CREATE TABLE `district` (
  `district_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `province_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
);

CREATE TABLE `hoadon` (
  `order_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `order_status` varchar(50),
  `order_date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL
);

CREATE TABLE `kho` (
  `product_id` varchar(50) PRIMARY KEY NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` mediumtext
);

CREATE TABLE `loaisanpham` (
  `category_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name_type` varchar(255) NOT NULL,
  `description` text
);

CREATE TABLE `nguoidung` (
  `user_name` varchar(255) PRIMARY KEY NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `hashPass` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `user_role` varchar(255) NOT NULL,
  `user_status` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL
);

CREATE TABLE `nhacungcap` (
  `supplier_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `province_id` int(11) NOT NULL,
  `date` datetime NOT NULL
);

CREATE TABLE `phieunhap` (
  `receipt_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `total` float NOT NULL,
  `note` text
);

CREATE TABLE `province` (
  `province_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL
);

CREATE TABLE `sanpham` (
  `product_id` varchar(50) PRIMARY KEY NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_status` varchar(255) NOT NULL,
  `product_price` float NOT NULL,
  `product_description` text,
  `supplier_id` int(11) NOT NULL
);

CREATE TABLE `ward` (
  `wards_id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `district_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
);

ALTER TABLE `district` COMMENT = 'Quận huyện';

ALTER TABLE `province` COMMENT = 'Tỉnh thành';

ALTER TABLE `ward` COMMENT = 'Xã Phường';

ALTER TABLE `chitiethoadon` ADD CONSTRAINT `fk_cthd_product` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chitiethoadon` ADD CONSTRAINT `fk_cthd_order` FOREIGN KEY (`order_id`) REFERENCES `hoadon` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `hoadon` ADD CONSTRAINT `fk_hd_user` FOREIGN KEY (`user_name`) REFERENCES `nguoidung` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sanpham` ADD CONSTRAINT `fk_sanpham_category` FOREIGN KEY (`category_id`) REFERENCES `loaisanpham` (`category_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `sanpham` ADD CONSTRAINT `fk_sanpham_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `nhacungcap` (`supplier_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `kho` ADD CONSTRAINT `fk_kho_product` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `chitietphieunhap` ADD CONSTRAINT `fk_ctpn_product` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `chitietphieunhap` ADD CONSTRAINT `fk_ctpn_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `phieunhap` (`receipt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `phieunhap` ADD CONSTRAINT `fk_receipt_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `nhacungcap` (`supplier_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `district` ADD CONSTRAINT `fk_district_province` FOREIGN KEY (`province_id`) REFERENCES `province` (`province_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ward` ADD CONSTRAINT `fk_ward_district` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON DELETE CASCADE ON UPDATE CASCADE;
