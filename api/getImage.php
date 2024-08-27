<?php
require_once('../../admin/cn.php');
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
	$requestBody=file_get_contents('php://input');
	$params= json_decode($requestBody);
	$params = (array) $params;

	if ($params['id_products']) {
		$idProduct = $params['id_products'];
        
		foreach(glob($idProduct.'/profile/*.{jpg,pdf,png,PNG}', GLOB_BRACE) as $file) {
            //echo $file;
            echo $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/dashboard/user/".$file;
        }

	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

$conn->close();
?>