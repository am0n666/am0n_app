<?php

$include_dirs = [
	BASE_PATH . '/app/components',
	BASE_PATH . '/app/library'
];

function loadAll($dirs) {
	$type = ucfirst(gettype($dirs));
	if ($type === 'Array')
	{
		foreach ($dirs as $directory)
		{
			if (is_dir($directory))
			{
				foreach(glob($directory . '*.php') as $filename){
					require_once $filename;
				}
				foreach(glob($directory . '**/*.php') as $filename){
					require_once $filename;
				}
			}
		}
	}

	if ($type === 'String')
	{
		if (is_dir($directory))
		{
			foreach(glob($directory . '*.php') as $filename){
				require_once $filename;
			}
			foreach(glob($directory . '**/*.php') as $filename){
				require_once $filename;
			}
		}
	}
}

loadAll($include_dirs);