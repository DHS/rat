<?php

// Helper function to convert raw input to html
function process_content($content) {

	// Replace line breaks with <br />
	$content = str_replace("\n", "<br />", $content);

	// Replace URLs with links, regex from php.net/ereg_replace
	$content = preg_replace('@(https?://([-\w\.]+)+(:\d+)?((/[\w/_\.%\-+~]*)?(\?\S+)?)?)@', "<a href=\"$0\">$0</a>", $content);

	// Coming soon here: parse for @mentions in the format @{12345}

	return $content;

}
