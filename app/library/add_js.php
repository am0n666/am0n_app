<?php

function add_js($js_files, $add_time = true, $js_dir = 'js/')
{
	$_result = "";
	$add_time ? $time_param = '?time()': $time_param = '';
	$type = ucfirst(gettype($js_files));
	if ($type === 'Array')
	{
		foreach ($js_files as $js_file)
		{
			if (is_file($js_dir . "/" . $js_file))
			{
				$_result .= "<link rel=\"stylesheet\" type=\"text/js\" href=\"" . $js_dir . "/" . $js_file . $time_param . "\">\n";
			}
		}
	}

	if ($type === 'String')
	{
		if (is_file($js_dir . "/" . $js_files))
		{
			$_result = "<script src=\"" . $js_dir . "/" . $js_files . $time_param . "\"></script>\n";
		}
	}
    echo $_result;
}