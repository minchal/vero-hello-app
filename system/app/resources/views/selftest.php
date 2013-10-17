<!DOCTYPE
 html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
<head>
	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	/* <![CDATA[ */
	body {
		background: #eee; padding: 0; margin: 0;
		font: 10pt normal Arial, Verdana, sans-serif; color: #333;
	}
	h1 {font-size: 18pt; padding: 0; margin: 0; color: #666;}
	p {margin: 0; padding: 0 0 1em 0;}
	a {text-decoration: none;} a:hover {text-decoration: underline;}
	hr {border: none; border-bottom: solid 1px #777;}
	#content {
		width: 750px; margin: 50px auto 0 auto; padding: 15px;
		background: #fff; border: solid 1px #777;
	}
	#foot {
		width: 750px; margin: 0 auto 20px; padding: 15px;  
		font-size: 9pt; background: #fff; border: solid 1px #777; border-top: none;
	}
	table {width:100%;}
	table td {border:solid 1px #aaa; padding: 2px 5px; width: 50%;}
	.ok {color: #178e17;}
	.bad {color: #f43636;}
	/* ]]> */
	</style>
</head>
<body>
<div id="content">
	<h1><?=$title?></h1>
	<hr />
	<?php
        echo '<table>';
		foreach($info as $i) {
			if (is_array($i)) {
				echo '<tr><td>'.$i[0].'</td>';
				if (isset($i[2])) {
					if ($i[2]) {
						echo '<td class="ok">'.($i[1] ? $i[1] : 'YES').'</td></tr>';
					} else {
						echo '<td class="bad">'.($i[1] ? $i[1] : 'NO').'</td></tr>';
					}
				} else {
					echo '<td>'.$i[1].'</td></tr>';
				}
			} else {
				echo '</table><h3>'.$i.'</h3><table>';
			}
		}
		echo '</table>';
    ?>
</div>
<div id="foot">
	<?=$signature?>
    <?=($debug? '<pre>'.$debug.'</pre>' : '')?>
</div>
</body>
</html>
