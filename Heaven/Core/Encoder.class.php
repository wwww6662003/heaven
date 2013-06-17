<?php
/**
 * @package HeavenMVC
 * @version 1.0 Encoder.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
class Core_Encoder extends Core_Base {
	private static $instance;
	protected $key = "";
	protected $use_hash = false;
	protected $use_code = 1;
	protected $separator = ":";
	public function __construct($key, $use_hash = true) {
		$this->key = $key;
		$this->use_hash = $use_hash;
	}
	public function encode($string) {
		$crypt = $this->crypt ( $string, $this->key );
		if ($this->use_hash)
			$crypt .= $this->separator . $this->hash ( $crypt );
		if ($this->use_code == 1)
			return rtrim ( asc_encode ( $crypt ), '=' );
		else
			return rtrim ( base64_encode ( $crypt ), '=' );
	}
	public function decode($string) {
		if ($this->use_code == 1)
			$string = asc_decode ( $string );
		else
			$string = base64_decode ( $string );
		if ($this->use_hash) {
			$string = explode ( $this->separator, $string );
			if (count ( $string ) < 2)
				return null;
			array_pop ( $string );
			$string = implode ( $this->separator, $string );
		}
		return $this->crypt ( $string, $this->key );
	}
	public function set_code($string) {
		if ($string == 1 || $string == 2) {
			$this->use_code = $string;
			return true;
		} else {
			return false;
		}
	}
	public function is_valid_encoded($string) {
		if ($this->use_hash) {
			$temp = $string;
			$code = true;
			$string = base64_decode ( $string );
			$string = explode ( $this->separator, $string );
			if (count ( $string ) < 2)
				$code = false;
			if ($code) {
				$hash = array_pop ( $string );
				$string = implode ( $this->separator, $string );
				if ($hash != $this->hash ( $string ))
					$code = false;
			}
			if ($code) {
				$this->use_code = 2;
				return true;
			} else {
				$codeasc = true;
				$string = $temp;
				$string = asc_decode ( $string );
				$string = explode ( $this->separator, $string );
				if (count ( $string ) < 2)
					return false;
				if ($codeasc) {
					$hash = array_pop ( $string );
					$string = implode ( $this->separator, $string );
					if ($hash != $this->hash ( $string ))
						return false;
				}
				if ($codeasc) {
					$this->use_code = 1;
					return true;
				} else {
					return false;
				}
			}
		}
		return true;
	}
	protected function hash($text) {
		return dechex ( crc32 ( md5 ( $text ) . md5 ( $this->key ) ) );
	}
	
	protected function crypt($text, $key) {
		$key = md5 ( $key );
		$crypt = "";
		$j = 0;
		$k = strlen ( $key );
		for($i = 0; $i < strlen ( $text ); $i ++) {
			$crypt .= chr ( ord ( $text [$i] ) ^ ord ( $key [$j] ) );
			$j ++;
			if ($j >= $k)
				$j = 0;
		}
		return $crypt;
	}
	
	function fix8bit($bin) {
		if (strlen ( $bin ) < 8) {
			for($i = 0; $i < 8 - strlen ( $bin ); $i ++) {
				$cbin .= '0';
			}
			return $cbin . $bin;
		} else {
			return $bin;
		}
	}
	function asc_decode($str) {
		$enbase64_array = array ('b' => '0', 'c' => '1', 'd' => '2', 'e' => '3', 'f' => '4', 'h' => '5', 'i' => '6', 'j' => '7', 'k' => '8', 'l' => '9', 'm' => '10', 'o' => '11', 'p' => '12', 'q' => '13', 'r' => '14', 's' => '15', 't' => '16', 'u' => '17', 'v' => '18', 'w' => '19', 'x' => '20', 'z' => '21', '0' => '22', '1' => '23', '2' => '24', '3' => '25', '4' => '26', '5' => '27', '6' => '28', '7' => '29', '8' => '30', '9' => '31' );
		$length = strlen ( $str );
		$num = 0;
		for($i = 0; $i < $length; $i ++) {
			$index = $str {$i};
			if ($index != 'g') {
				$encode [$i] = decbin ( $enbase64_array [$index] );
			} else {
				$num ++;
			}
		}
		$length = $length - $num;
		for($i = 0; $i < $length - 1; $i ++)
			$encode [$i] = fix8bit ( $encode [$i] );
		$templen = 8 - $num - strlen ( $encode [$length - 1] );
		for($i = 0; $i < $templen; $i ++)
			$encode [$length - 1] = '0' . $encode [$length - 1];
		foreach ( $encode as $d )
			$all .= substr ( $d, 3 );
		for($i = 0; $i < strlen ( $all ); $i ++) {
			$tem1 .= $all {$i};
			if (($i % 8) == 7) {
				$tem2 .= chr ( hexdec ( base_convert ( $tem1, 2, 16 ) ) );
				$tem1 = '';
			}
		}
		return $tem2;
	}
	function asc_encode($str) {
		$base64_array = array ('b', 'c', 'd', 'e', 'f', 'h', 'i', 'j', 'k', 'l', 'm', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		for($i = 0; $i < strlen ( $str ); $i ++) {
			$temp_bin = fix8bit ( base_convert ( bin2hex ( $str {$i} ), 16, 2 ) );
			$str_bin .= $temp_bin;
		}
		for($i = 0; $i < strlen ( $str_bin ); $i ++) {
			if ($i % 5 == 0)
				$str_bin_add = '000';
			else
				$str_bin_add = '';
			$str_bin_8 .= $str_bin_add . $str_bin {$i};
		}
		for($i = 0; $i < strlen ( $str_bin_8 ); $i ++) {
			$str_bin_end .= $str_bin_8 {$i};
			if ($i % 8 == 7) {
				$index = bindec ( $str_bin_end );
				$base_code .= $base64_array [$index];
				$str_bin_end = '';
			}
		}
		if ($i % 8 != 0) {
			$add_str = '';
			for($j = 0; $j < 8 - ($i % 8); $j ++)
				$add_str .= 'g';
			$index = bindec ( $str_bin_end );
			$base_code .= $base64_array [$index];
			$base_code .= $add_str;
		}
		return $base_code;
	}
	/**
	 * 用于本类的静态调用,子类需要重载才能正常使用.
	 * @access public
	 * @param string $params 类的名称
	 * @return void
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self ( );
		}
		return self::$instance;
	}
}
?>