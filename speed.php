<?php
include_once ('konfiguration.php');
include_once ('calculate.php');
/*
$limit = "Limit 10";
if(isset($_GET['all']) && $_GET['all'] == 'y'){
    $limit = '';
}
*/

/*
$db_link = mysqli_connect ($host,$user,$pw,$db,$port);
$sql = "SELECT * FROM speedtest ORDER BY datum DESC $limit";

$db_erg = mysqli_query( $db_link, $sql );
if ( ! $db_erg )
{
    die('Ungültige Abfrage: ' . mysqli_error());
}
*/

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
if(isset($_GET['all']) && $_GET['all'] == 'y'){
    $no_of_records_per_page = '1000';
}else{
    $no_of_records_per_page = 10;
}
if(isset($_GET['boxid'])) {
    $id = $_GET['boxid'];
    $boxwhere = "WHERE boxid = $id";
}else{
    $boxwhere = "";
}

$offset = ($pageno-1) * $no_of_records_per_page;

$conn=mysqli_connect ($host,$user,$pw,$db,$port);
// Check connection
if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}

$total_pages_sql = "SELECT COUNT(*) FROM speedtest";
$result = mysqli_query($conn,$total_pages_sql);
$total_rows = mysqli_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

$sql = "SELECT * FROM speedtest $boxwhere order by id desc LIMIT $offset, $no_of_records_per_page";
error_log($sql);
$db_erg= mysqli_query($conn,$sql);
if ( ! $db_erg ) {
    die('Ungültige Abfrage: ' . mysqli_error());
}
mysqli_close($conn);

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
    <caption>Blackheart Speedtest <br> durchschnittliche Abweichung:'.calculate_sumabweichung().'<br> SOLL &#8595; '.$dmax.'MB/s / &#8593; '.$umax.' MB/s </caption>
'; ?>
<caption class="pagination"><a href="?pageno=1">Aktuellste</a> | <?php if($pageno <= 1){ echo "&lt;&lt; 10"; } else { echo "<a href=?pageno=".($pageno - 1).">&lt;&lt; 10</a>";} ?>| <?php if($pageno >= $total_pages){ echo '<span>10 &gt;&gt;</span>'; } else { echo '<a href="?pageno='.($pageno + 1).'">10 &gt;&gt;</a>'; } ?> | <a href="?pageno=<?php echo $total_pages; ?>">&Auml;lteste</a></caption>

<?php
echo '    <thead>
      <tr>
        <th scope="col">Nr</th>
        <th scope="col">Server ID</th>
        <th scope="col">Sponsor</th>
        <th scope="col">Server Name</th>
        <th scope="col">Datum</th>
        <th scope="col">Entfernung</th>
        <th scope="col">Ping</th>
        <th scope="col">Download</th>
        <th scope="col">Down Abweichung</th>
        <th scope="col">Upload</th>
        <th scope="col">Up Abweichung</th>
        <th scope="col">share</th>
        <th scope="col">IP</th>
        <th scope="col">Fritzbox Nr</th>
        <th scope="col">FB Firmware</th>
      </tr>
    </thead>
    <tbody>';
while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
{
    $saveabweichung = 0;
    $id = $zeile['id'];
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
    if($zeile['downabweichung'] == '')
    {
        $dpercabw = calculate_abweichung($dperc,0);
        $saveabweichung++ ;
    }else{
        $dpercabw = $zeile['downabweichung'];
    }
    if($zeile['upabweichung'] == ''){
        $upercabw = calculate_abweichung(0,$uperc);
        $saveabweichung++;
    }else{
        $upercabw = $zeile['upabweichung'];
    }
    if ($saveabweichung > 0){
        set_abweichung($dpercabw,$upercabw,$zeile['id']);
    }
    if($zeile['fw'] == '')
    {
        setfw($fw,$id);
        $zeile['fw'] = $fw;

    }
    if($zeile['boxid'] == '')
    {
        setbox($boxid,$id);
        $zeile['boxid'] = $boxid;

    }


    echo "\n    <tr>\n";
    echo "      <td>". $zeile['id'] . "</td>\n";
    echo "	   <td>". $zeile['server_id'] . "</td>\n";
    echo "      <td>". $zeile['sponsor'] . "</td>\n";
    echo "      <td>". $zeile['server_name'] . "</td>\n";
    echo "      <td>". $zeile['datum'] . "</td>\n";
    echo "      <td>". $zeile['distance'] . "</td>\n";
    echo "      <td>". $zeile['ping'] . "</td>\n";
    if($dperc < 50)
    {
        echo "      <td style=\"background-color:#f00; \">". $zeile['download'] . "</td>\n";
    }elseif ($dperc >=50 && $dperc < 90){
        echo "      <td style=\"background-color:#ff0; \">". $zeile['download'] . "</td>\n";
    }elseif($dperc >=90){
        echo "      <td style=\"background-color:#0f0; \">". $zeile['download'] . "</td>\n";
    }
    echo "      <td><b>". $dpercabw . " % </b></td>\n";
    if($uperc < 50)
    {
        echo "      <td style=\"background-color:#f00; \">". $zeile['upload'] . "</td>\n";
    }elseif ($uperc >=50 && $uperc < 90){
        echo "      <td style=\"background-color:#ff0; \">". $zeile['upload'] . "</td>\n";
    }elseif($uperc >=90){
        echo "      <td style=\"background-color:#0f0; \">". $zeile['upload'] . "</td>\n";
    }
    echo "      <td><b>". $upercabw . " % </b></td>\n";
    echo "      <td>". $zeile['share'] . "</td>\n";
    echo "      <td>". $zeile['ip'] . "</td>\n";
    echo "      <td>". $zeile['boxid'] . "</td>\n";
    echo "      <td>". $zeile['fw'] . "</td>\n";
    echo "    </tr>\n";
}
echo '
  </table>
</div>
<!-- partial -->
<p align="center">speedtest core from <a href = "https://github.com/sivel/speedtest-cli" target="_blank" rel="noopener">https://github.com/sivel/speedtest-cli</a></p>
<img src="transparent_f.png" alt="vault II Logo" style="display: block;margin-left: auto;margin-right: auto;width:130px;height:180px;">
<p align="center"><a href="?all=y" target="_self">Alle anzeigen</a></p>
</body>
</html>';

mysqli_free_result( $db_erg );

function setfw($fw = null, $id)
{
    include('konfiguration.php');
    $db_link = mysqli_connect($host, $user, $pw, $db, $port);

    $sql = "UPDATE speedtest set fw = '$fw' where id = $id";
    error_log (__LINE__.$sql);
    $db_erg = mysqli_query($db_link, $sql);
    if (!$db_erg) {
        die('Ungültige Abfrage: ' . mysqli_error());
    } else {
        return false;

    }
}

function setbox($boxid = NULL, $id)
{
    include('konfiguration.php');
    $db_link = mysqli_connect($host, $user, $pw, $db, $port);
    $sql = "UPDATE speedtest set boxid = $boxid where id = $id";
    $db_erg = mysqli_query($db_link, $sql);
    if (!$db_erg) {
        die('Ungültige Abfrage: ' . mysqli_error());
    } else {
        return false;
    }
}
?>
