<?php
/**
 * @author        YangHuan
 * @datetime   
 * @version        1.0.0
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Lib_Category extends Core_Base {
	private static $instance;
	private $data = array ();
	private $child = array (- 1 => array () );
	private $layer = array (- 1 => - 1 );
	private $parent = array ();
	public function Tree($value) {
		$this->setNode ( 0, - 1, $value );
	}
	public function setNode($id, $parent, $value) {
		$parent = $parent ? $parent : 0;
		
		$this->data [$id] = $value;
		$this->child [$id] = array ();
		$this->child [$parent] [] = $id;
		$this->parent [$id] = $parent;
		
		if (! isset ( $this->layer [$parent] )) {
			$this->layer [$id] = 0;
		} else {
			$this->layer [$id] = $this->layer [$parent] + 1;
		}
	}
	public function getList(&$tree, $root = 0) {
		foreach ( $this->child [$root] as $key => $id ) {
			$tree [] = $id;
			if ($this->child [$id])
				$this->getList ( $tree, $id );
		}
	}
	public function getValue($id) {
		return $this->data [$id];
	}
	public function getLayer($id, $space = false) {
		return $space ? str_repeat ( $space, $this->layer [$id] ) : $this->layer [$id];
	}
	public function getParent($id) {
		return $this->parent [$id];
	}
	public function getParents($id) {
		while ( $this->parent [$id] != - 1 ) {
			$id = $parent [$this->layer [$id]] = $this->parent [$id];
		}
		ksort ( $parent );
		reset ( $parent );
		return $parent;
	}
	public function getChild($id) {
		return $this->child [$id];
	}
	public function getChilds($id = 0) {
		$child = array ($id );
		$this->getList ( $child, $id );
		return $child;
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