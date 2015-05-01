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
     * @param int $aid The algorithm id.
     * @return int The number of affected rows.
     */
    public function deleteAlgorithm($aid)
    {
        $stmt = $this->_sql->prepare("DELETE FROM algorithm WHERE aid = ?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param $aid int
     * @return object|stdClass
     */
    public function fetchAlgorithm($aid)
    {
        $stmt = $this->_sql->prepare("SELECT * FROM algorithm WHERE aid = ?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param int $uid
     * @param int $from
     * @param int $amount
     * @return mixed
     */
    public function fetchAlgorithmsOfUser($uid, $from, $amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT *, TIMESTAMPDIFF(MINUTE, date_creation, NOW()) AS age
            FROM algorithm
            WHERE uid = ?
            ORDER BY date_creation DESC
            LIMIT ?, ?
        ");
        $stmt->bind_param("iii", $uid, $from, $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param $amount int
     * @return array
     */
    public function fetchLatestAlgorithms($amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT
              aid, name, description, long_description,
              TIMESTAMPDIFF(MINUTE, date_creation, NOW()) AS age,
              uid, username
            FROM algorithm a
            JOIN user u USING(uid)
            WHERE date_publish IS NOT NULL
            ORDER BY date_creation DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param int $amount
     * @return array
     */
    public function fetchModifiedAlgorithms($amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT
              aid, name, description, long_description,
              TIMESTAMPDIFF(MINUTE, date_lastedit, NOW()) AS modified,
              uid, username
            FROM algorithm a
            JOIN user u USING(uid)
            WHERE date_publish IS NOT NULL
            ORDER BY date_lastedit DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param $uid int
     * @return object|stdClass
     */
    public function fetchUser($uid)
    {
        $stmt = $this->_sql->prepare("SELECT * FROM user WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
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

    public function fetchUsersWithMostAlgorithms($amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT
              u.uid, u.username, u.date_registration,
              COUNT(aid) AS algorithm_count
            FROM user u
            LEFT JOIN algorithm a
            ON a.uid = u.uid
            AND date_publish IS NOT NULL
            GROUP BY uid
            ORDER BY algorithm_count DESC
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
     * @param string $username The user's username.
     * @param string $email The user's email address.
     * @param string $password The user's encrypted password.
     * @return int The new user's id.
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
     * @param int $uid The user id.
     * @return int The new algorithm's id.
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
     * @param int $aid The algorithm id.
     * @param string $name The algorithm's name.
     * @param string $desc A short description.
     * @param string $long A long description.
     * @return int The number of affected rows.
     */
    public function updateAlgorithmInfo($aid, $name, $desc, $long)
    {
        $name = $this->_sql->real_escape_string($name);
        $desc = $this->_sql->real_escape_string($desc);
        $long = $this->_sql->real_escape_string($long);

        $stmt = $this->_sql->prepare("UPDATE algorithm SET name=?, description=?, long_description=?, date_lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("sssi", $name, $desc, $long, $aid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $aid The algorithm id.
     * @param string $variables JSON representation of variables.
     * @return int The number of affected rows.
     */
    public function updateAlgorithmVariables($aid, $variables)
    {
        $null = null;
        $stmt = $this->_sql->prepare("UPDATE algorithm SET variables=?, date_lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $variables);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param $aid int The algorithm id.
     * @param string $tree JSON representation of tree.
     * @return int Number of changed rows.
     */
    public function updateAlgorithmScript($aid, $tree)
    {
        $null = null;
        $stmt = $this->_sql->prepare("UPDATE algorithm SET tree=?, date_lastedit=NOW() WHERE aid=?");
        $stmt->bind_param("bi", $null, $aid);
        $stmt->send_long_data(0, $tree);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $aid The algorithm id.
     * @param string $status The new visibility status ("public" or "private").
     * @return int The number of affected rows.
     */
    public function updateAlgorithmVisibility($aid, $status)
    {
        $value = ($status === "public") ? "NOW()" : "NULL";
        $stmt = $this->_sql->prepare("UPDATE algorithm SET date_publish=$value WHERE aid=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @return int The number of affected rows.
     */
    public function updateUserSignInDate($uid)
    {
        $stmt = $this->_sql->prepare("UPDATE user SET date_lastsignin=NOW() WHERE uid=?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }
}
