<?php
class UserController extends BaseController
{
    /**
     * "/user/list" Endpoint - Get list of users
     */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();
 
                $intLimit = 20;
                if (isset($queries['limit']) && $queries['limit']) {
                    $intLimit = strtoupper($queries['limit']);
                }

                $offset = 0;
                if (isset($arrQueryStringParams['offset']) && $arrQueryStringParams['offset']) {
                    $offset = $arrQueryStringParams['offset'];
                }

                $search = "";
                if (isset($arrQueryStringParams['search']) && $arrQueryStringParams['search']) {
                    $search = $arrQueryStringParams['search'];
                }
 
                $arrUsers = $userModel->getUsers($intLimit, $offset, $search);
                
                foreach ($arrUsers as $key => $value) {
                    unset($arrUsers[$key]['password']);
                };
                $responseData = $arrUsers;
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
                'message' => 'Success get user!',
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

    public function loginAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $userModel = new UserModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                
                if ($bodyRequest["username"] == "" || 
                $bodyRequest["username"] == null || 
                $bodyRequest["password"] == "" || $bodyRequest["username"] == null) {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $arrUsers = $userModel->getUsersByUsernameAndPassword($bodyRequest["username"], $bodyRequest["password"]);

                    if (count($arrUsers) != 0) {
                        $jwt = $this->makeJWT($arrUsers[0]['id_user'], $arrUsers[0]['username'], $arrUsers[0]['level'], $arrUsers[0]['email'], $arrUsers[0]['instansi']);
                        $result = (object) [
                            'id' => $arrUsers[0]['id_user'],
                            'username' => $arrUsers[0]['username'],
                            'email' => $arrUsers[0]['email'],
                            'token' => $jwt,
                            'message' => "Success login user!"
                        ];
                        $responseData = $result;
                    } else {
                        $strErrorDesc = 'Username / Password is wrong!';
                        $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
                'message' => 'Success login user!',
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

    public function registrationAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $userModel = new UserModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                $instansi = "";
                if (empty($bodyRequest["name"]) ||
                 empty($bodyRequest["username"]) || 
                 empty($bodyRequest["email"]) ||  
                 empty($bodyRequest["password"]) || 
                 empty($bodyRequest["level"])) 
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    if (!empty($bodyRequest["instansi"])) {
                        $instansi = $bodyRequest["instansi"];
                    } 
                    $userModel->registrationUsers($bodyRequest["name"], $bodyRequest["username"], $bodyRequest["email"], $instansi, $bodyRequest["password"], $bodyRequest["level"]);
                    $arrUsers = $userModel->getUsersByUsernameAndPassword($bodyRequest["username"], $bodyRequest["password"]);
                    $jwt = $this->makeJWT($arrUsers[0]['id_user'], $arrUsers[0]['username'], $arrUsers[0]['level'], $arrUsers[0]['email'], $arrUsers[0]['instansi']);
                    $result = (object) [
                        'username' => $bodyRequest['username'],
                        'email' => $bodyRequest['email'],
                        'message' => "Success registration user!",
                        'token' => $jwt
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
                'message' => 'Success login user!',
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

    public function forgotAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $userModel = new UserModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                if (empty($bodyRequest["email"]))
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $len = count($userModel->getUserByEmail($bodyRequest["email"]));
                    if ($len == 0)
                    {
                        $strErrorDesc = 'Not Found Account!';
                        $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                    }else{
                        $randomPass = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
                        $userModel->changePassword($bodyRequest["email"], $randomPass);
                        $result = (object) [
                            'email' => $bodyRequest['email']
                        ];
                        $this->sendEmail($bodyRequest["email"], "Forgot Password", "Hi, your password is ".$randomPass);
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
                'message' => "Success create new password, check email please!",
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

    public function changedataAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        if (strtoupper($requestMethod) == 'PUT') {
            try {
                $userModel = new UserModel();
                
                $bodyRequest = json_decode(file_get_contents('php://input'), true); 
                if (empty($bodyRequest["name"]) ||
                    empty($bodyRequest["username"]) ||
                    empty($bodyRequest["email"]) ||
                    empty($bodyRequest["instansi"]) ||
                    empty($bodyRequest["level"]) ||
                    empty($bodyRequest["id"]))
                {
                    $strErrorDesc = 'Body request incomplete!';
                    $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
                } else {
                    $arrUsers = $userModel->getUserByID($bodyRequest["id"]);
                    if (count($arrUsers) == 0){
                        $strErrorDesc = 'Not Found Account!';
                    $strErrorHeader = 'HTTP/1.1 404 Not Found';
                    } else {
                    $userModel->changeDataUser($bodyRequest["name"], $bodyRequest["username"], $bodyRequest["email"], $bodyRequest["instansi"], $bodyRequest["level"], $bodyRequest["id"]);
                    $arrUsers = $userModel->getUserByID($bodyRequest["id"]);
                    $jwt = $this->makeJWT($arrUsers[0]['id'], $arrUsers[0]['username'], $arrUsers[0]['level'], $arrUsers[0]['email'], $arrUsers[0]['instansi']);
                    $result = (object) [
                        'email' => $bodyRequest['email'],
                        'token' => $jwt
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
                'message' => "Success change data account!",
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