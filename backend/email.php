<?php

include_once ('PDO_Config.php');
include('constants.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $email = $obj['email'];
    $operation = strtolower($obj['operation']);

    //First, check this email id DB
    $result = array();
    if($email){
        $user_email = $pdo->prepare('SELECT * FROM users_email WHERE email= :email');
        $user_email->execute(array('email'=> $email));
        $email_exist = $user_email->fetchAll(PDO::FETCH_ASSOC);
        if($email_exist && count($email_exist)>0){
            if($operation == 'addemail'){
        	    $result['error'] = EMAIL_EXIST_MSG;
                $result['email'] = $email;
            }
            if($operation == 'unsubscr'){
                $sql = " DELETE FROM users_email WHERE email=:email ";
                $delete = $pdo->prepare($sql);
                $delete->bindParam(':email',$email);
                if($delete->execute()){
                    $result['unsubscr'] = true;
                }else{
                    $result['unsubscr'] = false;
                }
                $result['email'] = $email;

            }
        }else{
			$insert_email = $pdo->exec("INSERT INTO Users_email (Email) VALUES('$email')");
			header("HTTP/1.1 200 OK");
			$result["msg"] = EMAIL_SUBSCRIPTION;
			$result["email"] = $email;
    	}
	}
	echo json_encode($result);exit;
}

?>