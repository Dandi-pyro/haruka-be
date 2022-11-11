<?php
class ApgResponController extends BaseController
{
    public function createAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $apgResponModel = new ApgResponModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if (empty($bodyRequest["id_apg"]) ||
                 empty($bodyRequest["alokasi"]) || 
                 empty($bodyRequest["agenda_item"]) ||  
                 empty($bodyRequest["judul_ai"]) || 
                 empty($bodyRequest["dokumen"]) ||
                 empty($bodyRequest["negara"]) || 
                 empty($bodyRequest["respon"]) || 
                 empty($bodyRequest["note"]) || 
                 empty($bodyRequest["user"]))
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $apgResponModel->createApgRespon(
                        $bodyRequest["id_apg"],
                        $bodyRequest["alokasi"],
                        $bodyRequest["agenda_item"],
                        $bodyRequest["judul_ai"],
                        $bodyRequest["dokumen"],
                        $bodyRequest["negara"],
                        $bodyRequest["respon"],
                        $bodyRequest["note"],
                        $bodyRequest["user"]
                    );
                    $result = (object) [
                        'id_apg'=>$bodyRequest["id_apg"],
                        'alokasi'=>$bodyRequest["alokasi"],
                        'agenda_item'=>$bodyRequest["agenda_item"],
                        'judul_ai'=>$bodyRequest["judul_ai"],
                        'dokumen'=>$bodyRequest["dokumen"],
                        'negara'=>$bodyRequest["negara"],
                        'respon'=>$bodyRequest["respon"],
                        'note'=>$bodyRequest["note"],
                        'user'=>$bodyRequest["user"]
                ];

