<?php

define("ROOTDIR", "../../");
define("REAL_ROOTDIR", "../../");

require_once REAL_ROOTDIR."includes/init.php";
use \Redacted\Database\Character\NewCharacter;
use \Redacted\Form\FormHTML;
use \Redacted\Page\UniversalFunctions;
use \Redacted\Page\Values;
use \Redacted\User\User;

define("PAGE_KEYWORD", Values::NEW_CHARACTER[0]);
define("PAGE_TITLE", Values::createTitle(Values::NEW_CHARACTER[1], []));

if (User::isLoggedIn()) {
	define("PAGE_COLOR", User::getCurrentUser()->getColor());
} else {
	define("PAGE_COLOR", Values::DEFAULT_COLOR);
}

require_once Values::HEAD_INC;

echo UniversalFunctions::createHeading("New Character");

if (FormHTML::testAjaxSubmissionFailed()) {
	echo FormHTML::getAjaxSubmissionHtml();
} elseif (User::isLoggedOut()) {
	echo User::getNotLoggedInHTML();
} else {
	echo FormHTML::generateForm(NewCharacter::getFormStructure());
}

require_once Values::FOOTER_INC;
