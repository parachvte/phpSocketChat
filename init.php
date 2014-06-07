<?php
//ryannx6@gmail.com
define('ROOT', __DIR__);

spl_autoload_register(function ($name) {
	$lib_folders = ['lib', 'server/controller'];
	$name = strtr($name, '\\', DIRECTORY_SEPARATOR);
	foreach ($lib_folders as $folder) {
		if (file_exists(ROOT . "/{$folder}/{$name}.php")) {
			require ROOT . "/{$folder}/{$name}.php";
			return;
		}
	}
});
