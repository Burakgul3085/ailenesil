<?php
/**
 * PHP yerlesik sunucusu .htaccess okumaz.
 * Kok ve akd-yonetim kurallarini Apache ile ayni sirada uygular.
 */
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uriPath = rawurldecode($uriPath);
$local = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uriPath);

if ($uriPath !== '/' && is_file($local)) {
	return false;
}

$uzanti = strtolower(pathinfo($uriPath, PATHINFO_EXTENSION));
$statik = ['css','js','map','png','jpg','jpeg','gif','webp','svg','ico','woff','woff2','ttf','eot','mp4','webm','pdf','xml','json','txt'];
if ($uzanti !== '' && in_array($uzanti, $statik, true)) {
	http_response_code(404);
	echo 'Dosya yok';
	return true;
}

function aile_bul($htaccessFile, $path, $scriptRoot, $urlPrefix)
{
	if (!is_file($htaccessFile)) {
		return null;
	}
	$htaccess = file_get_contents($htaccessFile);
	if (!preg_match_all('/^RewriteRule\s+(\S+)\s+(\S+)/m', $htaccess, $rules, PREG_SET_ORDER)) {
		return null;
	}
	foreach ($rules as $rule) {
		$pattern = str_replace('#', '\#', $rule[1]);
		$target = $rule[2];
		if (@preg_match('#' . $pattern . '#', $path, $m) !== 1) {
			continue;
		}
		for ($i = count($m) - 1; $i >= 1; $i--) {
			$target = str_replace('$' . $i, $m[$i], $target);
		}
		$script = strtok($target, '?');
		$query = parse_url($target, PHP_URL_QUERY);
		if (is_string($query) && $query !== '') {
			parse_str($query, $params);
			foreach ($params as $key => $value) {
				$_GET[$key] = $value;
			}
		}
		$scriptFile = $scriptRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $script);
		if (!is_file($scriptFile)) {
			continue;
		}
		return [
			'file' => $scriptFile,
			'dir' => $scriptRoot,
			'name' => $urlPrefix . '/' . str_replace('\\', '/', $script),
		];
	}
	return null;
}

$path = trim($uriPath, '/');
$hedef = null;
$altKlasor = false;

if ($path !== '' && strpos($path, '/') !== false) {
	$first = strstr($path, '/', true);
	$rest = substr($path, strlen($first) + 1);
	$dir = __DIR__ . DIRECTORY_SEPARATOR . $first;
	if (is_dir($dir) && is_file($dir . DIRECTORY_SEPARATOR . '.htaccess')) {
		$altKlasor = true;
		$hedef = aile_bul($dir . DIRECTORY_SEPARATOR . '.htaccess', $rest, $dir, '/' . $first);
	}
}

if ($hedef === null && !$altKlasor) {
	$hedef = aile_bul(__DIR__ . DIRECTORY_SEPARATOR . '.htaccess', $path, __DIR__, '');
}

if ($hedef === null && $altKlasor) {
	http_response_code(404);
	echo 'Sayfa bulunamadi';
	return true;
}

if ($hedef === null) {
	$hedef = [
		'file' => __DIR__ . DIRECTORY_SEPARATOR . 'index.php',
		'dir' => __DIR__,
		'name' => '/index.php',
	];
}

$_SERVER['SCRIPT_NAME'] = $hedef['name'];
$_SERVER['PHP_SELF'] = $hedef['name'];
$oncekiDizin = getcwd();
register_shutdown_function(static function () use ($oncekiDizin) {
	@chdir($oncekiDizin);
});
chdir($hedef['dir']);
require $hedef['file'];
return true;
