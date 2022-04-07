  <?php
  $servername = "localhost";
  $username = "coa123cycle";
  $password = "bgt87awx";
  $dbname = "coa123cdb";


  $firstCoun = $_POST["firstCountry"];
  $rankGold = $_POST["rankGold"];
  $totalMedals = $_POST["totalMedals"];
  $numCyclists = $_POST["numCyclists"];
  $avgAge = $_POST["avgAge"];
  // $secondCoun = $_POST["secondCountry"];

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
  }
  echo "<script>console.log('Connected Successfully');</script>";


  //query to get cyclist names where the ISO_id is the same as this country's ISO_id
  $queryNameFirst = "SELECT name FROM Cyclist WHERE ISO_id = '$firstCoun'";
  //query to get country_name where ISO_id is the same as this country's ISO_id
  $queryCountryNameFirst = "SELECT country_name FROM Country WHERE ISO_id = '$firstCoun'";
  //query to get gold, silver, bronze and total medals from each country where ISO_id is the same as this country's
  $queryMedalsFirst = "SELECT gold, silver, bronze, total FROM Country WHERE ISO_id = '$firstCoun'";
  //query to get ISO_id ordered by gold in descending order
  $queryGoldRank = "SELECT ISO_id FROM Country ORDER BY gold DESC";
  //query to get ISO_id ordered by total medals in descending order
  $queryTotalMedals = "SELECT ISO_id FROM Country ORDER BY total DESC";
  //query to ISO_id of countries ordered by how many cyclists they have in descending order
  $queryNumCyclists = "SELECT Country.ISO_id FROM Country INNER JOIN Cyclist ON Country.ISO_id = Cyclist.ISO_id GROUP BY Country.country_name ORDER BY COUNT(Cyclist.name) DESC";
  //query to get the count of cyclists from each country in descending order
  $queryNumCyclistsNum = "SELECT COUNT(Cyclist.name) FROM Country INNER JOIN Cyclist ON Country.ISO_id = Cyclist.ISO_id GROUP BY Country.country_name ORDER BY COUNT(Cyclist.name) DESC";
  //query to get ISO_id and the age of cyclists ordered by age in ascending order
  $queryAvgAge = "SELECT ISO_id as Country, AVG(DATEDIFF('2012-01-01', dob)/365) as Age FROM Cyclist GROUP BY ISO_id ORDER BY Age ASC";
  //query to get ISO_id and the age of cyclists ordered by age in ascending order but age comes first in the row as to be used later
  $queryAvgAgeNum = "SELECT AVG(DATEDIFF('2012-01-01', dob)/365) as Age,  ISO_id as Country FROM Cyclist GROUP BY ISO_id ORDER BY Age ASC";

  //results for the queries
  $resultGoldRank = mysqli_query($conn, $queryGoldRank);
  $resultMedalsRank = mysqli_query($conn, $queryTotalMedals);
  $resultName = mysqli_query($conn, $queryNameFirst);
  $resultCountryName = mysqli_query($conn, $queryCountryNameFirst);
  $resultNumCyclists = mysqli_query($conn, $queryNumCyclists);
  $resultNumCyclistsNum = mysqli_query($conn, $queryNumCyclistsNum);
  $resultAvgAge = mysqli_query($conn, $queryAvgAge);
  $resultAvgAgeNum = mysqli_query($conn, $queryAvgAgeNum);
  $resultMedalsFirst = mysqli_query($conn, $queryMedalsFirst);


  $row_name = mysqli_fetch_array($resultCountryName);

  if($firstCoun != "default"){
  $row_medals = mysqli_fetch_array($resultMedalsFirst);

  //making a table for all the different ranking categories and basic information about the country's olympics standings with the first row being the country's name
  echo "<table style='height: 100%; border-spacing: 10px; table-layout: fixed; width:1800px;'><th style='text-align: center;'>$row_name[0]</th><td>";


  //function to turn the results into an array, no need to order here as the queries were thought out to order the results
  function rankChecker($result){
    $emptyArray = array();

    if(mysqli_num_rows($result) > 0){
      while($row = mysqli_fetch_array($result)){
        array_push($emptyArray,$row[0]);
      }
    }
    return $emptyArray;
  }

  //assigning the rank arrays to variables
  $rankByGold = rankChecker($resultGoldRank);
  $rankTotalMedals = rankChecker($resultMedalsRank);
  $rankNumCyclists = rankChecker($resultNumCyclists);
  $rankNumCyclistsNum = rankChecker($resultNumCyclistsNum);
  $rankAvgAge = rankChecker($resultAvgAge);
  $trueRankAvgAge = rankChecker($resultAvgAgeNum);


  //index of the rank of current country in each field
  $index = array_search($firstCoun, $rankByGold) + 1;
  $indexTotalMedals = array_search($firstCoun, $rankTotalMedals) + 1;
  $indexNumCyclists = array_search($firstCoun, $rankNumCyclists) + 1;
  $trueCyclistNumber = $rankNumCyclistsNum[$indexNumCyclists - 1];
  $indexAvgAge = array_search($firstCoun, $rankAvgAge) + 1;
  $trueAvgAge = $trueRankAvgAge[$indexAvgAge - 1];

  //filling a table with the names of the cyclists in the country
  if(mysqli_num_rows($resultName)>0){
    echo "<table id='insideTable' border=1 style='float: left; height: 370px; overflow-y: scroll; display: block; border-collapse: collapse;'><th>Cyclists in $row_name[0]</th>";
    while($row = mysqli_fetch_array($resultName)){
      echo "<tr><td>" . $row[0] . "</td></tr>";
    }
    echo "</table>";
  }
  //adding categories to the table
  echo "<td> MEDALS EARNED <br> <span id='gold'>GOLD MEDALS: $row_medals[0]</span><br><span id='silver'>SILVER MEDALS: $row_medals[1]</span><br><span id='bronze'>BRONZE MEDALS: $row_medals[2]</span><br><span id='total'>TOTAL MEDALS: $row_medals[3]</span>";
  if($rankGold == "true"){
    echo "<td><span>RANK BY GOLD: #$index</span>";
  }
  if($totalMedals == "true"){
    echo "<td><span>RANK BY TOTAL MEDALS: #$indexTotalMedals</span>";
  }

  if($numCyclists == "true"){
    echo "<td><span>RANK: #$indexNumCyclists<br>TOTAL NUMBER OF CYCLISTS: $trueCyclistNumber</span>";
  }

  if($avgAge == "true"){
    $age = number_format((float)$trueAvgAge, 2, '.', '');
    echo "<td><span>RANK: #$indexAvgAge<br>Average Age: $age</span>";
  }
  echo "</td></tr>";
}

  else {
    echo "<p style='color: red;'>MUST SELECT A COUNTRY</p>";
  }

   ?>
