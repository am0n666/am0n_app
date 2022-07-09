<?php

class _MyDump
{
	protected $themes = [
			'dark' => [
				'default' => 'border-radius:5px; padding: 10px;background-color:#18171B; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
				'arrow' => 'font-weight:bold; color:#1299DA',
				'type' => 'font-weight:bold; color:#ffffff',
				'boolean' => 'font-weight:bold; color:#B729D9',
				'double' => 'font-weight:bold; color:#607d8b',
				'int' => 'font-weight:bold; color:#1299DA',
				'null' => 'font-weight:bold; color:#ff0026',
				'str' => 'font-weight:bold; color:#fff700',
				'key' => 'color:#56DB3A',
				'bracket' => 'color:#FF8400;',
			],
			'light' => [
				'default' => 'border-radius:5px; padding: 10px;background-color:white; color:#CC7832; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
				'arrow' => 'font-weight:bold; color:#1299DA',
				'type' => 'font-weight:bold; color:#000000',
				'boolean' => 'font-weight:bold; color:#B729D9',
				'double' => 'font-weight:bold; color:#262626',
				'int' => 'font-weight:bold; color:#1299DA',
				'null' => 'font-weight:bold; color:#1299DA',
				'str' => 'font-weight:bold; color:#b8bb23;',
				'key' => 'color:#789339',
				'bracket' => 'color:#CC7832;',
			],
		];

	public function mydump($var, $theme)
	{
	
		$dumptheme = $this->themes[$theme];
	
		$scope = false;
		$prefix = 'unique';
		$suffix = 'value';
	
		if($scope) $vals = $scope;
		else $vals = $GLOBALS;
	
		$old = $var;
		$var = $new = $prefix.rand().$suffix; $vname = FALSE;
		foreach($vals as $key => $val) if($val === $new) $vname = $key;
		$var = $old;
	
		echo "<pre style=\"".$dumptheme['default']."\">";
		echo $this->do_mydump($dumptheme, $var);
		echo "</pre>";
	}

	private function do_mydump($dumptheme, $arr, $addtab = "") {
		$tab = $addtab;
		$string = "";
		$i = 0;
		$type = ucfirst(gettype($arr));	
		
		foreach($arr as $key => $val) {
			if($key !== '__been_here'){
				$type = ucfirst(gettype($val));		
				if($type	== "String"	) { $string .= $tab."".$this->print_span("\"", $dumptheme['bracket'])."" . $this->print_span($key, $dumptheme['key']) . "".$this->print_span("\"", $dumptheme['bracket'])." ".$this->print_span("=>", $dumptheme['arrow'])." \"" . $this->print_span($val, $dumptheme['str']) .  "".$this->print_span("\"", $dumptheme['bracket'])."" . "\n"; }
				elseif($type	== "Integer") { $string .= $tab . "".$this->print_span("\"", $dumptheme['bracket'])."".$this->print_span($key, $dumptheme['key'])."".$this->print_span("\"", $dumptheme['bracket'])."" . " ".$this->print_span("=>", $dumptheme['arrow'])." " . $this->print_span($val, $dumptheme['int']) . "\n"; }
				elseif($type	== "Double"	) { $string .= $tab . "".$this->print_span("\"", $dumptheme['bracket'])."".$this->print_span($key, $dumptheme['key'])."".$this->print_span("\"", $dumptheme['bracket'])."" . " ".$this->print_span("=>", $dumptheme['arrow'])." " . $this->print_span($val, $dumptheme['double']) . "\n"; }
				elseif($type	== "Boolean") { ($val == 1) ? $val_bool = "true": $val_bool = "false"; $string .= $tab . "".$this->print_span("\"", $dumptheme['bracket'])."".$this->print_span($key, $dumptheme['key'])."".$this->print_span("\"", $dumptheme['bracket'])."" . " ".$this->print_span("=>", $dumptheme['arrow'])." " . $this->print_span($val_bool, $dumptheme['boolean']) . "\n"; }
				elseif($type	== "NULL"	) { $string .= $tab . "".$this->print_span("\"", $dumptheme['bracket'])."".$this->print_span($key, $dumptheme['key'])."".$this->print_span("\"", $dumptheme['bracket'])."" . " ".$this->print_span("=>", $dumptheme['arrow'])." " . $this->print_span("NULL", $dumptheme['null']) . "\n"; }
				elseif($type == "Array") {
					$type = ucfirst(gettype($val));
					$string .= $tab."".$this->print_span("\"", $dumptheme['bracket'])."" . $this->print_span($key, $dumptheme['key']) . "".$this->print_span("\"", $dumptheme['bracket'])." ".$this->print_span("=>", $dumptheme['arrow'])." ".$this->print_span($type, $dumptheme['type'])." (\n";
					$string .= $this->do_mydump($dumptheme, $val, "\t".$tab);
					$string .= $tab.")\n";
				}elseif($type == "Object") {
					$type = ucfirst(gettype($val));
					$string .= $tab."".$this->print_span("\"", $dumptheme['bracket'])."" . $this->print_span($key, $dumptheme['key']) . "".$this->print_span("\"", $dumptheme['bracket'])." ".$this->print_span("=>", $dumptheme['arrow'])." ".$this->print_span($type, $dumptheme['type'])." (\n";
					$string .= $this->do_mydump($dumptheme, $val, "\t".$tab);
					$string .= $tab.")\n";
				}
				$i ++;
			}
		}

	
		return $string;
	}

	private function print_span($val, $style) {
		return "<span style=\"".$style."\">" . $val . "</span>";
	}
}

function dump($var, $theme = 'dark') {
	$dump = new _MyDump();
	return $dump->mydump($var, $theme);
}

?>
