<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function get_the_ip() {
	$text = "\n\n\nIP-адрес: ";
	
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		return $text.$_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
		return $text.$_SERVER["HTTP_CLIENT_IP"];
	} else {
		return $text.$_SERVER["REMOTE_ADDR"];
	}
}


$mail = new PHPMailer(true);
$data = array();
$errors = [];
$fields = [];
$email_message = '';
$required_fields = array("dia_name", "dia_email", "dia_phone");
$responses = array(
	"error_common" => "<span>Произошла ошибка. Пожалуйста, повторите попытку позже.</span>",
	"error_empty" => "<span>Пожалуйста, заполните все необходимые поля.</span>",
	"error_email" => "<span>Пожалуйста, введите правильный email адрес.</span>",
	"error_phone" => "<span>Пожалуйста, введите правильный номер телефона.</span>",
	// "error_company" => "<span>Пожалуйста, введите правильное название компании.</span>",
	"error_name" => "<span>Пожалуйста, введите правильное имя.</span>",
	// "error_msg" => "<span>Текст сообщения, слишком длинный.</span>",
	"success" => "<span>Спасибо, ваше сообщение отправлено.</span>"
);


try {

	foreach ($_POST as $field => $value) {
		$data[$field] = trim(strip_tags(stripslashes($value)));
	}

	foreach ($required_fields as $required_field) {
		$value = trim($data[$required_field]);
		if(empty($value)) {
			$errors['error_empty'] = $responses['error_empty'];
			array_push($fields, $required_field);
		}
	}

	// Проверка скрытого пустого антиспам поля
	if (! empty($data["dia_country"])) {
		$errors['error_empty'] = $responses['error_empty'];
	}

	// Проверка поля "Имя"
	if (! empty($data["dia_name"])) {
		if (preg_match('/^[A-Za-zА-Яа-я ]{2,30}$/iu', $data['dia_name'])) {
			$email_message .= "\n\nИмя: {$data['dia_name']}";
		} else {
			$errors['error_name'] = $responses['error_name'];
			array_push($fields, 'dia_name');
		}
	}

	// Проверка поля "Компания"
	/* if (! empty($data["dia_company"])) {
		if (preg_match('/^[\w\s]{1,50}$/iu', $data['dia_company'])) {
			$email_message .= "\n\nКомпания: {$data['dia_company']}";
		} else {
			$errors['error_company'] = $responses['error_company'];
		}
	} */

	if (! empty($data["dia_phone"])) {
		// if (preg_match('/^\+?[78][-\(]?\d{3}\)?-?\d{3}-?\d{2}-?\d{2}$/', $data['form_phone'])) {
		if (preg_match('/^\+?[\d() -]{6,30}$/', $data['dia_phone'])) {
			$email_message .= "\n\nТелефон: {$data['dia_phone']}";
		} else {
			$errors['error_phone'] = $responses['error_phone'];
			array_push($fields, 'dia_phone');
		}
	}

	// Проверка поля "Email"
	if (! empty($data["dia_email"])) {
		if (preg_match('/^\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b$/', $data['dia_email'])) {
			$email_message .= "\n\nEmail: {$data['dia_email']}";
		} else {
			$errors['error_email'] = $responses['error_email'];
			array_push($fields, 'dia_email');
		}
	}

	// Проверка поля "Сообщение"
	/* if (! empty($data["dia_msg"])) {
		if (preg_match('/^.{1,3000}$/iu', $data['dia_msg'])) {
			$email_message .= "\n\nСообщение: {$data['dia_msg']}";
		} else {
			$errors['error_msg'] = $responses['error_msg'];
		}
	} */
	

	// Server settings
	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->isSMTP();
	$mail->Host       = 'smtp.mail.ru';
	$mail->SMTPAuth   = true;
	$mail->Username   = 'shurinskiy@mail.ru'; 
	$mail->Password   = 'ptPgtdcqnpnKkQrcNd2s';
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$mail->Port       = 465;

	//Recipients
	$mail->setFrom('shurinskiy@mail.ru');
	$mail->addAddress('shurinskiy@mail.ru');

	//Content
	$mail->CharSet = 'UTF-8';
	// $mail->isHTML(true);
	$mail->Subject = "Заявка на рекламную компанию, на сайте {$_SERVER['HTTP_REFERER']}";
	$mail->Body    = $email_message.get_the_ip();

	if(empty($errors)) {
		$mail->send();
		echo json_encode(array(
			'status'=>'success', 
			'text'=>"<span>Спасибо, {$_POST['dia_name']}! Ваше сообщение успешно отправлено.</span>"
		));
	}
	else {
		echo json_encode(array(
			'status'=> 'error', 
			'errors'=> $fields,
			'text'=> implode($errors)
		));
	}

} catch (Exception $e) {
	echo json_encode(array(
		'status'=>'error', 
		'text'=>$responses['error_common'],
		// 'text'=>"Письмо не отправлено. Ошибка: {$mail->ErrorInfo}",
	));
}

?>