<?php 
function fetchApiData($url) {
    $ch = curl_init();

    // Asetetaan cURL-asetukset
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Asetetaan kokonaisaikakatkaisu 5 sekunniksi
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Asetetaan yhdistämisen aikakatkaisu 5 sekunniksi
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seurataan uudelleenohjauksia
    curl_setopt($ch, CURLOPT_FAILONERROR, true); // Palauttaa virheen HTTP-koodilla 4xx tai 5xx
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // Lisätty cURL-signaalien estämiseksi aikakatkaisun vuoksi

    $output = curl_exec($ch);

    // Tarkistetaan, tapahtuiko virhe
    if (curl_errno($ch)) {
        $error_message = curl_error($ch);
        curl_close($ch);
        return ['error' => 1, 'error_message' => $error_message];
    }

    // Tarkistetaan, jos palautusarvo on false
    if ($output === false) {
        $error_message = curl_error($ch);
        curl_close($ch);
        return ['error' => 1, 'error_message' => $error_message];
    }

    curl_close($ch);

    return json_decode($output, true);
}

//Muokkaa tähän hevosesi VH-tunnus
$vh = 'VH03-028-8756';
$url = 'http://virtuaalihevoset.net/rajapinta/varsat/'.$vh;
$obj = fetchApiData($url);

if(isset($obj['error']) && $obj['error'] == 0){
    $varsat = $obj['varsat'];
    
    if(sizeof($varsat) == 0){
        echo "Ei jälkeläisiä";
    }
    else {
        foreach ($varsat as $varsa){
            $reknro =  $varsa['reknro'];
            $nimi = $varsa['nimi'];
            $rotunro = $varsa['rotunro']; //katso VRL:stä mikä numero vastaa mitäkin rotua
            $rotulyhenne = $varsa['rotulyhenne'];
            $vari =  $varsa['vari']; //katso VRL:sta mikä numero vastaa mitäkin väriä
            $varilyhenne = $varsa['varilyhenne'];
            $sukupuoli = $varsa['sukupuoli']; // 1=tamma, 2=ori, 3=ruuna
            $syntymaaika = $varsa['syntymaaika'];
            $url = $varsa['url'];
            $rek_url = $varsa['rek_url'];
            $vanhempi = $varsa['vanhempi']; //samassa muodossa kuin varsan omat tiedot
            
            
            $skp_kirjain = array(1=>"t", 2=>"o", 3=>"r");
            $vanhempi_kirjain = array(1=>"e", 2=>"i", 3=>"i");
    
            //tulostus
            echo $syntymaaika . ", " . $skp_kirjain[$sukupuoli] . ". <a href=\"".$url."\">".$nimi."</a> (<a href=\"".$rek_url."\">".$reknro."</a>)";
            
            if(isset($vanhempi) && sizeof($vanhempi)>0){
                echo ", " . $vanhempi_kirjain[$vanhempi['sukupuoli']] . ". <a href=\"".$vanhempi['url']."\">".$vanhempi['nimi']."</a> (<a href=\"".$vanhempi['rek_url']."\">".$vanhempi['reknro']."</a>)";
            }
            echo "<br>";
            
        }
    }
    
    
    
    
} else { 
    echo '<p>Tapahtui virhe tietoja haettaessa.</p>';
}

?>