<?php
require_once ('konfiguration.php');
$limit = "Limit 10";
if($_GET['all'] == 'y'){
    $limit = '';
}

$db_link = mysqli_connect ($host,$user,$pw,$db,'3307');
$sql = "SELECT * FROM speedtest ORDER BY datum DESC $limit";

$db_erg = mysqli_query( $db_link, $sql );
if ( ! $db_erg )
{
    die('Ungültige Abfrage: ' . mysqli_error());
}

echo
'<!DOCTYPE html>
<html lang="de" >
<head>
  <meta charset="UTF-8">
  <title>Blackheart Speedtest</title>
  

<meta name="viewport" content="width=device-width"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

</head>
<body>
<!-- partial:index.partial.html -->
<div class="container">
  <table class="responsive-table">
    <caption>Blackheart Speedtest</caption>
    <thead>
      <tr>
        <th scope="col">Nr</th>
        <th scope="col">Server ID</th>
        <th scope="col">Sponsor</th>
        <th scope="col">Server Name</th>
        <th scope="col">Datum</th>
        <th scope="col">Entfernung</th>
        <th scope="col">Ping</th>
        <th scope="col">Download</th>
        <th scope="col">Upload</th>
        <th scope="col">share</th>
        <th scope="col">IP</th>
      </tr>
    </thead>
    <tbody>';
while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
{
    $time = new DateTime($zeile['datum']);
    $time->modify('+1 hour');
    $timef = $time->format('d.m.Y H:i');
    $zeile['datum'] = $timef;
    $zeile['distance'] = $zeile['distance'].' km';
    $zeile['ping'] = $zeile['ping'].' ms';
    $dprogress = round(($zeile['download']/1000000),2);
    $uprogress = round(($zeile['upload']/1000000),2);
    $dperc = round($dprogress*100/$dmax);
    $uperc = round($uprogress*100/$umax);
    $zeile['download'] = '<b>'.$dprogress.'</b> MB/s';
    $zeile['upload'] = '<b>'.$uprogress.'</b> MB/s';

    echo "<tr>";
    echo "<td>". $zeile['id'] . "</td>";
    echo "<td>". $zeile['server_id'] . "</td>";
    echo "<td>". $zeile['sponsor'] . "</td>";
    echo "<td>". $zeile['server_name'] . "</td>";
    echo "<td>". $zeile['datum'] . "</td>";
    echo "<td>". $zeile['distance'] . "</td>";
    echo "<td>". $zeile['ping'] . "</td>";
    if($dperc < 50)
    {
        echo "<td style=\"background-color:#f00; \">". $zeile['download'] . "</td>";
    }elseif ($dperc >=50 && $dperc < 90){
        echo "<td style=\"background-color:#ff0; \">". $zeile['download'] . "</td>";
    }elseif($dperc >=90){
        echo "<td style=\"background-color:#0f0; \">". $zeile['download'] . "</td>";
    }
    if($uperc < 50)
    {
        echo "<td style=\"background-color:#f00; \">". $zeile['upload'] . "</td>";
    }elseif ($uperc >=50 && $uperc < 90){
        echo "<td style=\"background-color:#ff0; \">". $zeile['upload'] . "</td>";
    }elseif($uperc >=90){
        echo "<td style=\"background-color:#0f0; \">". $zeile['upload'] . "</td>";
    }
    echo "<td>". $zeile['share'] . "</td>";
    echo "<td>". $zeile['ip'] . "</td>";
    echo "</tr>";
}
echo '
</table>
</div>
<!-- partial -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</body>
</html>';

mysqli_free_result( $db_erg );
?>
