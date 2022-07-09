<?php

function add_css($css_files, $add_time = true, $css_dir = 'css/')
{
	$_result = "";
	$add_time ? $time_param = '?time()': $time_param = '';
	$type = ucfirst(gettype($css_files));
	if ($type === 'Array')
	{
		foreach ($css_files as $css_file)
		{
			if (is_file($css_dir . "/" . $css_file))
			{
				$_result .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $css_dir . "/" . $css_file . $time_param . "\">\n";
			}
		}
	}

	if ($type === 'String')
	{
		if (is_file($css_dir . "/" . $css_files))
		{
			$_result = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $css_dir . "/" . $css_file . $time_param . "\">\n";
		}
	}
    echo $_result;
}

?>
