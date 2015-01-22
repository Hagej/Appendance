<!DOCTYPE html>
<html>
	<head>
		<title>TEST FETCHER</title>
		<meta charset="utf-8">
	</head>
	<body>



	<form method="post" action="">
		Skriv in din kurskod:</br>
    	<input type="text" name="something" value="<?= isset($_POST['something']) ? htmlspecialchars($_POST['something']) : '' ?>" />
    	<input type="submit" name="submit" />
  	</form>



	<?php
		# Fetch data from text field

		if(isset($_POST['submit'])) {
			$submittedData = htmlspecialchars($_POST['something']); //htmlspecialchars to convert from html
  			echo "Vald kurskod: $submittedData </br>";
  		}



		include_once('simple_html_dom.php'); //for html source code parsing http://simplehtmldom.sourceforge.net

		echo "</br>TEST 1</br></br>";
		//this is php-code

		$courseCode = "TMV206";

		# Trying to get data-id for the course code
		echo "Trying to fetch course code $courseCode... </br></br>";
		$urlDataID = "https://se.timeedit.net/web/chalmers/db1/public/objects.html?max=15&fr=t&partajax=t&im=f&sid=3&l=sv&search_text=" . $courseCode . "&types=182";
		$html = file_get_html($urlDataID); //needs allow_url_fopen to be enabled in PHP-settings
		$elem = $html->find('[id=objectbasketitemX0]',0); //finding DIV which contains data-id for course
		$dataID = $elem->attr['data-id']; //our data-id

		echo "Woho! data-id for course code " . $courseCode . " is " . $dataID;


		#Fetch scheme from timeedit
		echo "</br>... now lets try to fetch scheme!</br>";



		$dateToday = date("Ymd");
		echo "</br>Today is " . $dateToday . "<br>";

		$fourWeeks = strtotime("+4 Weeks");
		$dateInFuture = date("Ymd",$fourWeeks);
		echo "4 weeks from now is: " . $dateInFuture . "</br>";

		#We now got all date variables, lets try fetch scheme.

		$urlScheme = "https://se.timeedit.net/web/chalmers/db1/public/ri.json?sid=3&p=" . $dateToday . ".x%2C" . $dateInFuture . ".x&objects=" . $dataID . "&ox=0&types=0&fe=0"; //url as json. it will automatically choose friday in same week as dateInFuture

		//echo "URL for scheme is: " . $urlScheme . "</br>";

		$htmlScheme = file_get_html($urlScheme); //getting our html
		$decodedScheme = json_decode($htmlScheme, true); //now decoded to json



		#All data gathered, now mining
		echo "</br>Lets try to find our Föreläsningar <br/><br/>";

		$schemeData = $decodedScheme['reservations'];

		foreach ($schemeData as $values) {
			echo "_____________________</br>";
			echo "ID: ". $values["id"]. "</br>";
			echo "Starts: ". $values["startdate"] ." ". $values["starttime"]. "</br>";
			echo "Ends: ". $values["enddate"] ." ". $values["endtime"]. "</br>";

			
			$data = $values["columns"];
			echo "Name: ". $data[0] ."</br>";
			echo "Location: ". $data[1] ."</br>";
			echo "Class: ". $data[2] ."</br>";
			echo "Type: " . $data[5] ."</br>";

			echo "</br></br>";


			/*foreach ($valueArray as $value) {
				echo "value: " . $value . "</br>"; // here we get Date and Times
				foreach ($value as $v) {	//whenever value is an array it will go through
					echo "v: " . $v . "</br>";
					if ($v == "Föreläsning") {
						echo "------ F&ouml;relsnign!! ------ <br/>";
					}
				}
			}*/

		}




		

		echo "</br></br></br>";
		echo $htmlScheme; //printing out all json info



	?>


	
	</body>
</html>