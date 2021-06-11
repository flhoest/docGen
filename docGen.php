<?php

	/*
	  			.___              ________               
			  __| _/____   ____  /  _____/  ____   ____  
			 / __ |/  _ \_/ ___\/   \  ____/ __ \ /    \ 
			/ /_/ (  <_> )  \___\    \_\  \  ___/|   |  \
			\____ |\____/ \___  >\______  /\___  >___|  /
				 \/           \/        \/     \/     \/   v0.1

			(c) 2021 - Frederic Lhoest
	 		docGen.php is a code documentation generator for GitHub.
	 		It reads a php file, detects all functions, inputs and outputs and creates a template from it.
	 		You just need to complete the "<ENTER TEXT HERE>" sections.
	 		As all IT, I'm lazy and becoming fedup to see that no documentation exist for my codes.
	 
	*/


	// ---------------------------------
	// Init Section
	// ---------------------------------
	 
	// File that contains your code to analyze
	$inputFile="library.php";
	
	// File where to write the generated documentation
	$outputFile="README.md";
	
	// If needed you can add the author of the code, it will be added in the author section below
	$author="Frederic Lhoest - *@flhoest*";
	
	// Define the document title
	$documentTitle="Rubrik API Framework (written in Php)";

	// Add logo on the header section (one or more if required, configured in a array as URL)
	$addLogo=TRUE;
	$logoArray=array("http://rubrik.com/wp-content/uploads/2014/10/logo-large-gray.png","https://d1yjjnpx0p53s8.cloudfront.net/styles/logo-thumbnail/s3/062015/php_0.png?itok=W6WL-Rbh");
		
	// Define project introduction
	$addIntro=TRUE;
	$documentIntro="This project's goal is to provide anyone who needs to script automation, a collection of functions that call Rubrik's APIs.
	 				I was facing some challenges around MS SQL DR and I had to start writing some functions to make a good use of what Rubrik is offering out of the box. 
	 				I have tested this framework with Rubrik CDM release 4.2 and 5.0. So far, so good ;)

					Ok, you may want to ask yourself why the hell php ?!? This is a fair question and the answer is quite simple : I'm not a developer, I'm considering myself as a \"all-terrain-IT\" 
					who is doing things without reinventing the wheel. Since I learnt Tubro Pascal, C and later php in my studies it was easy to adopt php since it works on any platform. 
					Most people think php is has been created for dynamic websites development. This is true, but not only. 
					Indeed, you can do almost anything with php : querying SQL DBs, manipulating filesystems, excecuting shell commands, computing heavy statistics, .... 
					and actually limit is the sky. I'm using a lot of php scripts in my daily work for server administration. 
					This is so versatile that when I started to query REST-endpoints, php was obvious. Little by little this framework got enriched and now, I think I have something to play with. 
					Of course, Rubrik is enhancing his API stack and therefore I need to follow and adapt my code. This is not an easy work, but I'm trying to keep on ! ;)";
	$documentIntro=str_replace("\t","",$documentIntro);
	
	// ---------------------------------
	// Local function definition
	// ---------------------------------

	function colorOutput($string)
	{
		return ("\e[1;36m".$string."\033[0m");
	}

	// ---------------------------------
	// Main entry point
	// ---------------------------------

	// Read file to memory
	$tmp=file($inputFile);

	print("\nPreparing functions documentation for ".colorOutput($inputFile)."...\n\n");

	// Identify each indicidual functions
	$functionText=array();
	for($i=0;$i<count($tmp);$i++)
	{
		if(strpos($tmp[$i],"function")==1) 
		{
			$functionName=$tmp[$i];
			$functionName=str_replace("\tfunction","",$functionName);
			$functionName=str_replace(" ","",$functionName);
			$functionName=str_replace(array("\r\n", "\n", "\r"),"",$functionName);
			$functionText[]=$functionName;
		}
	}

 	// All function calls sorted by name
	sort($functionText);

	// Let's isolate function name and parameters

	$functions=array();

 	for($i=0;$i<count($functionText);$i++)
 	{
 		$t=explode("(",$functionText[$i]);
		// remove last char of $t[1] 		
		
		$t[1]=substr_replace($t[1] ,"",-1);

		$functions[$i]["longName"] = $functionText[$i];
		$functions[$i]["name"] = $t[0];
		$functions[$i]["params"] = $t[1];
 	}
 
	// Next, determine the return for every functions

	for($i=0;$i<count($functions);$i++)
	{
		for($j=0;$j<count($tmp);$j++)
		{
			// if start of function found, look for next return statement
			if(strpos($tmp[$j],"function ".$functions[$i]["name"]) && !strpos($tmp[$j],"//"))
			{
// 				print("function found => line ".$j." ".$tmp[$j]."\n");
				while ((!strpos($tmp[$j],"return"))	|| $j==count($tmp) 
						)
				{
					$j++;
				}
				
				// Check end of loop condition
				
				if($j<count($tmp))
				{
					// found
					$r=trim($tmp[$j]);
					$r=str_replace(array("return","(",")",";"),"",$r);
					$functions[$i]["return"] = $r;
				}
				else
				{
					print("Error, not foun\d.\n");
					exit();
					// not found
				}
			}
		}
	}
 	
	// ---------------------------------
 	// Layout section
	// ---------------------------------
 	
	// String that contains the entire file
 	$output="";
 	
 	// Title section
 	$output.="#".$documentTitle;
 	$output.="\n\n";
 	
 	// Add logo id needed
 	if($addLogo)
 	{
 		for($l=0;$l<count($logoArray);$l++)
 		{
 			$output.="![logo".$l."](".$logoArray[$l].")\t";
 		}
 	
 		$output.="\n\n";
 	}
 	
 	// Intro section (aka background)
 	if($addIntro)
 	{
 		$output.="## Background\n";
 		$output.=$documentIntro;
 		$output.="\n";
 	}
 	
