<?php

class DataModel
{

    /** @var mysqli */
    private $_sql;

    public function __construct()
    {
        $this->open();
    }

    public function open()
    {
        require_once BASEDIR . 'config/config.php';
        $this->_sql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    public function close()
    {
        $this->_sql->close();
    }

    /**
     * @param $aid int
     * @return object|stdClass
     */
    public function fetchAlgorithmByAID($aid)
    {
        $stmt = $this->_sql->prepare("SELECT * FROM algorithm WHERE aid = ?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    public function fetchLatestAlgorithms($amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT aid, name, description, long_description, username, TIMESTAMPDIFF(MINUTE, creation, NOW()) AS age
            FROM algorithm a
            JOIN user u
            ON a.uid=u.uid
            ORDER BY creation DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param $username string
     * @return object|stdClass
     */
    public function fetchUserByUsername($username)
    {
        $username = $this->_sql->real_escape_string($username);

        $stmt = $this->_sql->prepare("SELECT * FROM user WHERE username = ?");
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
    public function fetchUserByMail($email)
    {
        $email = $this->_sql->real_escape_string($email);

        $stmt = $this->_sql->prepare("SELECT * FROM user WHERE email = ?");
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
    public function fetchLoginByUsername($username)
    {
        $username = $this->_sql->real_escape_string($username);

        $stmt = $this->_sql->prepare("SELECT uid, password FROM user WHERE username = ?");
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
    public function fetchLoginByUID($uid)
    {
        $stmt = $this->_sql->prepare("SELECT password FROM user WHERE uid = ?");
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
    public function insertUser($username, $email, $password)
    {
        $username = $this->_sql->real_escape_string($username);
        $email = $this->_sql->real_escape_string($email);
        $password = $this->_sql->real_escape_string($password);

        $stmt = $this->_sql->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
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
    public function insertAlgorithm($uid)
    {
        $stmt = $this->_sql->prepare("INSERT INTO algorithm (uid) VALUES (?)");
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
    public function updateAlgorithmInfo($aid, $name, $desc, $long)
    {
        $name = $this->_sql->real_escape_string($name);
        $desc = $this->_sql->real_escape_string($desc);
        $long = $this->_sql->real_escape_string($long);

        $stmt = $this->_sql->prepare("UPDATE algorithm SET name=?, description=?, long_description=?, lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("sssi", $name, $desc, $long, $aid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param $aid int
     * @param $variables mixed
     */
    public function updateAlgorithmVariables($aid, $variables)
    {
        $null = null;
        $stmt = $this->_sql->prepare("UPDATE algorithm SET variables=?, lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $variables);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param $aid int The algorithm id.
     * @param $tree mixed JSON representation of tree.
     * @return int Number of changed rows.
     */
    public function updateAlgorithmScript($aid, $tree)
    {
        $null = null;
        $stmt = $this->_sql->prepare("UPDATE algorithm SET tree=?, lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $tree);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param $uid int
     */
    public function updateUserSignInDate($uid)
    {
        $stmt = $this->_sql->prepare("UPDATE user SET lastsignin=NOW() WHERE uid=?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
    }
}
