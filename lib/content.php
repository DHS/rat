<?php

// Helper function to convert raw input to html
function process_content($content) {

	// Replace line breaks with <br />
	$content = str_replace("\n", "<br />", $content);

	// Replace URLs with links, regex from php.net/ereg_replace
	$content = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $content);

	// Coming soon here: parse for @mentions in the format @{12345}

	return $content;

}

?>