<?php
/*
phpSimpleLanguage version 1.0 Beta
Multi lingual engine for PHP


Copyright (C) 2013 Filip Meštrić <filip@enytstudio.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

class phpSimpleLanguage {
	//Configuration starts here
	private $defaultLang = "en"; //The default language which will be displayed to user who didn't select his language yet
	private $cookieName = "userlang"; //Name of the cookie where user's selected language will be store
	private $dictionaryFolder = "dictionary"; //Path to dictionary folder
	//Configuration ends here

	
	//Parse dictionary
	function getDictionary($language) {
		//Get corresponding dictionary file
		$dictionary = file_get_contents($this->dictionaryFolder."/$language.lang");
		//Split file by lines
		$dictionary = explode("\n", $dictionary);
		//Empty array where our definitions will be
		$return = Array();
		//Loop through definitions
		foreach($dictionary as $definition) {
			if ($definition != "") {
				//Remove commented parts
				$definition = preg_replace("/(?!<\")\/\*[^\*]+\*\/(?!\")/", "", $definition);
				
				//Check if line is part of multi line definition 
				if(substr($definition, 0, 1)=="\t") {
					//Add line to main definition
					$return[$lastKey].=" ".str_replace("\t", "", trim($definition));
				}
				else {
					//Split key and definition
					$def = explode("=", $definition, 2);
					
					//Check if key already exists
					if(!array_key_exists(trim($def[0]), $return)) {
						//Add to array
						$return[trim($def[0])] = trim($def[1]);
						
						//For merging lines
						$lastKey = trim($def[0]);
					}
				}
			}
		}
		//Clean array from empty definitions and define if doDefine is set to 1
		foreach ($return as $key => $value) {
			if ($key == "" || $value == "") {
				unset($return[$key]);
			}
		}
		//Return dictionary
		return $return;
	}
	
	//Parse definition
	public function lang($key, $vars = Array(), $language = null) {
		if ($language == null) $language = $this->getUserLanguage(); 
		//Get corresponding dictionary
		$dictionary = $this->getDictionary($language);
		//Get value
		$value = $dictionary[$key];
		//Check if we have to set any variables
		if (!empty($vars)) {
			//Set variables
			foreach ($vars as $key => $var) {
				$value = str_replace("[%$key%]", $var, $value);  
			}
			//Return value
			return $value;
		}
		else {
			//Return value
			return $value;
		}
	}
	
	//Get all available languages 
	public function getDictionaryFiles() {
		//Get list of files
		$files = scandir($this->dictionaryFolder);
		//Array of data
		$info = array();
		//Loop trough files
		foreach ($files as $file) {
			//Check if file is not '.' or '..'
			if($file != '.' && $file != '..') {
				//Get language code
				$f = str_replace(".lang", "", $file); 
				
				//Get file content
				$content = file_get_contents($this->dictionaryFolder."/$file");
				//Split content by line
				$content = explode("\n", $content);
				//Put langcode in array
				$info[$f]['LANGCODE'] = $f;
				//Loop trough lines
				foreach($content as $c) {
					//Remove comment
					$c = preg_replace("/##.*$/", "", $c);
					//Match property
					preg_match("/(.*): (.*)/", $c, $m);
					//Check if matches are not empty
					if(!empty($m)) {
						//Add property to array
						$info[$f][trim($m[1])] = trim($m[2]);
					}
				}
			}
		}
		//Return property
		return $info;
	}
	
	//Get user language
	public function getUserLanguage() {
		if(isset($_COOKIE['psl_lang'])) return $_COOKIE['psl_lang'];
		else return $this->defaultLang;
	}
	
	//Change user language
	public function setUserLanguage($langcode) {
		//Save in cookie
		setcookie('psl_lang', $langcode);
	}
}
?>
