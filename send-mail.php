<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$_prod = true;

// The global $_POST variable allows you to access the data sent
// with the POST method by name. To access the data sent with the
// GET method, you can use $_GET
// Early immidiate exit if we are not called via the form
if (!$_prod) {
	echo '<pre>', PHP_EOL;
	var_dump($_POST);
	echo '</pre>', PHP_EOL;
}

if (!isset($_POST['contact_cap']) && !$_prod) {
	$_POST = [
		'contact_eml' => 'hartmann.christian@gmail.com',
		'contact_tel' => '+4987654321',
		'contact_msg' => 'Sample message text'
	];
}

if (!isset($_POST['contact_cap']) && $_prod) {
	echo 'ERROR: 1';
	exit(1);
}

// get list of names, address to send to from config (include allows own check on success)
$read = include("send-mail.config.php");
if ($read != 'success' ) {
	echo 'ERROR: 2';
	exit(2);
}

// get additional code if configured to use it
switch ($sm_config['with']) {
	case 'mail':
		break;
	case 'smtp':
		require '../php/PHPMailer/src/Exception.php';
		require '../php/PHPMailer/src/PHPMailer.php';
		require '../php/PHPMailer/src/SMTP.php';
		break;
	case 'echo':
		break;
}

if (isset($sm_config)) {
	// get some required values from config file
	$subj = $sm_config['subj'];
	$to   = $sm_config['to'];
	$cc   = $sm_config['cc'];
	$bcc  = $sm_config['bcc'];
	$from = $sm_config['from'];
	$fromname = $sm_config['fromname'];
	// prepare to get user form entered values via configured key mapping
	$eml_key = $sm_config['map']['eml'];
	$tel_key = $sm_config['map']['tel'];
	$msg_key = $sm_config['map']['msg'];
	$cnf_key = $sm_config['map']['cnf'];
} else {
	echo 'ERROR: 3';
	exit(3);
}

// above test should catch this, but who knows ...
if(isset($_POST)) {
	$body = "Email:   " . PHP_EOL . htmlspecialchars($_POST[$eml_key]) . PHP_EOL
		  . "Phone:   " . PHP_EOL . htmlspecialchars($_POST[$tel_key]) . PHP_EOL
		  . "Time:    " . PHP_EOL . date('Y-m-d H:i:s') . PHP_EOL
		  . "Message: " . PHP_EOL . htmlspecialchars($_POST[$msg_key], ENT_NOQUOTES, 'UTF-8');
	$body = wordwrap($body,70);
}

switch ($sm_config['with']) {
	case 'mail':
		$success = null;
		$header = "From: " . $from; // 'Cc' to sender?
		$params = "";
		$success = mail($to, $subj, $body, $header, $params);
		break;
	case 'smtp':
		$mail = new PHPMailer(true);
		// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output. see: https://mailtrap.io/blog/phpmailer/ (Debugging), https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-SMTP.html
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = $sm_config['smtp']['host'];             //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = $sm_config['smtp']['user'];             //SMTP username
		$mail->Password   = $sm_config['smtp']['pass'];             //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = $sm_config['smtp']['port'];             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		$mail->Subject    = $subj;
		$mail->setFrom($from, $fromname);
		// allowing multiple to, cc and bcc recipients (separated by not other character than ';')
		if (!empty($to)) {
			$to_list = explode(';', $to);
			foreach ($cc_list as $addr ) {
				$mail->addAddress($addr);
			}
		}
		if (!empty($cc)) {
			$cc_list = explode(';', $cc);
			foreach ($cc_list as $addr ) {
				$mail->addCC($addr);
			}
		}
		if (!empty($bcc)) {
			$bcc_list = explode(';', $bcc);
			foreach ($cc_list as $addr ) {
				$mail->addBCC($addr);
			}
		}
		$mail->Body = $body;
		
		// check on success? 
		$mail->send();
		
		break;
	case 'echo':
		echo '<pre>', PHP_EOL;
		echo 'SUBJECT: ', $subj, PHP_EOL, 'TO: ', $to, PHP_EOL, 'FROM: ', $from, PHP_EOL, 'HEADER: ', $header, PHP_EOL, 'BODY: ', $body, PHP_EOL;
		echo '</pre>', PHP_EOL;
		break;
}

// load thank you page
header("Location: " . $sm_config['redirect']);
