<?php
class DataModel {

    private $_sql;

    public function __construct() {
        require_once BASEDIR . 'config/config.php';
        $this->_sql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    public function close() {
        $this->_sql->close();
    }

    /**
     * @param $aid int
     * @return object|stdClass
     */
    public function fetchAlgorithmByAID($aid) {
        $stmt = $this->_sql->prepare("SELECT * FROM algorithms WHERE aid = ?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param $username string
     * @return object|stdClass
     */
    public function fetchUserByUsername($username) {
        $stmt = $this->_sql->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param $email string
     * @return object|stdClass
     */
    public function fetchUserByMail($email) {
        $stmt = $this->_sql->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param $username string
     * @return object|stdClass
     */
    public function fetchLoginByUsername($username) {
        $stmt = $this->_sql->prepare("SELECT uid, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param $uid int
     * @return object|stdClass
     */
    public function fetchLoginByUID($uid) {
        $stmt = $this->_sql->prepare("SELECT password FROM users WHERE uid = ?");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param $username string
     * @param $email string
     * @param $password string
     * @return int
     */
    public function insertUser($username, $email, $password) {
        $stmt = $this->_sql->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        $uid = $stmt->insert_id;
        $stmt->close();
        return $uid;
    }

    /**
     * @param $uid int
     * @param $name string
     * @param $desc string
     * @param $long string
     * @param $variables mixed
     * @param $script mixed
     * @return int
     */
    public function insertAlgorithm($uid, $name, $desc, $long, $variables, $script) {
        $stmt = $this->_sql->prepare("INSERT INTO algorithms (uid, name, description, long_description, variables, script)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssbb", $uid, $name, $desc, $long, $variables, $script);
        $stmt->execute();
        $aid = $stmt->insert_id;
        $stmt->close();
        return $aid;
    }

    /**
     * @param $aid int
     * @param $uid int
     * @param $name string
     * @param $desc string
     * @param $long string
     * @param $variables mixed
     * @param $script mixed
     */
    public function updateAlgorithm($aid, $uid, $name, $desc, $long, $variables, $script) {
        $stmt = $this->_sql->prepare("UPDATE algorithms SET uid=?, name=?, description=?, long_description=?, variables=?, script=? WHERE aid=?");
        $stmt->bind_param("isssbbi", $uid, $name, $desc, $long, $variables, $script, $aid);
        $stmt->execute();
        $stmt->close();
    }
}
