<?php
class Product {
    private $product_id;
    private $product_name;
    private $product_price;
    private $product_image;
    private $category_id;

    public function __construct($product_id, $product_name, $product_price, $product_image, $category_id) {
        $this->product_id = $product_id;
        $this->product_name = $product_name;
        $this->product_price = $product_price;
        $this->product_image = $product_image;
        $this->category_id = $category_id;
    }

    public function getId() { return $this->product_id; }
    public function getName() { return $this->product_name; }
    public function getPrice() { return $this->product_price; }
    public function getImage() { return $this->product_image; }
    public function getCategoryId() { return $this->category_id; }
}
