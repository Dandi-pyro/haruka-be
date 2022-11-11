<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
 
class ApgModel extends Database
{
    public function getApg(
        $sort, 
        $order_by, 
        $search, 
        $limit, 
        $offset, 
        $alokasi, 
        $agenda_item, 
        $dokumen,	
        $nama_dokumen,	
        $source_dokumen,	
        $summary_dokumen,	
        $tanggal_dokumen,	
        $pic_stakeholder,	
        $pic_kominfo,	
        $file,	
        $user,	
        $waktu_input
        )
    {
        if ($search != ""){
            $query = "SELECT * FROM apg_ai
                WHERE alokasi LIKE '%".$search."%' OR
                agenda_item LIKE '%".$search."%' OR
                dokumen LIKE '%".$search."%' OR
                nama_dokumen LIKE '%".$search."%' OR
                source_dokumen LIKE '%".$search."%' OR
                summary_dokumen LIKE '%".$search."%' OR
                respon LIKE '%".$search."%' OR
                tanggal_dokumen LIKE '%".$search."%' OR
                pic_stakeholder LIKE '%".$search."%' OR
                pic_kominfo LIKE '%".$search."%' OR
                file LIKE '%".$search."%' OR
                user LIKE '%".$search."%' OR
                waktu_input LIKE '%".$search."%' LIMIT ".$limit." OFFSET ".$offset;
        }else {
            $query = "SELECT * FROM apg_ai";
            $query = $query." WHERE 1 = '1'";

            if(!empty($alokasi)){
                $query = $query." AND alokasi = "."'".$alokasi."'";
            }

            if(!empty($agenda_item)){
                $query = $query." AND agenda_item = "."'".$agenda_item."'";
            }

            if(!empty($wp)){
                $query = $query." AND wp = "."'".$wp."'";
            }

            if(!empty($dokumen)){
                $query = $query." AND dokumen LIKE '%".$dokumen."%'";
            }
            
            if(!empty($nama_dokumen)){
                $query = $query." AND nama_dokumen = "."'".$nama_dokumen."'";
            }
            
            if(!empty($source_dokumen)){
                $query = $query." AND source_dokumen = "."'".$source_dokumen."'";
            }

            if(!empty($summary_dokumen)){
                $query = $query." AND summary_dokumen = "."'".$summary_dokumen."'";
            }

            if(!empty($tanggal_dokumen)){
                $query = $query." AND tanggal_dokumen = "."'".$tanggal_dokumen."'";
            }

            if(!empty($pic_stakeholder)){
                $query = $query." AND pic_stakeholder = "."'".$pic_stakeholder."'";
            }

            if(!empty($pic_kominfo)){
                $query = $query." AND pic_kominfo = "."'".$pic_kominfo."'";
            }

            if(!empty($file)){
                $query = $query." AND file = "."'".$file."'";
            }

            if(!empty($user)){
                $query = $query." AND user = "."'".$user."'";
            }
            
            if(!empty($waktu_input)){
                $query = $query." AND waktu_input = "."'".$waktu_input."'";
            }
            
            if(!empty($sort)){
                if (!empty($order_by)) {
                    $query = $query." ORDER BY ".$order_by." ".$sort." LIMIT ".$limit." OFFSET ".$offset;
                }else{
                    $query = $query." ORDER BY id_apg_ai".$sort." LIMIT ".$limit." OFFSET ".$offset;
                }
            }
        }
        
        return $this->select($query);
    }

    public function createAPG($alokasi, $agenda_item,$dokumen,$nama_dokumen,$source_dokumen,$summary_dokumen,$tanggal_dokumen,$pic_stakeholder,$pic_kominfo,$file, $user){
        $query = "INSERT INTO `apg_ai`(`alokasi`,`agenda_item`,`dokumen`,`nama_dokumen`,`source_dokumen`,`summary_dokumen`,`tanggal_dokumen`,`pic_stakeholder`,`pic_kominfo`,`file`,`user`) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            $stmt->bind_param("sssssssssss", $alokasi, $agenda_item,$dokumen,$nama_dokumen,$source_dokumen,$summary_dokumen,$tanggal_dokumen,$pic_stakeholder,$pic_kominfo,$file, $user);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function deleteAPG($id){
        
        $query = "DELETE FROM apg_ai WHERE id_apg_ai = $id";
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

    public function getApgByID($id)
    {
        $query = "SELECT `alokasi`,`agenda_item`,`dokumen`,`nama_dokumen`,`source_dokumen`,`summary_dokumen`,`tanggal_dokumen`,`pic_stakeholder`,`pic_kominfo`,`file`,`user`
         FROM apg_ai WHERE id_apg_ai ='$id'";
        return $this->select($query);
    }

    public function updateAPG($alokasi, $agenda_item,$dokumen,$nama_dokumen,$source_dokumen,$summary_dokumen,$tanggal_dokumen,$pic_stakeholder,$pic_kominfo,$file, $user, $id)
    {
        $query = "UPDATE apg_ai SET 
        `alokasi` = ?,
        `agenda_item` = ?,
        `dokumen` = ?,
        `nama_dokumen` = ?,
        `source_dokumen` = ?,
        `summary_dokumen` = ?,
        `tanggal_dokumen` = ?,
        `pic_stakeholder` = ?,
        `pic_kominfo` = ?,
        `file` = ?,
        `user` = ? 
        WHERE id_apg_ai = ?;";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->bind_param("sssssssssssi", $alokasi, $agenda_item,$dokumen,$nama_dokumen,$source_dokumen,$summary_dokumen,$tanggal_dokumen,$pic_stakeholder,$pic_kominfo,$file, $user, $id);
 
            $stmt->execute();
            
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }
}