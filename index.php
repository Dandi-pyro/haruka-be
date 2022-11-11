<?php
require __DIR__ . "/inc/bootstrap.php";
 
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
 
if ((isset($uri[3]) && $uri[3] != 'user') || !isset($uri[4])) {
    header("HTTP/1.1 404 Not Found");
}
 
require PROJECT_ROOT_PATH . "/Controller/Api/UserController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/MaindataController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/ApgController.php";
require PROJECT_ROOT_PATH . "/Controller/Api/ApgResponController.php";
 
$strMethodName = $uri[4] . 'Action';
if ($uri[3] == 'user'){
    $objFeedController = new UserController();
    $objFeedController->{$strMethodName}();
} else if ($uri[3] == 'maindata') {
    $objMaindataController = new MaindataController();
    $objMaindataController->{$strMethodName}();
} else if ($uri[3] == 'apg') {
    $objApgController = new ApgController();
    $objApgController->{$strMethodName}();
} else if ($uri[3] == 'apgrespon') {
    $objApgController = new ApgResponController();
    $objApgController->{$strMethodName}();
}
?>