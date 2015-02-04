<html>
  
  <head>
    <!-- Latest compiled and minified bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    
    <!-- Latest compiled and minified JQUERY JS -->
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

    <!-- Tablesorter -->
    <script src="sortable.js"></script>

    <!-- MY CSS / SCRIPTS -->
    <link rel="stylesheet" href="style.css">
    <script src="scripts.js"></script>
  </head>

  <body>

    <div class="jumbotron">
      <div class="container">
        <h1>WPI Professor <?php $profname = "Not Found";
        if (isset($_GET['prof']))
          $profname=$_GET['prof'];
        echo $profname." <a href=\"/api/crx.php?prof=".urlencode($profname)."\">(api)</a>" ?></h1>
      </div>
    </div>
    <div class="col-md-offset-1 col-md-10">
       <table class="table table-hover table-bordered sortable">
        <?php 

        $profname = "Not Found";
        if (isset($_GET['prof']))
          $profname=$_GET['prof'];


          $loginfo = fopen("loginfo.txt", "r");
          $servername = "localhost";
          $username = trim(fgets($loginfo));
          $password = trim(fgets($loginfo));
          $dbname = trim(fgets($loginfo));

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);
          // Check connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          } 
          $minClasses = 5;
          if (isset($_GET['numClassMin']))
            $minClasses = intval($_GET['numClassMin']);


          $sql = 
          "select class_title, avg(question_avg) as rating from classes natural join questions
          where class_prof_id in (select prof_id from professors where prof_name = '$profname')
          and question_text like '%1.%'
          group by class_title";

          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              echo "<tr><th>Class</th><th>Rating</th></tr>";
              // output data of each row
              while($row = $result->fetch_assoc()) {
                  echo "<tr><td>".urldecode($row["class_title"])."</td><td>".$row["rating"]."</td></tr>";
              }
          } else {
              echo "0 results";
          }
          $conn->close();
        ?>
      </table>
    </div>

</body>