//  	var_dump($output);
//  	exit();
 	
 	$output.="## Functions Reference\n\n";
 	$output.="The below section is a list of all existing functions in this framework.\n\n";
 	$output.="### Index\n";
 	$output.="````\n";

	// List all functions and parameters
	
	for($i=0;$i<count($functions);$i++)
	{
		$output.= $functions[$i]["longName"]."\n";
	}

	$output.="````\n\n";
	$output.="### Explanation\n\n";

	for($i=0;$i<count($functions);$i++)
	{
		$output.= "> _" . $functions[$i]["longName"]."_\n\n";
		$output.= "This function is ENTER TEXT HERE\n\n";
		$output.="- Input : `".$functions[$i]["params"]."` -> ENTER TEXT HERE\n";
		$output.="- Output : `".$functions[$i]["return"]."` -> ENTER TEXT HERE\n";
		$output.="- Usage sample : \n\n";
		$output.="```php\n";
		$output.="\$var=".$functions[$i]["longName"].";\n";
		$output.="var_dump(\$var);\n";
		$output.="```\n\n";
		$output.="The above will display : \n\n";
		$output.="```\n ENTER TEXT HERE \n```\n\n";
	}
	
	$output.="## Todo List\n\n";
	$output.="ENTER TEXT HERE\n\n";
	
	$output.="## Versioning\n\n";
	$output.="ENTER TEXT HERE\n\n";
	
	$output.="## Thanks to\n\n";
	$output.="ENTER TEXT HERE\n\n";

	$output.="## Authors\n\n";
	$output.=$author;
	
	$output.="\n\n> Disclaimer : This documentation has been generated with docGen.php. An Open Source initiative producing easy and accurate documentation of php codes in seconds.\n";

	file_put_contents ($outputFile, $output);

	print("Check generated document here : ".colorOutput($outPutFile)."\n");
	print("A total of ".colorOutput(count($functions))." functions has been pre-documented\n");
	print("The generated file is best viewed using MD file parser like ".colorOutput("https://dillinger.io/")." or directly on GitHub\n\n");
	print("End.\n\n");

?>
