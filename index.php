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
        <h1>WPI Professors</h1>
      </div>
    </div>
    <div class="col-md-offset-1 col-md-10">

      <table id="maintable" class="table table-hover table-bordered sortable">
        <?php
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
          "select P.prof_name, avg(question_avg) as rating, count(question_avg) as classes_taught from questions Q, professors P 
          where P.prof_id = Q.class_prof_id and question_text like '1.%' 
          group by class_prof_id 
          having classes_taught > $minClasses  
          order by rating desc";

          $result = $conn->query($sql);
          $rownum = 1;
          if ($result->num_rows > 0) {
              echo "<tr><th>#</th><th>Professor</th><th>Rating</th><th>Classes Taught</th></tr>";
              // output data of each row
              while($row = $result->fetch_assoc()) {
                  echo "<tr><td>$rownum</td><td><a href=\"/professor.php?prof=".urlencode($row["prof_name"])."\">".$row["prof_name"]."</a></td><td data-sortable=\"true\">".$row["rating"]."</td><td>".$row["classes_taught"]."</td></tr>";
              $rownum = $rownum+1;
              }
          } else {
              echo "0 results";
          }
          $conn->close();
        ?>
      </table>
    </div>
  </body>

</html>