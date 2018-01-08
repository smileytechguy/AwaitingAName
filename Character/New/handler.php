<?php

define("ROOTDIR", "../../");
define("REAL_ROOTDIR", "../../");

require_once REAL_ROOTDIR."includes/init.php";
use \Redacted\Database\Character\NewCharacter;
use \Redacted\Form\FormPHP;
use \Redacted\Response;
use \Redacted\User\User;

if (User::isLoggedOut()) {
	\Redacted\Response::send401(NewCharacter::ERROR_UNKNOWN, NewCharacter::PHRASES[NewCharacter::ERROR_UNKNOWN]);
}

if (empty($_POST)) {
	\Redacted\Response::send401(NewCharacter::PICTURE_INVALID, NewCharacter::PHRASES[NewCharacter::PICTURE_INVALID]);
}

FormPHP::checkForm(NewCharacter::getFormStructure());

$token = "";

$result = NewCharacter::add(
	$_POST["name"],
	$_POST["desc"],
	isset($_FILES["imgs"]) ? $_FILES["imgs"] : null,
	$_POST["color"],
	$_POST["public"] === "true",
	$token
);

if ($result == NewCharacter::ERROR_UNKNOWN) {
	Response::send500(NewCharacter::PHRASES[NewCharacter::ERROR_UNKNOWN].NewCharacter::$lastErrId, NewCharacter::ERROR_UNKNOWN);
}

Response::send201($token);
