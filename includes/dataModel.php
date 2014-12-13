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
        $username = $this->_sql->real_escape_string($username);

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
        $email = $this->_sql->real_escape_string($email);

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
        $username = $this->_sql->real_escape_string($username);

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
        $username = $this->_sql->real_escape_string($username);
        $email = $this->_sql->real_escape_string($email);
        $password = $this->_sql->real_escape_string($password);

        $stmt = $this->_sql->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        $uid = $stmt->insert_id;
        $stmt->close();
        return $uid;
    }

    /**
     * @param $uid int
     * @return int
     */
    public function insertAlgorithm($uid) {
        $stmt = $this->_sql->prepare("INSERT INTO algorithms (uid) VALUES (?)");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $aid = $stmt->insert_id;
        $stmt->close();
        return $aid;
    }

    /**
     * @param $aid int
     * @param $name string
     * @param $desc string
     * @param $long string
     */
    public function updateAlgorithmInfo($aid, $name, $desc, $long) {
        $name = $this->_sql->real_escape_string($name);
        $desc = $this->_sql->real_escape_string($desc);
        $long = $this->_sql->real_escape_string($long);

        $stmt = $this->_sql->prepare("UPDATE algorithms SET name=?, description=?, long_description=? WHERE aid=?");
        $stmt->bind_param("sssi", $name, $desc, $long, $aid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param $aid int
     * @param $variables mixed
     */
    public function updateAlgorithmVariables($aid, $variables) {
        $null = NULL;
        $stmt = $this->_sql->prepare("UPDATE algorithms SET variables=? WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $variables);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param $aid int
     * @param $script mixed
     */
    public function updateAlgorithmScript($aid, $script) {
        $null = NULL;
        $stmt = $this->_sql->prepare("UPDATE algorithms SET script=? WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $script);
        $stmt->execute();
        $stmt->close();
    }
}
