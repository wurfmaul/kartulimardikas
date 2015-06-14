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
        require_once BASEDIR . 'includes/settings.php';
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

    public function deleteTags($tags, $aid)
    {
        $values = "";
        $bind_types = "i";
        $bind_vars = [&$bind_types, &$aid];
        for ($i = 0; $i < sizeof($tags); $i++) {
            $values .= "?";
            $bind_types .= "s";
            $bind_vars[] = &$tags[$i];
            if ($i < sizeof($tags) - 1) {
                $values .= ",";
            }
        }
        $stmt = $this->_sql->prepare("DELETE FROM tags WHERE aid = ? AND tag IN ($values)");
        // work-around because $stmt->bind_param($bind_types, $bind_vars) does not work.
        call_user_func_array([$stmt, "bind_param"], $bind_vars);
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
        $stmt = $this->_sql->prepare("
            SELECT * FROM algorithm
            WHERE aid = ?
            AND date_deletion IS NULL
        ");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    public function fetchAlgorithmsByTag($tag)
    {
        $stmt = $this->_sql->prepare("
            SELECT *, TIMESTAMPDIFF(MINUTE, date_creation, NOW()) AS age
            FROM tags
            LEFT JOIN algorithm a USING (aid)
            LEFT JOIN user u USING (uid)
            WHERE tag = ?
        ");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param int $uid
     * @param bool $fetchPrivate
     * @param int $from
     * @param int $amount
     * @return mixed
     */
    public function fetchAlgorithmsOfUser($uid, $fetchPrivate, $from, $amount)
    {
        $privateFilter = $fetchPrivate ? "" : "AND date_publish IS NOT NULL";

        $stmt = $this->_sql->prepare("
            SELECT *, TIMESTAMPDIFF(MINUTE, date_creation, NOW()) AS age
            FROM algorithm
            WHERE uid = ?
            $privateFilter
            AND date_deletion IS NULL
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
            AND a.date_deletion IS NULL
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
            AND a.date_deletion IS NULL
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
     * @param int $uid
     * @param bool $fetchPrivate
     * @param int $amount
     * @return array
     */
    public function fetchModifiedAlgorithmsOfUser($uid, $fetchPrivate, $amount)
    {
        $privateFilter = $fetchPrivate ? "" : "AND date_publish IS NOT NULL";
        $stmt = $this->_sql->prepare("
            SELECT
              aid, name, description, long_description, date_publish,
              TIMESTAMPDIFF(MINUTE, date_lastedit, NOW()) AS modified,
              uid, username
            FROM algorithm a
            JOIN user u USING(uid)
            WHERE a.date_deletion IS NULL
            AND uid = ?
            $privateFilter
            ORDER BY date_lastedit DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $uid, $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @return array
     */
    public function fetchAllTags()
    {
        $stmt = $this->_sql->prepare("SELECT DISTINCT tag FROM tags");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $this->flatten($result->fetch_all());
    }

    /**
     * @param array $array Two-dimensional array with exactly one element in the inner array.
     * @return array One-dimensional array using the inner value instead of the array.
     */
    private function flatten($array)
    {
        return call_user_func_array('array_merge', $array);
    }

    /**
     * @param int $aid The algorithm's id.
     * @return array All the tags the specified algorithm uses.
     */
    public function fetchTags($aid)
    {
        $stmt = $this->_sql->prepare("
            SELECT tag FROM tags
            WHERE aid = ?
        ");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $this->flatten($result->fetch_all());
    }

    public function fetchTagStats($amount)
    {
        $stmt = $this->_sql->prepare("
            SELECT * FROM (
                SELECT
                  tag,
                  COUNT(aid) AS count,
                  COUNT(aid) / (SELECT COUNT(aid) FROM tags) AS total
                FROM tags
                GROUP BY tag
                ORDER BY count DESC
                LIMIT ?) AS grouped
            ORDER BY tag
        ");
        $stmt->bind_param("i", $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param int $uid The user id.
     * @return object|stdClass
     */
    public function fetchUser($uid)
    {
        $stmt = $this->_sql->prepare("
            SELECT * FROM user
            WHERE uid = ?
            AND date_deletion IS NULL
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param string $username The user's name
     * @return object|stdClass
     */
    public function fetchUserByUsername($username)
    {
        $stmt = $this->_sql->prepare("
            SELECT * FROM user
            WHERE username = ?
            AND date_deletion IS NULL
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param string $email The user's email address.
     * @return object|stdClass
     */
    public function fetchUserByMail($email)
    {
        $stmt = $this->_sql->prepare("
            SELECT * FROM user
            WHERE email = ?
            AND date_deletion IS NULL
        ");
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
            WHERE u.date_deletion IS NULL
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

        $stmt = $this->_sql->prepare("
            SELECT uid, password, language, rights FROM user
            WHERE username = ?
            AND date_deletion IS NULL
        ");
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
        $stmt = $this->_sql->prepare("
            SELECT password FROM user
            WHERE uid = ?
            AND date_deletion IS NULL
        ");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_object();
    }

    /**
     * @param int $uid The user id.
     * @return int The new algorithm's id.
     */
    public function insertAlgorithm($uid)
    {
        $stmt = $this->_sql->prepare("
            INSERT INTO algorithm (uid)
            VALUES (?)
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $aid = $stmt->insert_id;
        $stmt->close();
        return $aid;
    }

    public function insertTags($tags, $aid)
    {
        $values = "";
        // preparation of the bind_param method
        $bind_types = "";
        $bind_vars = [&$bind_types];
        for ($i = 0; $i < sizeof($tags); $i++) {
            $values .= "(?, ?)";
            $bind_types .= "si";
            $bind_vars[] = &$tags[$i];
            $bind_vars[] = &$aid;
            if ($i < sizeof($tags) - 1) {
                $values .= ",";
            }
        }
        $stmt = $this->_sql->prepare("
            INSERT INTO tags (tag, aid)
            VALUES $values
        ");
        // work-around because $stmt->bind_param($bind_types, $bind_vars) does not work.
        call_user_func_array([$stmt, "bind_param"], $bind_vars);
        $stmt->execute();
        $count = $stmt->affected_rows;
        $stmt->close();
        return $count;
    }

    /**
     * @param string $username The user's username.
     * @param string $email The user's email address.
     * @param string $password The user's encrypted password.
     * @return int The new user's id.
     */
    public function insertUser($username, $email, $password)
    {
        $password = $this->_sql->real_escape_string($password);

        $stmt = $this->_sql->prepare("
            INSERT INTO user (username, email, password)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param('sss', $username, $email, $password);
        $stmt->execute();
        $uid = $stmt->insert_id;
        $stmt->close();
        return $uid;
    }

    /**
     * @param int $aid The algorithm id.
     * @return int The number of affected rows.
     */
    public function updateDeleteAlgorithm($aid)
    {
        $stmt = $this->_sql->prepare("
            UPDATE algorithm
            SET date_deletion=NOW()
            WHERE aid=?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
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
        $stmt = $this->_sql->prepare("
            UPDATE algorithm
            SET name=?, description=?, long_description=?, date_lastedit=NOW()
            WHERE aid=?
        ");
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
        $stmt = $this->_sql->prepare("
            UPDATE algorithm
            SET variables=?, date_lastedit=NOW()
            WHERE aid=?
        ");
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
        $stmt = $this->_sql->prepare("
            UPDATE algorithm
            SET tree=?, date_lastedit=NOW()
            WHERE aid=?
        ");
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
        $stmt = $this->_sql->prepare("
            UPDATE algorithm
            SET date_publish=$value
            WHERE aid=?
        ");
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
    public function updateDeleteUser($uid)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user u, algorithm a
            SET u.date_deletion=NOW(), a.date_deletion=NOW()
            WHERE u.uid=a.uid
            AND a.date_deletion IS NULL
            AND u.uid=?
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @param string $email The new email address.
     * @return int The number of affected rows.
     */
    public function updateUserEmail($uid, $email)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user
            SET email=?
            WHERE uid=?
        ");
        $stmt->bind_param("si", $email, $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @param string $username The new user name.
     * @return int The number of affected rows.
     */
    public function updateUserName($uid, $username)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user
            SET username=?
            WHERE uid=?
        ");
        $stmt->bind_param("si", $username, $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @param string $hash The hashed new password.
     * @return int The number of affected rows.
     */
    public function updateUserPassword($uid, $hash)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user
            SET password=?
            WHERE uid=?
        ");
        $stmt->bind_param("si", $hash, $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @param string $lang The interface language.
     * @return int The number of affected rows.
     */
    public function updateUserLang($uid, $lang)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user
            SET language=?
            WHERE uid=?
        ");
        $stmt->bind_param("si", $lang, $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

    /**
     * @param int $uid The user id.
     * @return int The number of affected rows.
     */
    public function updateUserSignIn($uid)
    {
        $stmt = $this->_sql->prepare("
            UPDATE user
            SET date_lastsignin=NOW()
            WHERE uid=?
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();
        return $rows;
    }
}
