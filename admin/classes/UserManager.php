<?php
class UserManager {
    private $db;
    private $limit = 4;

    public function __construct($db) {
        $this->db = $db;
    }

    // 🧭 Lấy tên địa phương (province / district)
    private function getLocationName($table, $id) {
        $id = $this->db->escape($id);
        $sql = "SELECT name FROM $table WHERE {$table}_id = '$id'";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return trim($row['name']);
        }
        return '';
    }

    // 🔍 Xây dựng điều kiện WHERE khi lọc
    private function buildWhereClause($provinceId, $districtId) {
        $where = "";

        if (!empty($provinceId)) {
            $provinceName = $this->getLocationName('province', $provinceId);
            if (!empty($provinceName)) {
                $provinceName = $this->db->escape($provinceName);
                $where .= " AND (nd.province LIKE '%$provinceName%' OR nd.address LIKE '%$provinceName%')";
            }
        }

        if (!empty($districtId)) {
            $districtName = $this->getLocationName('district', $districtId);
            if (!empty($districtName)) {
                $districtName = $this->db->escape($districtName);
                $where .= " AND (nd.district LIKE '%$districtName%' OR nd.address LIKE '%$districtName%')";
            }
        }

        return $where;
    }

    // 📋 Lấy danh sách người dùng có phân trang & lọc
    public function getUsers($page = 1, $provinceId = null, $districtId = null) {
        $offset = ($page - 1) * $this->limit;
        $where = "WHERE 1=1 " . $this->buildWhereClause($provinceId, $districtId);

        $sql = "SELECT user_name, fullname, user_address, user_email, phone, user_role, user_status, district, province 
                FROM nguoidung nd 
                $where 
                LIMIT {$this->limit} OFFSET $offset";
        return $this->db->query($sql);
    }

    // 📊 Tính tổng số trang
    public function getTotalPages() {
        $sql = "SELECT COUNT(*) as total FROM nguoidung";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return ceil($row['total'] / $this->limit);
    }

    // 🗺️ Lấy danh sách tỉnh/thành phố
    public function getProvinces() {
        $sql = "SELECT province_id, name FROM province ORDER BY name";
        return $this->db->query($sql);
    }
}