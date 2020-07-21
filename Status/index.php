<?php

define("ROOTDIR", "../");
define("REAL_ROOTDIR", "../");

require_once REAL_ROOTDIR."src/initializer.php";
use \Catalyst\Page\Values;
use \Catalyst\User\User;

define("PAGE_KEYWORD", Values::STATUS[0]);
define("PAGE_TITLE", Values::createTitle(Values::STATUS[1], []));
define("NO_HEADER", 1);

if (User::isLoggedIn()) {
	define("PAGE_COLOR", $_SESSION["user"]->getColor());
} else {
	define("PAGE_COLOR", Values::DEFAULT_COLOR);
}

require_once Values::HEAD_INC;
?></div>
	<?php include('status.html'); ?>
		</div>
		<div class="row center align-center no-margin">
			<h4>Have any questions or need more information?</h4>
			<p class="flow-text">Please send us an e-mail at <a href="mailto:catalyst@catalystapp.co">catalyst@catalystapp.co</a> and we will try to respond as soon as possible!</p>
		</div>
<div><?php
require_once Values::FOOTER_INC;
