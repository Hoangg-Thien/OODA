<?php

class Statistics {
    private $db;
    private $conn;
    private $whereClause = "";

    public function __construct(Database $db) {
        $this->db = $db;
        $this->conn = $db->getConnection();
    }

    public function setDateFilter($dateIn = null, $dateOut = null) {
        if ($dateIn) {
            $dateIn = mysqli_real_escape_string($this->conn, $dateIn);
            $this->whereClause .= " AND DATE(hd.order_date) >= '$dateIn'";
        }
        if ($dateOut) {
            $dateOut = mysqli_real_escape_string($this->conn, $dateOut);
            $this->whereClause .= " AND DATE(hd.order_date) <= '$dateOut'";
        }
    }

    public function getTopCustomers($limit = 5) {
        $ordersSql = "SELECT hd.order_id, hd.order_date, nd.fullname, 
                    (SELECT SUM(cthd.quantity * sp.product_price) 
                     FROM chitiethoadon cthd 
                     JOIN sanpham sp ON cthd.product_id = sp.product_id 
                     WHERE cthd.order_id = hd.order_id) as total_amount
                    FROM hoadon hd 
                    LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                    WHERE 1=1 AND hd.order_status = 'Giao thành công'{$this->whereClause}
                    ORDER BY hd.order_date DESC";

        $ordersResult = mysqli_query($this->conn, $ordersSql);
        $mergedCustomers = [];

        if ($ordersResult && mysqli_num_rows($ordersResult) > 0) {
            while ($order = mysqli_fetch_assoc($ordersResult)) {
                $fullname = $order['fullname'];
                if (!isset($mergedCustomers[$fullname])) {
                    $mergedCustomers[$fullname] = [
                        'fullname' => $fullname,
                        'orders' => [],
                        'total_amount' => 0,
                        'date_range' => []
                    ];
                }
                
                $mergedCustomers[$fullname]['orders'][] = [
                    'order_id' => $order['order_id'],
                    'order_date' => $order['order_date'],
                    'total_amount' => $order['total_amount']
                ];
                
                $mergedCustomers[$fullname]['total_amount'] += $order['total_amount'];
            }
        }

        uasort($mergedCustomers, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        return array_slice($mergedCustomers, 0, $limit);
    }

    public function getTopProducts($limit = 5) {
        $sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                SUM(cthd.quantity) as total_sold,
                SUM(cthd.quantity * sp.product_price) as total_revenue
                FROM sanpham sp
                JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                JOIN hoadon hd ON cthd.order_id = hd.order_id
                WHERE 1=1 AND hd.order_status = 'Giao thành công' {$this->whereClause}
                GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                ORDER BY total_sold DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($product = $result->fetch_assoc()) {
            $product['orders'] = $this->getProductOrders($product['product_id']);
            $product['order_count'] = count($product['orders']);
            $products[] = $product;
        }

        return $products;
    }

    public function getBestSeller() {
        $sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                SUM(cthd.quantity) as total_sold,
                SUM(cthd.quantity * sp.product_price) as total_revenue
                FROM sanpham sp
                JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                JOIN hoadon hd ON cthd.order_id = hd.order_id
                WHERE 1=1 AND hd.order_status = 'Giao thành công' {$this->whereClause}
                GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                ORDER BY total_sold DESC
                LIMIT 1";
        
        $result = mysqli_query($this->conn, $sql);
        if ($result && $product = mysqli_fetch_assoc($result)) {
            $product['orders'] = $this->getProductOrders($product['product_id']);
            $product['order_count'] = count($product['orders']);
            return $product;
        }
        return null;
    }

    public function getWorstSeller() {
        $sql = "SELECT sp.product_id, sp.product_name, sp.product_price, sp.product_image, 
                COALESCE(SUM(cthd.quantity), 0) as total_sold,
                COALESCE(SUM(cthd.quantity * sp.product_price), 0) as total_revenue
                FROM sanpham sp
                LEFT JOIN chitiethoadon cthd ON sp.product_id = cthd.product_id
                LEFT JOIN hoadon hd ON cthd.order_id = hd.order_id AND (1=1 AND hd.order_status = 'Giao thành công' {$this->whereClause})
                GROUP BY sp.product_id, sp.product_name, sp.product_price, sp.product_image
                HAVING total_sold > 0
                ORDER BY total_sold ASC
                LIMIT 1";
        
        $result = mysqli_query($this->conn, $sql);
        if ($result && $product = mysqli_fetch_assoc($result)) {
            $product['orders'] = $this->getProductOrders($product['product_id']);
            $product['order_count'] = count($product['orders']);
            return $product;
        }
        return null;
    }

    private function getProductOrders($productId) {
        $sql = "SELECT DISTINCT hd.order_id, hd.order_date, nd.fullname
                FROM hoadon hd
                JOIN chitiethoadon cthd ON hd.order_id = cthd.order_id
                LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                WHERE cthd.product_id = ? 
                AND hd.order_status = 'Giao thành công' {$this->whereClause}
                ORDER BY hd.order_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        
        while ($order = $result->fetch_assoc()) {
            $orders[] = $order;
        }
        
        return $orders;
    }
}