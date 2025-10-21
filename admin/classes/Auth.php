<?php
class Auth {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
        session_name('ADMINSESSID');
        session_start();
    }

    public function login($username, $password) {
        $user = $this->userModel->getUserByUsername($username);

        if (!$user) {
            $this->setError("Tài khoản không tồn tại.");
            return false;
        }

        if (!password_verify($password, $user['hashPass'])) {
            $this->setError("Mật khẩu không đúng.");
            return false;
        }

        if ($user['user_role'] !== 'Nhân viên') {
            $this->setError("Bạn không có quyền truy cập trang này.");
            return false;
        }

        $_SESSION['fullname'] = $user['user_name'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_role'] = $user['user_role'];
        $_SESSION['login_success'] = true;
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_name']);
    }

    public function logout() {
        session_destroy();
    }

    private function setError($message) {
        $_SESSION['error_message'] = $message;
    }

    public function getError() {
        if (isset($_SESSION['error_message'])) {
            $msg = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            return $msg;
        }
        return null;
    }

    public function hasLoginSuccess() {
        if (isset($_SESSION['login_success'])) {
            unset($_SESSION['login_success']);
            return true;
        }
        return false;
    }
}
?>
