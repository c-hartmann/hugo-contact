<?php

/*
 * send-mail.config.php
 *
 * Copyright Christian Hartmann 2023
 * hartmann.christian@gmail.com
 *
 */

$sm_config = [

	// to, cc and bcc allow multiple recipients separated by _no other_ character than a semicollon
	'subj'     => 'Kontaktanfrage',
	'to'       => 'hartmann.christian@gmail.com',
	'cc'       => 'chhtm@gmx.de;chhtm@gmx.net',
	'bcc'      => 'hartmann.christian@gmail.com',
	'from'     => 'hartmann.christian@gmail.com',
	'fromname' => 'Kontaktformular',

	// map internal keys to form element name attributes
	'map' => [
		'eml' => 'contact_eml',
		'tel' => 'contact_tel',
		'msg' => 'contact_msg',
		'cnf' => 'contact_cnf',
		'cap' => 'contact_cap'
	],

	'with' => 'smtp', // PHPs built in 'mail' function or 'smtp' (via https://github.com/PHPMailer/PHPMailer)

	'smtp' => [
		'host' => 'mailhost.out',
		'port' =>  465, // 25, 465
		'user' => 'noreply@foobar.de', // noreply@elkethiel-berlin.de, elke.thiel@gmx.net
		'pass' => 'SUOPER-SECRET-PASS'
	],

	'redirect' => './danke.html'

];

return 'success';
