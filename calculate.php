<?php
function calculate_abweichung($down = 0, $up = 0){
    $erg = '';
    if($down === 0 && $up > 0){
        $erg = $up - 100;
    }
    if($up === 0 and $down > 0){
        $erg= $down - 100;
    }
    if($up === 0 && $down === 0)
    {
        $erg = '-';
    }
    if ($erg != ''){
        return $erg;
    }else{
        return 0;
    }
}

function set_abweichung($down, $up, $id)
{
    if ($down != '' && $up != '' && $id != '') {
        include('konfiguration.php');
        $db_link = mysqli_connect($host, $user, $pw, $db, $port);
        $sql = "UPDATE speedtest set downabweichung = $down, upabweichung = $up where id = $id";
        $db_erg = mysqli_query($db_link, $sql);
        if (!$db_erg) {
            die('Ungültige Abfrage: ' . mysqli_error());
        } else {
            return false;
        }
    }
}

function calculate_sumabweichung(){
    $i = 1;
    $down = 0;
    $up = 0;
    $erg = '';
    include('konfiguration.php');
    $db_link = mysqli_connect ($host,$user,$pw,$db,$port);
    $sql1 = "SELECT downabweichung from speedtest";
    $sql2 = "SELECT upabweichung from speedtest";
    $sql3 = "SELECT max(id) from speedtest";
    $db_erg1 = mysqli_query( $db_link, $sql1 );
    if ( ! $db_erg1 )
    {
        die('Ungültige Abfrage: ' . mysqli_error());
    }
    $db_erg2 = mysqli_query( $db_link, $sql2 );
    if ( ! $db_erg2 )
    {
        die('Ungültige Abfrage: ' . mysqli_error());
    }
    $db_erg3= mysqli_query( $db_link, $sql3 );
    if ( ! $db_erg3 )
    {
        die('Ungültige Abfrage: ' . mysqli_error());
    }
    while ($zeile1 = mysqli_fetch_array( $db_erg1, MYSQLI_ASSOC)){
        $down += $zeile1['downabweichung'];
    }
    while ($zeile2 = mysqli_fetch_array( $db_erg2, MYSQLI_ASSOC)){
        $up += $zeile2['upabweichung'];
    }
    $anzahl = mysqli_fetch_row($db_erg3);

    $erg .= "&#8595;";
    $erg .= round($down / $anzahl[0]);
    $erg .= "%";
    $erg .= " / &#8593;";
    $erg .= round($up / $anzahl[0]);
	$erg .= "%";
    $erg .= "<br> Anzahl Messpunkte:". $anzahl[0];
	return $erg;
}


?>
