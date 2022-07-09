<?php

class AppConfig
{
	public $config_file_path;

	public function __construct(string $config_file_path)
	{
		$this->config_file_path = $config_file_path;
		return;
	}

	private function configAsObject($mycfg)
	{
		$result_obj = [];
		foreach($mycfg as $key => $val) {
			$type = ucfirst(gettype($val));
			if($type == "Array") {
				$result_obj[$key] = (object) $this->configAsObject($val);
			}elseif($type == "Object") {
				$result_obj[$key] = (object) $this->configAsObject($val);
			} else {
				$result_obj[$key] = $val;
			}
		}
		$result = (object) $result_obj;
		return (object) $result;
	}

	public function load() {
		$result = null;
		if (is_file($this->config_file_path))
		{
			$result = $this->configAsObject(json_decode(file_get_contents($this->config_file_path), false));
		}
		return $result;
	}
	
	public function save($config) {
		$result = false;
		$_config = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		if (file_put_contents($this->config_file_path, $_config)) $result = true;
		return $result;
	}

}