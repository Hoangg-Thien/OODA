<?php
class OrderDetail {
    private $db;
    private $conn;
    private $order_id;
    private $order_info;
    private $order_items;
    private $total_amount;

    public function __construct($order_id) {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->order_id = mysqli_real_escape_string($this->conn, $order_id);
        $this->loadOrderInfo();
        $this->loadOrderItems();
        $this->calculateTotal();
    }

    private function loadOrderInfo() {
        $order_sql = "SELECT hd.*, nd.fullname, nd.phone, nd.district, nd.province, nd.user_address
                      FROM hoadon hd 
                      LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                      WHERE hd.order_id = '$this->order_id'";
        
        $order_result = mysqli_query($this->conn, $order_sql);
        $this->order_info = mysqli_fetch_assoc($order_result);
        
        if (!$this->order_info) {
            header('Location: order.php');
            exit;
        }
    }

    private function loadOrderItems() {
        $items_sql = "SELECT cthd.*, sp.product_name, sp.product_price, sp.product_image
                      FROM chitiethoadon cthd
                      LEFT JOIN sanpham sp ON cthd.product_id = sp.product_id
                      WHERE cthd.order_id = '$this->order_id'";
        $this->order_items = mysqli_query($this->conn, $items_sql);
    }

    private function calculateTotal() {
        $total_amount_sql = "SELECT SUM(cthd.quantity * sp.product_price) as total_amount
                            FROM chitiethoadon cthd 
                            JOIN sanpham sp ON cthd.product_id = sp.product_id 
                            WHERE cthd.order_id = '$this->order_id'";
        $total_result = mysqli_query($this->conn, $total_amount_sql);
        $total_row = mysqli_fetch_assoc($total_result);
        $this->total_amount = $total_row['total_amount'];
    }

    public function getOrderInfo() {
        return $this->order_info;
    }

    public function getOrderItems() {
        return $this->order_items;
    }

    public function getTotalAmount() {
        return $this->total_amount;
    }

    public function getOrderId() {
        return $this->order_id;
    }

    public function getFormattedDate() {
        return date('d/m/Y', strtotime($this->order_info['order_date']));
    }

    public function getFormattedTime() {
        return date('H:i', strtotime($this->order_info['order_date']));
    }

    public function getStatusClass() {
        $status = $this->order_info['order_status'];
        switch($status) {
            case 'Đã xác nhận':
                return 'confirmed';
            case 'Chưa xác nhận':
                return 'pending';
            case 'Giao thành công':
                return 'completed';
            case 'Đã hủy':
                return 'cancelled';
            default:
                return '';
        }
    }

    public function getFinalTotal() {
        $shipping_fee = 30000;
        $discount = ($this->total_amount > 0) ? 30000 : 0;
        return $this->total_amount + $shipping_fee - $discount;
    }
}
?>