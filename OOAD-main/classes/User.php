<?php
class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $fullname;

    public function __construct($id, $username, $password, $email, $fullname) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->fullname = $fullname;
    }

    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getEmail() { return $this->email; }
    public function getFullname() { return $this->fullname; }
}
?>
