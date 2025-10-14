CREATE TABLE `chitiethoadon` (
  `product_id` varchar(50) NOT NULL,
  `order_id` int(50) NOT NULL,
  `total_amount` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  PRIMARY KEY (`product_id`, `order_id`)
);

CREATE TABLE `chitietphieunhap` (
  `receipt_detail_id` int(11) PRIMARY KEY NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL
);

CREATE TABLE `district` (
  `district_id` int(11) PRIMARY KEY NOT NULL,
  `province_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
);

CREATE TABLE `hoadon` (
  `order_id` int(50) PRIMARY KEY NOT NULL,
  `order_status` varchar(50) DEFAULT null,
  `order_date` datetime NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `PaymentMethod` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `customerName` varchar(255) NOT NULL
);

CREATE TABLE `kho` (
  `product_id` int(11) PRIMARY KEY NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` mediumtext NOT NULL
);

CREATE TABLE `loaisanpham` (
  `category_id` int(11) PRIMARY KEY NOT NULL,
  `name_type` varchar(255) NOT NULL,
  `description` text NOT NULL
);

CREATE TABLE `nguoidung` (
  `fullname` varchar(255) NOT NULL,
  `user_name` varchar(255) PRIMARY KEY NOT NULL,
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
  `supplier_id` int(11) PRIMARY KEY NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `province_id` varchar(255) NOT NULL,
  `date` datetime NOT NULL
);

CREATE TABLE `phieunhap` (
  `receipt_id` int(11) PRIMARY KEY NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `note` text NOT NULL
);

CREATE TABLE `province` (
  `province_id` int(11) PRIMARY KEY NOT NULL,
  `name` varchar(64) NOT NULL
);

CREATE TABLE `sanpham` (
  `product_id` varchar(50) PRIMARY KEY NOT NULL,
  `category_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_status` varchar(255) NOT NULL,
  `product_price` float NOT NULL,
  `product_description` text NOT NULL
);

CREATE TABLE `ward` (
  `wards_id` int(11) PRIMARY KEY NOT NULL,
  `district_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
);

ALTER TABLE `district` COMMENT = 'Quận huyện';

ALTER TABLE `province` COMMENT = 'Tỉnh thành';

ALTER TABLE `ward` COMMENT = 'Xã Phường';

ALTER TABLE `chitiethoadon` ADD CONSTRAINT `fk_ord1` FOREIGN KEY (`order_id`) REFERENCES `hoadon` (`order_id`);

ALTER TABLE `chitiethoadon` ADD CONSTRAINT `fk_pro1` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`);

ALTER TABLE `chitietphieunhap` ADD CONSTRAINT `fk_productID1` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`);

ALTER TABLE `chitietphieunhap` ADD CONSTRAINT `fk_receiptID` FOREIGN KEY (`receipt_id`) REFERENCES `phieunhap` (`receipt_id`);

ALTER TABLE `hoadon` ADD CONSTRAINT `fk_usernamee` FOREIGN KEY (`user_name`) REFERENCES `nguoidung` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `phieunhap` ADD CONSTRAINT `fk_supplierID1` FOREIGN KEY (`supplier_id`) REFERENCES `nhacungcap` (`supplier_id`);

ALTER TABLE `sanpham` ADD CONSTRAINT `fk_categoryID` FOREIGN KEY (`category_id`) REFERENCES `loaisanpham` (`category_id`);

ALTER TABLE `district` ADD FOREIGN KEY (`province_id`) REFERENCES `province` (`province_id`);

ALTER TABLE `ward` ADD FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`);

ALTER TABLE `kho` ADD FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`);

ALTER TABLE `nhacungcap` ADD FOREIGN KEY (`province_id`) REFERENCES `province` (`province_id`);

ALTER TABLE `sanpham` ADD FOREIGN KEY (`supplier_id`) REFERENCES `nhacungcap` (`supplier_id`);
