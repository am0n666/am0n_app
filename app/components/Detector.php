<?php

class Detector
{
    private static $map = [];
    private static $regexp = [];
	const SKIN_TONE_RANGE = '\x{1F3FB}-\x{1F3FF}';
	const HAIR_RANGE = '\x{1F9B0}-\x{1F9B3}';
	const COMPONENTS_REGEX = '/[' . self::SKIN_TONE_RANGE . self::HAIR_RANGE . ']+/ui';

    public function __construct()
    {
        if (! self::$map) {
            self::$map = $this->loadMap();
        }
        if (! self::$regexp) {
            self::$regexp = $this->loadRegexp();
        }
    }

    public function loadMap(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/map.json'), true);
    }

    public function loadRegexp(): string
    {
        return '/(?:' . json_decode(file_get_contents(__DIR__ . '/regexp.json')) . ')/u';
    }

	public static function containsEmoji(string $string): bool
	{
		$string = preg_replace(self::COMPONENTS_REGEX, '', $string);
		if (preg_match(self::$regexp, $string)) {
			return true;
		}
		return false;
	}

    public function detectEmoji(string $string): array
    {
		$string = fix_emojis($string);
        $previousEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        $data = [];
        if (preg_match_all(self::$regexp, $string, $matches)) {
            foreach ($matches[0] as $emojiDetected) {
                $points = [];
                $emojiDetectedLength = mb_strlen($emojiDetected);
                for ($i = 0; $i < $emojiDetectedLength; $i++) {
                    $points[] = strtoupper(dechex(uniOrd(mb_substr($emojiDetected, $i, 1))));
                }
                $hexString = implode('-', $points);

                $fullName = self::$map[$hexString]['name'] ?? null;
                $PNGImage = self::$map[$hexString]['image'] ?? null;

                $skinTone = null;
                $skinTones = [
                    '1F3FB' => 'skin-tone-2',
                    '1F3FC' => 'skin-tone-3',
                    '1F3FD' => 'skin-tone-4',
                    '1F3FE' => 'skin-tone-5',
                    '1F3FF' => 'skin-tone-6',
                ];
                foreach ($points as $point) {
                    if (array_key_exists($point, $skinTones)) {
                        $skinTone = $skinTones[$point];
                    }
                }

                $data[] = [
                    'emoji' => $emojiDetected,
                    'name' => $fullName,
                    'num_points' => mb_strlen($emojiDetected),
                    'points_hex' => $points,
                    'hex_str' => $hexString,
                    'skin_tone' => $skinTone,
                    'image' => $PNGImage,
                ];
            }
        }

        if ($previousEncoding) {
            mb_internal_encoding($previousEncoding);
        }

        return $data;
    }

	public static function onlyEmoji(string $string, bool $ignoreWhitespace = true): bool
	{
		$string = fix_emojis($string);
		$string = self::removeEmoji($string);
		if ($ignoreWhitespace) {
			$string = preg_replace('/\s+/', '', $string);
		}
		return strlen($string) === 0;
	}

	public static function removeEmoji(string $string): string
	{
		$string = fix_emojis($string);
		return preg_replace([self::COMPONENTS_REGEX, self::$regexp], ['', ''], $string);
	}

}

function encode_points($points){
	$bits = array();
	if (is_array($points)){
		foreach ($points as $p){
			$bits[] = sprintf('%04X', $p);
		}
	}
	if (!count($bits)) return null;
	return implode('-', $bits);
}

function uniOrd(string $data): ?int
   {
       $ord0 = ord($data[0]);
       if ($ord0 >= 0 && $ord0 <= 127) {
           return $ord0;
       }
       $ord1 = ord($data[1]);
       if ($ord0 >= 192 && $ord0 <= 223) {
           return ($ord0 - 192) * 64 + ($ord1 - 128);
       }
       $ord2 = ord($data[2]);
       if ($ord0 >= 224 && $ord0 <= 239) {
           return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
       }
       $ord3 = ord($data[3]);
       if ($ord0 >= 240 && $ord0 <= 247) {
           return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
       }

       return null;
   }
