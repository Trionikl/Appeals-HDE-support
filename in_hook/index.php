<?
require_once (__DIR__.'/crest.php');

$result = CRest::call(
		'imbot.message.add',
		{{query[params][PARAMS]}}
	);

echo '<pre>';
	print_r($result);
echo '</pre>';