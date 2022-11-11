<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ApgResponModel extends Database
{
    public function getApgRespon(
        $sort, 
        $order_by, 
        $search, 
        $limit, 
        $offset, 
        $alokasi,
        $agenda_item,
        $judul_ai,
        $dokumen,
        $negara,
        $respon,
        $note,
        $user,
        $id_apg
        )
    {
        if ($search != ""){
            $query = "SELECT * FROM apg_ai_respon
                WHERE alokasi LIKE '%".$search."%' OR
                alokasi LIKE '%".$search."%' OR
                agenda_item LIKE '%".$search."%' OR
                judul_ai LIKE '%".$search."%' OR
                dokumen LIKE '%".$search."%' OR
                negara LIKE '%".$search."%' OR
                respon LIKE '%".$search."%' OR
                note LIKE '%".$search."%' OR
                user LIKE '%".$search."%' OR
                id_apg LIKE '%".$search."%' LIMIT ".$limit." OFFSET ".$offset;
        }else {
            $query = "SELECT * FROM apg_ai_respon";
            $query = $query." WHERE 1 = '1'";

            if(!empty($alokasi)){
                $query = $query." AND alokasi = "."'".$alokasi."'";
            }

            if(!empty($agenda_item)){
                $query = $query." AND agenda_item = "."'".$agenda_item."'";
            }

            if(!empty($judul_ai)){
                $query = $query." AND judul_ai = "."'".$judul_ai."'";
            }

            if(!empty($dokumen)){
                $query = $query." AND dokumen LIKE '%".$dokumen."%'";
            }

            if(!empty($negara)){
                $query = $query." AND negara = "."'".$negara."'";
            }
            
            if(!empty($respon)){
                $query = $query." AND respon = "."'".$respon."'";
            }
            
            if(!empty($note)){
                $query = $query." AND note = "."'".$note."'";
            }

            if(!empty($user)){
                $query = $query." AND user = "."'".$user."'";
            }

            if(!empty($id_apg)){
                $query = $query." AND id_apg = "."'".$id_apg."'";
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

    public function createApgRespon(
        $id_apg, 
        $alokasi,	
        $agenda_item,	
        $judul_ai,	
        $dokumen,	
        $negara,	
        $respon,	
        $note,	
        $user){
        $query = "INSERT INTO `apg_ai_respon`( 
            `id_apg_ai`, 
            `alokasi`,	
            `agenda_item`,	
            `judul_ai`,	
            `dokumen`,	
            `negara`,	
            `respon`,	
            `note`,	
            `user`) 
        VALUES (?,?,?,?,?,?,?,?,?)";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            $stmt->bind_param("issssssss", $id_apg, $alokasi, $agenda_item, $judul_ai, $dokumen, $negara, $respon, $note, $user);
 
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }

    public function deleteApgRespon($id){
        
        $query = "DELETE FROM apg_ai_respon WHERE id_apg_ai_respon = $id";
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

    public function getApgResponByID($id)
    {
        $query = "SELECT id_apg_ai, alokasi, agenda_item, judul_ai, dokumen, negara, respon, note, user, waktu_input FROM apg_ai_respon WHERE id_apg_ai_respon ='$id' LIMIT 1";
        return $this->select($query);
    }

    public function updateApgRespon(
        $alokasi,
        $agenda_item,
        $judul_ai,
        $dokumen,
        $negara,
        $respon,
        $note,
        $user,
        $id
        )
    {
        $query = "UPDATE apg_ai_respon SET 
        `alokasi` = ?,	
        `agenda_item` = ?,	
        `judul_ai` = ?,	
        `dokumen` = ?,	
        `negara` = ?,	
        `respon` = ?,	
        `note` = ?,	
        `user` = ?
        WHERE id_apg_ai_respon = ?;";
        try {
            $stmt = $this->connection->prepare( $query );
 
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            $stmt->bind_param("ssssssssi", 
            $alokasi,
            $agenda_item,
            $judul_ai,
            $dokumen,
            $negara,
            $respon,
            $note,
            $user,
            $id);
 
            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage());
        }
    }
}