function utf8_bytes_to_uni_hex($utf8_bytes){

	$bytes = array();

	foreach (str_split($utf8_bytes) as $ch){
		$bytes[] = ord($ch);
	}

	$codepoint = 0;
	if (count($bytes) == 1) $codepoint = $bytes[0];
	if (count($bytes) == 2) $codepoint = (($bytes[0] & 0x1F) << 6) | ($bytes[1] & 0x3F);
	if (count($bytes) == 3) $codepoint = (($bytes[0] & 0x0F) << 12) | (($bytes[1] & 0x3F) << 6) | ($bytes[2] & 0x3F);
	if (count($bytes) == 4) $codepoint = (($bytes[0] & 0x07) << 18) | (($bytes[1] & 0x3F) << 12) | (($bytes[2] & 0x3F) << 6) | ($bytes[3] & 0x3F);
	if (count($bytes) == 5) $codepoint = (($bytes[0] & 0x03) << 24) | (($bytes[1] & 0x3F) << 18) | (($bytes[2] & 0x3F) << 12) | (($bytes[3] & 0x3F) << 6) | ($bytes[4] & 0x3F);
	if (count($bytes) == 6) $codepoint = (($bytes[0] & 0x01) << 30) | (($bytes[1] & 0x3F) << 24) | (($bytes[2] & 0x3F) << 18) | (($bytes[3] & 0x3F) << 12) | (($bytes[4] & 0x3F) << 6) | ($bytes[5] & 0x3F);

	$str = sprintf('%x', $codepoint);
	return str_pad($str, 4, '0', STR_PAD_LEFT);
}

function utf8_bytes_to_hex($str){
	mb_internal_encoding('UTF-8');
	$out = array();
	while (strlen($str)){
		$out[] = utf8_bytes_to_uni_hex(mb_substr($str, 0, 1));
		$str = mb_substr($str, 1);
	}
	return implode('-', $out);
}

function parse_unicode_specfile($filename, $callback){

	$lines = file($filename);
	foreach ($lines as $line){
		$p = strpos($line , '#');
		$comment = '';
		if ($p !== false){
			$comment = trim(substr($line, $p+1));
			$line = substr($line, 0, $p);
		}
		$line = trim($line);
		if (!strlen($line)) continue;

		$bits = explode(';', $line);
		$fields = array();
		foreach ($bits as $bit){
			$fields[] = trim($bit);
		}

		call_user_func($callback, $fields, $comment);
	}
}

function cp_to_utf8_bytes($v){

	if ($v < 128){
		return chr($v);
	}

	if ($v < 2048){
		return chr(($v >> 6) + 192) . chr(($v & 63) + 128);
	}

	if ($v < 65536){
		return chr(($v >> 12) + 224) . chr((($v >> 6) & 63) + 128) . chr(($v & 63) + 128);
	}

	if ($v < 2097152){
		return chr(($v >> 18) + 240) . chr((($v >> 12) & 63) + 128) . chr((($v >> 6) & 63) + 128) . chr(($v & 63) + 128);
	}

	die("can't create codepoints for $v");
}

function unicode_bytes($uni){

	$out = '';

	$cps = explode('-', $uni);
	foreach ($cps as $cp){
		$out .= emoji_utf8_bytes(hexdec($cp));
	}

	return $out;
}

function emoji_utf8_bytes($cp){

	if ($cp > 0x10000){
		# 4 bytes
		return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
			chr(0x80 | (($cp & 0x3F000) >> 12)).
			chr(0x80 | (($cp & 0xFC0) >> 6)).
			chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x800){
		# 3 bytes
		return	chr(0xE0 | (($cp & 0xF000) >> 12)).
			chr(0x80 | (($cp & 0xFC0) >> 6)).
			chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x80){
		# 2 bytes
		return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
			chr(0x80 | ($cp & 0x3F));
	}else{
		# 1 byte
		return chr($cp);
	}
}

function unicodeOrd($hexChar)
{
	$ord0 = ord($hexChar[0]);
	if ($ord0 >= 0 && $ord0 <= 127) return $ord0;

	$ord1 = ord($hexChar[1]);
	if ($ord0 >= 192 && $ord0 <= 223) return ($ord0 - 192) * 64 + ($ord1 - 128);

	$ord2 = ord($hexChar[2]);
	if ($ord0 >= 224 && $ord0 <= 239) return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);

	$ord3 = ord($hexChar[3]);
	if ($ord0 >= 240 && $ord0 <= 247) return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);

	return false;
}

function fix_emojis($text) {
	return unicode_bytes(preg_replace(array('/(-2640)(?!-FE0F)/', '/(-2642)(?!-FE0F)/'), array('-2640-FE0F', '-2642-FE0F'), utf8_bytes_to_hex($text)));
}