<?php
	class CWD {
		private $files;
		private $lines;
		private $root;
		private $cwd;

		public function __construct($root) {
			$this->files = 0;
			$this->lines = 0;
			$this->root = $root;
			$this->cwd = scandir($root);
		}

		public function countFiles($haystack = array(), $debug = false) {
			$this->files = 0;
			$array = $this->rlist($this->cwd, $this->root, $haystack, $debug); 
			
			return $array['files'];
		}

		public function countLines($haystack = array(), $debug = false) {
			$this->files = 0;
			$array = $this->rlist($this->cwd, $this->root, $haystack, $debug); 
			
			return $array['lines'];
		}

		public function countAll($haystack = array(), $debug = false) {
			$this->files = 0;
			$this->lines = 0;
			$array = $this->rlist($this->cwd, $this->root, $haystack, $debug);
			
			return $array;
		}

		private function rlist($dirArray, $root, $haystack, $debug = false) {
			// se é folha
			if(!is_array($dirArray)){
				if(!is_dir($root)) {
					$this->files++;
					
					$lines_counter = file($root);
					$this->lines += count($lines_counter);
				}
				else {
					$subdir = scandir($root);
					
					if($debug) {
						echo "<strong>{$root}</strong><br/>";
						echo "<pre>";print_r($subdir);echo"</pre>";
					}

					//echo $files;				
					return $this->rlist($subdir, $root, $haystack, $debug);
				}

			}
			// avança até o filho
			else if (is_array($dirArray)) {
	        	foreach ($dirArray as $key => $value) {
	        		if($key > 1  && !in_array($value, $haystack) ) {        			
	            		$dirArray[$key] = $this->rlist($value, $root."/".$dirArray[$key], $haystack, $debug);
	        		}
	            }
	        }

	        return array("files" => $this->files, "lines" => $this->lines);
	    }
	}

	$root = str_replace("\\", "/", getcwd());
	$root .= "../..";

	$counter = new CWD($root);
	print_r($counter->countAll(array(".git", "Twig"), true ));

?>
