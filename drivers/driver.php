<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class BootstrapCache_Driver {
	
	const KEY_SEPARATOR = '!';
	
	abstract public function get();
	abstract public function set($data);
	abstract public function remove();
	abstract public function purge();

	// Key

	public function setKey($group, $id) {
		$this->setGroup($group);
		$this->setId($id);
	}

	public function getKey() {
		return $this->group . static::KEY_SEPARATOR . $this->id;
	}

	// ID

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	// Group

	public function getGroup() {
		return $this->group;
	}

	public function setGroup($group) {
		$this->group = $group;
	}

	// Output buffering

	public function start() {
		if (is_null($this->id) || is_null($this->group)) {
			throw new Exception('You must pass an ID and a group to either setKey or to setId and setGroup');
		}

        $data = $this->get();
        if ($data !== false) {
            echo($data);
            return true;
        }
        ob_start();
        ob_implicit_flush(false);
        return false;
	}

	public function end() {
        $data = ob_get_contents();
        ob_end_clean();
        $this->set($data);
        echo($data);
	}
}