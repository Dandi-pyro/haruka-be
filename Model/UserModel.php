<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
 
class UserModel extends Database
{
    public function getUsers($limit, $offset, $search)
    {
        if ($search != ""){
            $query = "SELECT * FROM tb_user
                WHERE name LIKE '".$search."' OR
                username LIKE '".$search."' OR
                email LIKE '".$search."' OR
                instansi LIKE '".$search."' LIMIT ".$limit." OFFSET ".$offset;
        }else{
            $query = "SELECT id_user as id, nama_user as name, username, email, instansi, level FROM tb_user ORDER BY id_user ASC LIMIT ".$limit." OFFSET ".$offset;
        }
        
        return $this->select($query);
    }

    public function getUsersByUsernameAndPassword($username, $password)
    {
        // return $this->select("SELECT id_user, username, email, instansi, level FROM tb_user WHERE username = '".$username."' AND password = '".md5($password)."'");
        $query = "SELECT id_user, username, email, instansi, level FROM tb_user WHERE username = ? AND password = ?";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $encryptPassword = md5($password);
            $stmt->bind_param("ss", $username, $encryptPassword);
 
            $stmt->execute();
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }

        $ss = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);       
        $stmt->close();
        return $ss;
    }

    public function registrationUsers($name, $username, $email, $instansi, $password, $level){
        $query = "INSERT INTO `tb_user`(`nama_user`, `username`, `email`, `instansi`, `password`, `level`) VALUES (?,?,?,?,?,?)";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $encryptPassword = md5($password);
            $stmt->bind_param("sssssi", $name, $username, $email, $instansi, $encryptPassword, $level);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT id_user as id, nama_user as name, username, email, instansi, level FROM tb_user WHERE email ='".$email."' LIMIT 1";
        return $this->select($query);
    }

    public function changePassword($email, $password)
    {
        $query = "UPDATE tb_user SET password = ? WHERE email = ?;";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $encryptPassword = md5($password);
            $stmt->bind_param("ss", $encryptPassword, $email);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function changeDataUser($name, $username, $email, $instansi, $level, $id)
    {
        $query = "UPDATE tb_user SET nama_user = ?, username = ?, instansi = ?, level = ?, email = ? WHERE id_user = ?;";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->bind_param("sssisi", $name, $username, $instansi, $level, $email, $id);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function getUserByID($id)
    {
        $query = "SELECT id_user as id, nama_user as name, username, email, instansi, level FROM tb_user WHERE id_user ='".$id."' LIMIT 1";
        return $this->select($query);
    }
}