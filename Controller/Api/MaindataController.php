<?php

use LDAP\Result;

class MaindataController extends BaseController
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
                $maindataModel = new MaindataModel();
 
                $sort = "ASC";
                if (isset($queries['sort']) && $queries['sort']) {
                    $sort = strtoupper($queries['sort']);
                }

                $order_by = "no";
                if (isset($queries['order_by']) && $queries['order_by']) {
                    $order_by = strtoupper($queries['order_by']);
                }

                $min_frekuensi = 0;
                if (isset($queries['min_frekuensi']) && $queries['min_frekuensi']) {
                    $min_frekuensi = strtoupper($queries['min_frekuensi']);
                }

                $max_frekuensi = 0;
                if (isset($queries['max_frekuensi']) && $queries['max_frekuensi']) {
                    $max_frekuensi = strtoupper($queries['max_frekuensi']);
                }

                $wp = "";
                if (isset($queries['wp']) && $queries['wp']) {
                    $wp = strtoupper($queries['wp']);
                }

                $minIndexOverlap = 0;
                if (isset($queries['minIndexOverlap']) && $queries['minIndexOverlap']) {
                    $minIndexOverlap = strtoupper($queries['minIndexOverlap']);
                }

                $maxIndexOverlap = 0;
                if (isset($queries['maxIndexOverlap']) && $queries['maxIndexOverlap']) {
                    $maxIndexOverlap = strtoupper($queries['maxIndexOverlap']);
                }

                $minBandwidth = 0;
                if (isset($queries['minBandwidth']) && $queries['minBandwidth']) {
                    $minBandwidth = strtoupper($queries['minBandwidth']);
                }

                $maxBandwidth = 0;
                if (isset($queries['maxBandwidth']) && $queries['maxBandwidth']) {
                    $maxBandwidth = strtoupper($queries['maxBandwidth']);
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
                
                $result = $maindataModel->getMaindata($sort, $order_by, $min_frekuensi, $max_frekuensi, $wp, $minIndexOverlap, $maxIndexOverlap, $minBandwidth, $maxBandwidth, $search, $limit, $offset);
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
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $maindataModel = new MaindataModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["min_frekuensi"]) ||
                empty($bodyRequest["max_frekuensi"]) ||
                empty($bodyRequest["bandwidth"]) ||
                empty($bodyRequest["band"]) || 
                empty($bodyRequest["jenis_band"]) || 
                empty($bodyRequest["wp"]) ||
                empty($bodyRequest["tech_indonesia"]) || 
                empty($bodyRequest["license"]) ||   
                empty($bodyRequest["assignment"]) || 
                empty($bodyRequest["document"]) ||
                empty($bodyRequest["isu_teknis"]) || 
                empty($bodyRequest["isu_lain"]) || 
                empty($bodyRequest["ref"]) || 
                empty($bodyRequest["ket"]) || 
                empty($bodyRequest["ide"]) ||
                empty($bodyRequest["tech_world"]) ||
                empty($bodyRequest["id_user"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    // Get Overlap and update
                    $maindataModel->insertMaindata($bodyRequest["min_frekuensi"], 
                    $bodyRequest["max_frekuensi"], 
                    $bodyRequest["bandwidth"], 
                    $bodyRequest["band"], 
                    $bodyRequest["jenis_band"], 
                    $bodyRequest["wp"], 
                    $bodyRequest["tech_world"], 
                    $bodyRequest["tech_indonesia"], 
                    $bodyRequest["license"],
                    $bodyRequest["assignment"],
                    $bodyRequest["document"],
                    $bodyRequest["isu_teknis"],
                    $bodyRequest["isu_lain"],
                    $bodyRequest["ref"],
                    $bodyRequest["ket"],
                    $bodyRequest["ide"],
                    $bodyRequest["id_user"]);
                    $result = $maindataModel->getAllData();
                    foreach ($result as $value) {
                        $resultOverlap = $maindataModel->getOverlap($value["min_frekuensi"], $value["max_frekuensi"]);
                        $maindataModel->updateOverlap($resultOverlap, $value["id_maindata"]);   
                    } 
                    $result = (object) [
                    'min_frekuensi'=>$bodyRequest["min_frekuensi"], 
                    'max_frekuensi'=>$bodyRequest["max_frekuensi"], 
                    'bandwidth'=>$bodyRequest["bandwidth"], 
                    'band'=>$bodyRequest["band"], 
                    'jenis_band'=>$bodyRequest["jenis_band"], 
                    'wp'=>$bodyRequest["wp"], 
                    'tech_world'=>$bodyRequest["tech_world"], 
                    'tech_indonesia'=>$bodyRequest["tech_indonesia"], 
                    'license'=>$bodyRequest["license"],
                    'assignment'=>$bodyRequest["assignment"],
                    'document'=>$bodyRequest["document"],
                    'isu_teknis'=>$bodyRequest["isu_teknis"],
                    'isu_lain'=>$bodyRequest["isu_lain"],
                    'ref'=>$bodyRequest["ref"],
                    'ket'=>$bodyRequest["ket"],
                    'ide'=>$bodyRequest["ide"],
                    'id_user'=>$bodyRequest["id_user"]];
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
                'message' => 'Success create data!',
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
                $maindataModel = new MaindataModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["id"]) ||
                empty($bodyRequest["min_frekuensi"]) ||
                empty($bodyRequest["max_frekuensi"]) ||
                empty($bodyRequest["bandwidth"]) ||
                empty($bodyRequest["band"]) || 
                empty($bodyRequest["jenis_band"]) || 
                empty($bodyRequest["wp"]) ||
                empty($bodyRequest["tech_indonesia"]) || 
                empty($bodyRequest["license"]) ||   
                empty($bodyRequest["assignment"]) || 
                empty($bodyRequest["document"]) ||
                empty($bodyRequest["isu_teknis"]) || 
                empty($bodyRequest["isu_lain"]) || 
                empty($bodyRequest["ref"]) || 
                empty($bodyRequest["ket"]) || 
                empty($bodyRequest["ide"]) ||
                empty($bodyRequest["tech_world"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    // Get Overlap and update
                    $lastData = $maindataModel->getDataByID($bodyRequest["id"]);
                    $maindataModel->insertHistory(
                        $lastData[0]["id_maindata"],
                        $lastData[0]["min_frekuensi"],
                        $lastData[0]["max_frekuensi"],
                        $lastData[0]["bandwidth"],
                        $lastData[0]["band"],
                        $lastData[0]["jenis_band"],
                        $lastData[0]["wp"],
                        $lastData[0]["tech_world"],
                        $lastData[0]["tech_indonesia"],
                        $lastData[0]["license"],
                        $lastData[0]["assignment"],
                        $lastData[0]["document"],
                        $lastData[0]["isu_teknis"],
                        $lastData[0]["isu_lain"],
                        $lastData[0]["ref"],
                        $lastData[0]["ket"],
                        $lastData[0]["ide"],
                        $lastData[0]["id_user"],
                        $lastData[0]["tgl_input"],
                        $lastData[0]["tgl_output"],
                        $lastData[0]["editor"],
                        $lastData[0]["author"],
                        $lastData[0]["status_data"]
                    );

                    $maindataModel->updateMaindata(
                    $bodyRequest["id"], 
                    $bodyRequest["min_frekuensi"],
                    $bodyRequest["max_frekuensi"], 
                    $bodyRequest["bandwidth"], 
                    $bodyRequest["band"], 
                    $bodyRequest["jenis_band"], 
                    $bodyRequest["wp"], 
                    $bodyRequest["tech_world"], 
                    $bodyRequest["tech_indonesia"], 
                    $bodyRequest["license"],
                    $bodyRequest["assignment"],
                    $bodyRequest["document"],
                    $bodyRequest["isu_teknis"],
                    $bodyRequest["isu_lain"],
                    $bodyRequest["ref"],
                    $bodyRequest["ket"],
                    $bodyRequest["ide"]);

                    $result = $maindataModel->getAllData();
                    foreach ($result as $value) {
                        $resultOverlap = $maindataModel->getOverlap($value["min_frekuensi"], $value["max_frekuensi"]);
                        $maindataModel->updateOverlap($resultOverlap, $value["id_maindata"]);   
                    } 
                    $result = (object) [
                    'min_frekuensi'=>$bodyRequest["min_frekuensi"], 
                    'max_frekuensi'=>$bodyRequest["max_frekuensi"], 
                    'bandwidth'=>$bodyRequest["bandwidth"], 
                    'band'=>$bodyRequest["band"], 
                    'jenis_band'=>$bodyRequest["jenis_band"], 
                    'wp'=>$bodyRequest["wp"], 
                    'tech_world'=>$bodyRequest["tech_world"], 
                    'tech_indonesia'=>$bodyRequest["tech_indonesia"], 
                    'license'=>$bodyRequest["license"],
                    'assignment'=>$bodyRequest["assignment"],
                    'document'=>$bodyRequest["document"],
                    'isu_teknis'=>$bodyRequest["isu_teknis"],
                    'isu_lain'=>$bodyRequest["isu_lain"],
                    'ref'=>$bodyRequest["ref"],
                    'ket'=>$bodyRequest["ket"],
                    'ide'=>$bodyRequest["ide"]];
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
                'message' => 'Success update data!',
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
                $maindataModel = new MaindataModel();
 
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ( 
                empty($bodyRequest["id"])) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    // Get Overlap and update
                    $lastData = $maindataModel->getDataByID($bodyRequest["id"]);
                    $maindataModel->insertHistory(
                        $lastData[0]["id_maindata"],
                        $lastData[0]["min_frekuensi"],
                        $lastData[0]["max_frekuensi"],
                        $lastData[0]["bandwidth"],
                        $lastData[0]["band"],
                        $lastData[0]["jenis_band"],
                        $lastData[0]["wp"],
                        $lastData[0]["tech_world"],
                        $lastData[0]["tech_indonesia"],
                        $lastData[0]["license"],
                        $lastData[0]["assignment"],
                        $lastData[0]["document"],
                        $lastData[0]["isu_teknis"],
                        $lastData[0]["isu_lain"],
                        $lastData[0]["ref"],
                        $lastData[0]["ket"],
                        $lastData[0]["ide"],
                        $lastData[0]["id_user"],
                        $lastData[0]["tgl_input"],
                        $lastData[0]["tgl_output"],
                        $lastData[0]["editor"],
                        $lastData[0]["author"],
                        $lastData[0]["status_data"]
                    );

                    $maindataModel->deleteMaindata($bodyRequest["id"]);

                    $result = $maindataModel->getAllData();
                    foreach ($result as $value) {
                        $resultOverlap = $maindataModel->getOverlap($value["min_frekuensi"], $value["max_frekuensi"]);
                        $maindataModel->updateOverlap($resultOverlap, $value["id_maindata"]);   
                    } 
                    $responseData = null;
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
                'message' => 'Success update data!',
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