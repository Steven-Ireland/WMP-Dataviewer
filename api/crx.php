<html><body>
<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	$profname = 'err';
	if (isset($_GET['prof']))
	    $profname=$_GET['prof'];
	else
		echo("There was no professorname");



	// We need to return:
	// Professor ranking
	// List of classes taught with ratings
	// List of data points (ratings)


	$loginfo = fopen("../loginfo.txt", "r");
    $servername = "localhost";
    $username = trim(fgets($loginfo));
    $password = trim(fgets($loginfo));
    $dbname = trim(fgets($loginfo));
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        echo("Connection failed: " . $conn->connect_error);
  	} 

  	$findProfnum = 
      	"select P.prof_name, avg(question_avg) as rating, count(question_avg) as classes_taught from questions Q, professors P 
      	where P.prof_id = Q.class_prof_id and question_text like '1.%' 
      	group by class_prof_id 
      	having classes_taught > 1  
      	order by rating desc";

	$result = $conn->query($findProfnum);
  	$rownum = 1;
  	if ($result->num_rows > 0) {
    	while(($row = $result->fetch_assoc()) && $row["prof_name"]!=$profname) {
    		$rownum = $rownum + 1;
    	}
    }
    else echo("Professor $profname not found");

 	$findProfClasses = 
        "select class_title, avg(question_avg) as rating from classes natural join questions
        where class_prof_id in (select prof_id from professors where prof_name = '$profname')
        and question_text like '%1.%'
        group by class_title";

	$result = $conn->query($findProfClasses);
	$classes = array();

	if ($result->num_rows > 0) {
    	while($row = $result->fetch_assoc()) {
    		$classes[] = array(urldecode($row["class_title"]), $row["rating"]);
    	}
    }
    else echo("Professor $profname classes not found");


    $findProfDistribution =
    	"select sum(question_ones) as one, sum(question_twos) as two, sum(question_threes) as three, sum(question_fours) as four, sum(question_fives) as five, avg(question_avg) as avg
		from questions
		group by class_prof_id
		having class_prof_id in (select prof_id from professors where prof_name = '$profname')";

	$result = $conn->query($findProfDistribution);
	$dist = array();

	if ($result->num_rows>0)
		$dist = $result->fetch_assoc();
	else echo ("No Dist Found for $profname");

	echo json_encode(array('rank'=>$rownum, 'classes'=>$classes, 'dist'=>$dist));

?>
</body></html>