<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
 
class MaindataModel extends Database
{
    public function getMaindata($sort, $order_by, $min_frekuensi, $max_frekuensi, $wp, $minIndexOverlap, $maxIndexOverlap, $minBandwidth, $maxBandwidth, $search, $limit, $offset)
    {
        if ($search != ""){
            $query = "SELECT * FROM maindata
                WHERE frequency LIKE '%".$search."%' OR
                min_frekuensi LIKE '%".$search."%' OR
                max_frekuensi LIKE '%".$search."%' OR
                bandwidth LIKE '%".$search."%' OR
                indeks_overlap LIKE '%".$search."%' OR
                band LIKE '%".$search."%' OR
                jenis_band LIKE '%".$search."%' OR
                wp LIKE '%".$search."%' OR
                tech_world LIKE '%".$search."%' OR
                tech_indonesia LIKE '%".$search."%' OR
                license LIKE '%".$search."%' OR
                assignment LIKE '%".$search."%' OR
                document LIKE '%".$search."%' OR
                tgl_input LIKE '%".$search."%' OR
                tgl_output LIKE '%".$search."%' OR
                isu_teknis LIKE '%".$search."%' OR
                isu_lain LIKE '%".$search."%' OR
                editor LIKE '%".$search."%' OR
                author  LIKE '%".$search."%' OR
                ref LIKE '%".$search."%' OR
                ket LIKE '%".$search."%' OR
                ide LIKE '%".$search."%' OR
                status_data LIKE '%".$search."%' LIMIT ".$limit." OFFSET ".$offset;
        }else {
            $query = "SELECT * FROM maindata";
            $query = $query." WHERE min_frekuensi >= ".$min_frekuensi;
            
            if ($max_frekuensi != 0){
                $query = $query." AND max_frekuensi <= ".$max_frekuensi;
            }
            
            if(!empty($wp)){
                $query = $query." AND wp = "."'".$wp."'";
            }
    
            if($minIndexOverlap != 0){
                $query = $query." AND indeks_overlap >= ".$minIndexOverlap;
            }
    
            if($maxIndexOverlap != 0){
                $query = $query." AND indeks_overlap <= ".$maxIndexOverlap;
            }
    
            if($minBandwidth != 0){
                $query = $query." AND bandwidth >= ".$minBandwidth;
            }
    
            if($maxBandwidth != 0){
                $query = $query." AND bandwidth <= ".$maxBandwidth;
            }
            
            if(!empty($sort)){
                if (!empty($order_by)) {
                    $query = $query." ORDER BY ".$order_by." ".$sort." LIMIT ".$limit." OFFSET ".$offset;
                }else{
                    $query = $query." ORDER BY no".$sort." LIMIT ".$limit." OFFSET ".$offset;
                }
            }
        }

        return $this->select($query);
    }

    public function getOverlap($min_frekuensi, $max_frekuensi){
        $query1 = "SELECT COUNT(*) AS total FROM maindata WHERE min_frekuensi <= $min_frekuensi AND max_frekuensi >= $max_frekuensi AND $min_frekuensi <= $max_frekuensi AND min_frekuensi <= max_frekuensi";
        $query2 = "SELECT COUNT(*) AS total FROM maindata WHERE min_frekuensi < $min_frekuensi AND min_frekuensi < $max_frekuensi AND $min_frekuensi <= max_frekuensi AND max_frekuensi < $min_frekuensi AND $min_frekuensi <= $min_frekuensi AND min_frekuensi <= max_frekuensi";
        $query3 = "SELECT COUNT(*) AS total FROM maindata WHERE min_frekuensi > $min_frekuensi AND min_frekuensi < $max_frekuensi AND max_frekuensi > $max_frekuensi AND $min_frekuensi <= $min_frekuensi AND min_frekuensi <= max_frekuensi";
        $query4 = "SELECT COUNT(*) AS total FROM maindata WHERE min_frekuensi > $min_frekuensi AND max_frekuensi < $max_frekuensi AND $min_frekuensi <= $max_frekuensi AND min_frekuensi <= max_frekuensi";
        return $this->select($query1)[0]['total'] + $this->select($query2)[0]['total'] + $this->select($query3)[0]['total'] + $this->select($query4)[0]['total'];
    }

    public function getAllData(){
        return $this->select("SELECT * FROM maindata");
    }

