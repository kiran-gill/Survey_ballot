<?php
$lines = file('../css/font-awesome.css');

$linecount = 0;
foreach ($lines as $line)
{
	$line = trim($line);

	if (substr_count($line, 'content: "')) {
		if (isset($lines[$linecount-1]))
		$icon = $lines[$linecount-1];
		$icon = str_replace(":before {", "", $icon);
		$icon = str_replace(".fa-", "", $icon);
		$icon = trim ($icon);
		echo "'" . $icon . "'," . "\n";
	}
	$linecount++;
}

?>