                $responseData = $result;
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $temp = json_encode((object) [
                'message' => 'Success create APG!',
                'data' => $responseData,
            ]);
            $this->sendOutput(
                $temp,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $temp = json_encode((object) [
                'message' => $strErrorDesc,
                'data' => null,
            ]);
            $this->sendOutput($temp, 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function deleteAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'DELETE') {
            try {
                $apgResponModel = new ApgResponModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["id"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $res = $apgResponModel->deleteApgRespon($bodyRequest["id"]);
                    if ($res->affected_rows == 0) {
                        $strErrorDesc = 'Not Found Data!';
                        $strErrorHeader = 'HTTP/1.1 404 Not Found';
                    } else {
                        $responseData = null;
                    }
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $temp = json_encode((object) [
                'message' => 'Success delete apg respon!',
                'data' => $responseData,
            ]);
            $this->sendOutput(
                $temp,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $temp = json_encode((object) [
                'message' => $strErrorDesc,
                'data' => null,
            ]);
            $this->sendOutput($temp, 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function updateAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'PUT') {
            try {
                $apgResponModel = new ApgResponModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if (empty($bodyRequest["id"]))
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $lastData = $apgResponModel->getApgResponByID($bodyRequest["id"]);
                    if (count($lastData) == 0){
                        $strErrorDesc = 'Not Found Data!';
                        $strErrorHeader = 'HTTP/1.1 404 Not Found';
                    }else{ 
                        $alokasi= $lastData[0]["alokasi"];
                        $agenda_item= $lastData[0]["agenda_item"];
                        $judul_ai= $lastData[0]["judul_ai"];
                        $dokumen= $lastData[0]["dokumen"];
                        $negara= $lastData[0]["negara"];
                        $respon= $lastData[0]["respon"];
                        $note= $lastData[0]["note"];
                        $user= $lastData[0]["user"];

                        if (!empty($bodyRequest["alokasi"])) {
                            $alokasi = $bodyRequest["alokasi"];
                        }
                        if (!empty($bodyRequest["agenda_item"])) {
                            $agenda_item = $bodyRequest["agenda_item"];
                        }
                        if (!empty($bodyRequest["judul_ai"])) {
                            $judul_ai = $bodyRequest["judul_ai"];
                        }
                        if (!empty($bodyRequest["dokumen"])) {
                            $dokumen = $bodyRequest["dokumen"];
                        }
                        if (!empty($bodyRequest["negara"])) {
                            $negara = $bodyRequest["negara"];
                        }
                        if (!empty($bodyRequest["respon"])) {
                            $respon = $bodyRequest["respon"];
                        }
                        if (!empty($bodyRequest["note"])) {
                            $note = $bodyRequest["note"];
                        }
                        if (!empty($bodyRequest["user"])) {
                            $user = $bodyRequest["user"];
                        }

                        $apgResponModel->updateApgRespon(
                            $alokasi,
                            $agenda_item,
                            $judul_ai,
                            $dokumen,
                            $negara,
                            $respon,
                            $note,
                            $user,
                            $bodyRequest["id"]);
                       $result = (object) [
                            'id_apg_respon'=>$bodyRequest["id"],
                            'agenda_item'=>$agenda_item,
                            'judul_ai'=>$judul_ai,
                            'dokumen'=>$dokumen,
                            'negara'=>$negara,
                            'respon'=>$respon,
                            'note'=>$note,
                            'user'=>$user
                    ];
                    $responseData = $result;
                    }
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $temp = json_encode((object) [
                'message' => 'Success update APG Respon!',
                'data' => $responseData,
            ]);
            $this->sendOutput(
                $temp,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $temp = json_encode((object) [
                'message' => $strErrorDesc,
                'data' => null,
            ]);
            $this->sendOutput($temp, 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $apgResponModel = new ApgResponModel();

                $sort = "ASC";
                if (isset($queries['sort']) && $queries['sort']) {
                    $sort = strtoupper($queries['sort']);
                }

                $order_by = "id_apg_ai";
                if (isset($queries['order_by']) && $queries['order_by']) {
                    $order_by = strtoupper($queries['order_by']);
                }

                $search = "";
                if (isset($queries['search']) && $queries['search']) {
                    $search = strtoupper($queries['search']);
                }

                $limit = 20;
                if (isset($queries['limit']) && $queries['limit']) {
                    $limit = strtoupper($queries['limit']);
                }

                $offset = 0;
                if (isset($queries['offset']) && $queries['offset']) {
                    $offset = strtoupper($queries['offset']);
                }

                $alokasi = "";
                if (isset($queries['alokasi']) && $queries['alokasi']) {
                    $alokasi = strtoupper($queries['alokasi']);
                }

                $agenda_item = "";
                if (isset($queries['agenda_item']) && $queries['agenda_item']) {
                    $agenda_item = strtoupper($queries['agenda_item']);
                }

                $judul_ai = "";
                if (isset($queries['judul_ai']) && $queries['judul_ai']) {
                    $judul_ai = strtoupper($queries['judul_ai']);
                }
                
                $dokumen = "";
                if (isset($queries['dokumen']) && $queries['dokumen']) {
                    $dokumen = strtoupper($queries['dokumen']);
                }

                $negara = "";
                if (isset($queries['negara']) && $queries['negara']) {
                    $negara = strtoupper($queries['negara']);
                }

                $respon = "";
                if (isset($queries['respon']) && $queries['respon']) {
                    $respon = strtoupper($queries['respon']);
                }

                $note = "";
                if (isset($queries['note']) && $queries['note']) {
                    $note = strtoupper($queries['note']);
                }

                $user = "";
                if (isset($queries['user']) && $queries['user']) {
                    $user = strtoupper($queries['user']);
                }

                $id_apg = "";
                if (isset($queries['id_apg']) && $queries['id_apg']) {
                    $id_apg = strtoupper($queries['id_apg']);
                }

                $result = $apgResponModel->getApgRespon(
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
                );
                if (count($result) == 0){
                    $strErrorDesc = 'Not Found Data!';
                    $strErrorHeader = 'HTTP/1.1 404 Not Found';
                }else{
                    $responseData = $result;
                }
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
 
        // send output
        if (!$strErrorDesc) {
            $temp = json_encode((object) [
                'message' => 'Success get APG Respon!',
                'data' => $responseData,
            ]);
            $this->sendOutput(
                $temp,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $temp = json_encode((object) [
                'message' => $strErrorDesc,
                'data' => null,
            ]);
            $this->sendOutput($temp, 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}