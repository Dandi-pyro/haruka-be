<?php
class ApgController extends BaseController
{
    /**
     * "/user/maindata" Endpoint - Get list of maindata
     */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $apgModel = new ApgModel();
 
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

                $dokumen = "";
                if (isset($queries['dokumen']) && $queries['dokumen']) {
                    $dokumen = strtoupper($queries['dokumen']);
                }

                $nama_dokumen = "";
                if (isset($queries['nama_dokumen']) && $queries['nama_dokumen']) {
                    $nama_dokumen = strtoupper($queries['nama_dokumen']);
                }

                $source_dokumen = "";
                if (isset($queries['source_dokumen']) && $queries['source_dokumen']) {
                    $source_dokumen = strtoupper($queries['source_dokumen']);
                }

                $summary_dokumen = "";
                if (isset($queries['summary_dokumen']) && $queries['summary_dokumen']) {
                    $summary_dokumen = strtoupper($queries['summary_dokumen']);
                }

                $tanggal_dokumen = "";
                if (isset($queries['tanggal_dokumen']) && $queries['tanggal_dokumen']) {
                    $tanggal_dokumen = strtoupper($queries['tanggal_dokumen']);
                }

                $pic_stakeholder = "";
                if (isset($queries['pic_stakeholder']) && $queries['pic_stakeholder']) {
                    $pic_stakeholder = strtoupper($queries['pic_stakeholder']);
                }

                $pic_kominfo = "";
                if (isset($queries['pic_kominfo']) && $queries['pic_kominfo']) {
                    $pic_kominfo = strtoupper($queries['pic_kominfo']);
                }

                $file = "";
                if (isset($queries['file']) && $queries['file']) {
                    $file = strtoupper($queries['file']);
                }

                $user = "";
                if (isset($user['offset']) && $queries['user']) {
                    $user = strtoupper($queries['user']);
                }

                $waktu_input = "";
                if (isset($queries['waktu_input']) && $queries['waktu_input']) {
                    $waktu_input = strtoupper($queries['waktu_input']);
                }

                
                $result = $apgModel->getApg($sort, 
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
                );
                $responseData = $result;
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
                'message' => 'Success get data!',
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

    public function createAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $apgModel = new ApgModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                if (empty($bodyRequest["alokasi"]) ||
                 empty($bodyRequest["agenda_item"]) || 
                 empty($bodyRequest["dokumen"]) ||  
                 empty($bodyRequest["nama_dokumen"]) || 
                 empty($bodyRequest["source_dokumen"]) ||
                 empty($bodyRequest["tanggal_dokumen"]) || 
                 empty($bodyRequest["user"]))
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $summary_dokumen = "";
                    $pic_stakeholder = "";
                    $pic_kominfo = "";
                    $file = "";
                    if (!empty($bodyRequest["summary_dokumen"])) {
                        $summary_dokumen = $bodyRequest["summary_dokumen"];
                    } 
                    if (!empty($bodyRequest["pic_stakeholder"])) {
                        $summary_dokumen = $bodyRequest["pic_stakeholder"];
                    } 
                    if (!empty($bodyRequest["pic_kominfo"])) {
                        $summary_dokumen = $bodyRequest["pic_kominfo"];
                    } 
                    if (!empty($bodyRequest["file"])) {
                        $summary_dokumen = $bodyRequest["file"];
                    } 
                    $apgModel->createAPG(
                        $bodyRequest["alokasi"],
                        $bodyRequest["agenda_item"],
                        $bodyRequest["dokumen"],
                        $bodyRequest["nama_dokumen"],
                        $bodyRequest["source_dokumen"],
                        $summary_dokumen,
                        $bodyRequest["tanggal_dokumen"],
                        $pic_stakeholder,
                        $pic_kominfo,
                        $file,
                        $bodyRequest["user"]
                    );
                    $result = (object) [
                        'alokasi' => $bodyRequest['alokasi'],
                        'agenda_item'=>$bodyRequest["agenda_item"],
                        'dokumen'=>$bodyRequest["dokumen"],
                        'nama_dokumen'=>$bodyRequest["nama_dokumen"],
                        'source_dokumen'=>$bodyRequest["source_dokumen"],
                        'tanggal_dokumen'=>$bodyRequest["tanggal_dokumen"],
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
                $apgModel = new ApgModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["id"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $res = $apgModel->deleteAPG($bodyRequest["id"]);
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
                'message' => 'Success delete apg!',
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
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'PUT') {
            try {
                $apgModel = new ApgModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["id"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $res = $apgModel->getApgByID($bodyRequest["id"]);
                    if (count($res) == 0) {
                        $strErrorDesc = 'Not Found Data!';
                        $strErrorHeader = 'HTTP/1.1 404 Not Found';
                    } else {
                        $alokasi = $res[0]["alokasi"];
                        $agenda_item = $res[0]["agenda_item"];
                        $dokumen = $res[0]["dokumen"];
                        $nama_dokumen = $res[0]["nama_dokumen"];
                        $source_dokumen = $res[0]["source_dokumen"];
                        $summary_dokumen = $res[0]["summary_dokumen"];
                        $tanggal_dokumen = $res[0]["tanggal_dokumen"];
                        $pic_stakeholder = $res[0]["pic_stakeholder"];
                        $pic_kominfo = $res[0]["pic_kominfo"];
                        $file = $res[0]["file"];
                        $user = $res[0]["user"];

                        if (!empty($bodyRequest["alokasi"])) {
                            $alokasi = $bodyRequest["alokasi"];
                        }
                        if (!empty($bodyRequest["agenda_item"])) {
                            $agenda_item = $bodyRequest["agenda_item"];
                        }
                        if (!empty($bodyRequest["dokumen"])) {
                            $dokumen = $bodyRequest["dokumen"];
                        }
                        if (!empty($bodyRequest["nama_dokumen"])) {
                            $nama_dokumen = $bodyRequest["nama_dokumen"];
                        }
                        if (!empty($bodyRequest["source_dokumen"])) {
                            $source_dokumen = $bodyRequest["source_dokumen"];
                        }
                        if (!empty($bodyRequest["summary_dokumen"])) {
                            $summary_dokumen = $bodyRequest["summary_dokumen"];
                        }
                        if (!empty($bodyRequest["tanggal_dokumen"])) {
                            $tanggal_dokumen = $bodyRequest["tanggal_dokumen"];
                        }
                        if (!empty($bodyRequest["pic_stakeholder"])) {
                            $pic_stakeholder = $bodyRequest["pic_stakeholder"];
                        }
                        if (!empty($bodyRequest["pic_kominfo"])) {
                            $pic_kominfo = $bodyRequest["pic_kominfo"];
                        }
                        if (!empty($bodyRequest["file"])) {
                            $file = $bodyRequest["file"];
                        }
                        if (!empty($bodyRequest["user"])) {
                            $user = $bodyRequest["user"];
                        }
                        $apgModel->updateAPG($alokasi, $agenda_item,$dokumen,$nama_dokumen,$source_dokumen,$summary_dokumen,$tanggal_dokumen,$pic_stakeholder,$pic_kominfo,$file, $user, $bodyRequest["id"]);
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
                'message' => 'Success update apg!',
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