    public function updateOverlap($indeks_overlap, $id_maindata)
    {
        $query = "UPDATE maindata SET indeks_overlap = ? WHERE no = ?;";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->bind_param("ii", $indeks_overlap, $id_maindata);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function insertMaindata($min_frekuensi, $max_frekuensi, $bandwidth, $band, $jenis_band, $wp, $tech_world, $tech_indonesia, $license,
    $assignment,
    $document,
    $isu_teknis,
    $isu_lain,
    $ref,
    $ket,
    $ide,
    $id_user){
        $frek = "$min_frekuensi - $max_frekuensi kHz";
        $query = "INSERT INTO `maindata` (`frequency`, 
        `min_frekuensi`, 
        `max_frekuensi`, 
        `bandwidth`, 
        `band`, 
        `jenis_band`, 
        `wp`, 
        `tech_world`, 
        `tech_indonesia`, 
        `license`, 
        `assignment`, 
        `document`, 
        `tgl_input`, 
        `tgl_output`, 
        `isu_teknis`, 
        `isu_lain`, 
        `editor`, 
        `author`, 
        `ref`, 
        `ket`, 
        `ide`, 
        `status_data`, 
        `id_user`) VALUES 
        ($frek, $min_frekuensi, $min_frekuensi, $bandwidth, '$band', '$jenis_band', '$wp', '$tech_world', '$tech_indonesia', '$license',
            '$assignment','$document', current_timestamp(), current_timestamp(), '$isu_teknis', '$isu_lain', 'SYSTEM', 'SYSTEM', 
            '$ref', '$ket', '$ide', 'terverifikasi', $id_user)";
        try {
            $stmt = $this->connection->prepare( $query );
            
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function updateMaindata($id_maindata, $min_frekuensi, $max_frekuensi, $bandwidth, $band, $jenis_band, $wp, $tech_world, $tech_indonesia, $license,
    $assignment,
    $document,
    $isu_teknis,
    $isu_lain,
    $ref,
    $ket,
    $ide
    )
    {
        $frek = "$min_frekuensi - $max_frekuensi kHz";
        $query = "UPDATE maindata SET 
        frequency = '$frek', 
        min_frekuensi = '$min_frekuensi', 
        max_frekuensi = '$max_frekuensi', 
        bandwidth = '$bandwidth', 
        band = '$band', 
        jenis_band = '$jenis_band', 
        wp = '$wp', 
        tech_world = '$tech_world', 
        tech_indonesia = '$tech_indonesia', 
        license = '$license', 
        assignment = '$assignment', 
        document = '$document', 
        isu_teknis = '$isu_teknis', 
        isu_lain = '$isu_lain', 
        ref = '$ref', 
        ket = '$ket', 
        ide ='$ide'
        WHERE 
        no = $id_maindata";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function getDataByID($id_maindata){
        $query = "SELECT * FROM maindata WHERE no = $id_maindata";
        return $this->select($query);
    }

    public function insertHistory(
        $id_maindata,
        $min_frekuensi, 
        $max_frekuensi, 
        $bandwidth, 
        $band, 
        $jenis_band, 
        $wp, 
        $tech_world, 
        $tech_indonesia, 
        $license,
        $assignment,
        $document,
        $isu_teknis,
        $isu_lain,
        $ref,
        $ket,
        $ide,
        $id_user,
        $tgl_input,
        $tgl_output,
        $editor,
        $author,
        $status_data){
        $frek = "$min_frekuensi - $max_frekuensi kHz";
        $query = "INSERT INTO `history` (`id_maindata`, `frequency`, 
        `min_frekuensi`, 
        `max_frekuensi`, 
        `bandwidth`, 
        `band`, 
        `jenis_band`, 
        `wp`, 
        `tech_world`, 
        `tech_indonesia`, 
        `license`, 
        `assignment`, 
        `document`, 
        `tgl_input`, 
        `tgl_output`, 
        `isu_teknis`, 
        `isu_lain`, 
        `editor`, 
        `author`, 
        `ref`, 
        `ket`, 
        `ide`, 
        `status_data`, 
        `id_user`) VALUES 
        ($id_maindata,$frek, $min_frekuensi, $min_frekuensi, $bandwidth, '$band', '$jenis_band', '$wp', '$tech_world', '$tech_indonesia', '$license',
            '$assignment','$document', '$tgl_input', '$tgl_output', '$isu_teknis', '$isu_lain', '$editor', '$author', 
            '$ref', '$ket', '$ide', '$status_data', $id_user)";
        try {
            $stmt = $this->connection->prepare( $query );
            
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function deleteMaindata($id_maindata){
        
        $query = "DELETE FROM maindata WHERE no = $id_maindata";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }
}