<?php

namespace App\Classes;

class System {

	public function reindex($index_name) {
		shell_exec("/usr/bin/indexer --rotate " . $index_name);
		return $this;
	}

	public function call($string) {
		/* shell_exec($string);
		  return $this; */
	}

}
