<?php
	//Include class file
	include_once("simplelang.class.php");
	
	//Define a class
	$l = new phpSimpleLanguage();
	
	//Checking if user set other language
	if (isset($_GET['lang'])) $l->setUserLanguage($_GET['lang']);
	
	//Check if form was submitted
	if($_POST['name']!="") { 
		echo $l->lang("HELLO", array("name" => $_POST['name']));
	}
	
	//Demonstration of using variables
	echo $l->lang("NAMEFORM", array("value" => $_POST['name']));
	
	//Demonstration of simple definition
	echo "<h3>".$l->lang("AVAILIBLE_LANGUAGES")."</h3>"; 
	echo $l->getUserLanguage();
	//Language bar
	foreach($l->getDictionaryFiles() as $b) {
		echo "<a href=\"?lang={$b['LANGCODE']}\">{$b['LANGUAGE']}</a> ";
	}
?>
