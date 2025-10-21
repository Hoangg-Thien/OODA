<?php
require_once __DIR__ . '/Database.php';

class Order {
    private $db;
    private $conn;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->conn = $db->getConnection();
    }

    public function getProvinces() {
        $sql = "SELECT * FROM province";
        $result = $this->db->query($sql);
        $rows = [];
        if ($result) {
            while ($r = $result->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }

    public function getOrders(array $filters = [], int $page = 1, int $limit = 5) {
        $offset = ($page - 1) * $limit;
        $where = $this->buildWhere($filters);

        $sql = "SELECT hd.*, nd.fullname,
                nd.district AS user_district, nd.province AS user_province, nd.user_address AS profile_address
                FROM hoadon hd
                LEFT JOIN nguoidung nd ON hd.name = nd.user_name
                WHERE 1=1 " . $where . "
                ORDER BY hd.order_date DESC
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $result = $this->db->query($sql);
        $orders = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        return $orders;
    }

    public function getTotalOrders(array $filters = []) {
        $where = $this->buildWhere($filters);
        $sql = "SELECT COUNT(*) as total FROM hoadon hd WHERE 1=1 " . $where;
        $result = $this->db->query($sql);
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    private function buildWhere(array $filters) {
        $where = '';

        // province filter expects province_id -> we look up name then match hd.province or hd.address
        if (!empty($filters['province'])) {
            $pid = $this->db->escape(trim($filters['province']));
            $psql = "SELECT name FROM province WHERE province_id = '" . $pid . "' LIMIT 1";
            $pres = $this->db->query($psql);
            if ($pres && $pres->num_rows > 0) {
                $pdata = $pres->fetch_assoc();
                $pname = $this->db->escape(trim($pdata['name']));
                $where .= " AND (hd.province LIKE '%" . $pname . "%' OR hd.address LIKE '%" . $pname . "%')";
            }
        }

        if (!empty($filters['district'])) {
            $did = $this->db->escape(trim($filters['district']));
            $dsql = "SELECT name FROM district WHERE district_id = '" . $did . "' LIMIT 1";
            $dres = $this->db->query($dsql);
            if ($dres && $dres->num_rows > 0) {
                $ddata = $dres->fetch_assoc();
                $dname = $this->db->escape(trim($ddata['name']));
                $where .= " AND (hd.district LIKE '%" . $dname . "%' OR hd.address LIKE '%" . $dname . "%')";
            }
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $st = $this->db->escape($filters['status']);
            $where .= " AND hd.order_status = '" . $st . "'";
        }

        if (!empty($filters['datein'])) {
            $din = $this->db->escape($filters['datein']);
            $where .= " AND DATE(hd.order_date) >= '" . $din . "'";
        }

        if (!empty($filters['dateout'])) {
            $dout = $this->db->escape($filters['dateout']);
            $where .= " AND DATE(hd.order_date) <= '" . $dout . "'";
        }

        return $where;
    }

    public static function statusMapToCode(): array {
        return [
            'Chưa xác nhận' => 'pending',
            'Đã xác nhận' => 'confirmed',
            'Giao thành công' => 'completed',
            'Đã hủy' => 'cancelled'
        ];
    }

    public static function statusMapToText(): array {
        return [
            'pending' => 'Chưa xác nhận',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Giao thành công',
            'cancelled' => 'Đã hủy'
        ];
    }

    // Normalize address parts and prefer invoice address then profile
    public static function formatAddress(array $order): string {
        $street = '';
        if (!empty($order['address'])) $street = $order['address'];
        elseif (!empty($order['profile_address'])) $street = $order['profile_address'];

        $district = '';
        if (!empty($order['district'])) $district = $order['district'];
        elseif (!empty($order['user_district'])) $district = $order['user_district'];

        $province = '';
        if (!empty($order['province'])) $province = $order['province'];
        elseif (!empty($order['user_province'])) $province = $order['user_province'];

        $rawParts = [
            preg_replace('/\s+/', ' ', trim((string)$street)),
            preg_replace('/\s+/', ' ', trim((string)$district)),
            preg_replace('/\s+/', ' ', trim((string)$province)),
        ];

        $address_parts = [];
        foreach ($rawParts as $p) {
            if ($p !== '' && !in_array($p, $address_parts, true)) {
                $address_parts[] = $p;
            }
        }

        return !empty($address_parts) ? implode(', ', $address_parts) : 'Không có địa chỉ';
    }
}

?>
