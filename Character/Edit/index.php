<?php

define("ROOTDIR", "../../".((isset($_GET["levels"]) && $_GET["levels"] == "/") ? "../" : ""));
define("REAL_ROOTDIR", "../../");

require_once REAL_ROOTDIR."includes/Controller.php";
use \Catalyst\Character\Character;
use \Catalyst\HTTPCode;
use \Catalyst\Page\{UniversalFunctions, Values};
use \Catalyst\User\User;

$id = $character = $pendingCharacter = null;
if (User::isLoggedIn()) {
	if (isset($_GET["q"])) {
		$id = Character::getIdFromToken($_GET["q"]);
		if ($id !== -1) {
			$pendingCharacter = new Character($id);
			if ($pendingCharacter->getOwnerId() == $_SESSION["user"]->getId()) {
				$character = $pendingCharacter;
			} else {
				HTTPCode::set(403);
			}
		} else {
			HTTPCode::set(404);
		}
	} else {
		HTTPCode::set(404);
	}
} else {
	HTTPCode::set(401);
}

define("PAGE_KEYWORD", Values::VIEW_CHARACTER[0]);
define("PAGE_TITLE", Values::createTitle(Values::VIEW_CHARACTER[1], ["name" => (isset($character) ? $character->getName() : "Invalid Character")]));

if (!is_null($character)) {
	define("PAGE_COLOR", $character->getColor());
} elseif (User::isLoggedIn()) {
	define("PAGE_COLOR", $_SESSION["user"]->getColor());
} else {
	define("PAGE_COLOR", Values::DEFAULT_COLOR);
}

require_once Values::HEAD_INC;

echo UniversalFunctions::createHeading("Character");

if (!User::isLoggedIn()):
	echo User::getNotLoggedInHtml();
elseif (is_null($pendingCharacter)): ?>
			<div class="section">
				<p class="flow-text">This character does not exist.</p>
			</div>
<?php elseif (is_null($character)): ?>
			<div class="section">
				<p class="flow-text">You aren't allowed to do that.</p>
			</div>
<?php
else:
	echo FormRepository::getEditCharacterForm($character)->getHtml();
endif;

require_once Values::FOOTER_INC;
