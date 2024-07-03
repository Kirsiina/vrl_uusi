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
$url = 'http://virtuaalihevoset.net/rajapinta/porrastetut/'.$vh;
$obj = fetchApiData($url);

if(isset($obj['error']) && $obj['error'] == 0){        
    $data = $obj['porrastetut'];
    
    $info = $data['info'];
    /* info sisältää
     * $info['ominaisuudet'] (kaikki VRL:sä käytössä olevat ominaisuudet, muodossa id =>nimi)
     * $info['jaokset'] (kaikki VRL:ssä porrastettuja kelpuuttavat jaokset muodossa id=>tietotaulukko)
     *      Tietotaulukossa seuraavat kentät. esimerkkinä KERJ:
     *       "jaos_nimi":"Kenttäratsastusjaos"
     *       "jaos_lyhenne":"KERJ",
     *       "ominaisuudet":["1","2","3","4"]},
     * */
    
    $hevonen = $data['hevonen'];
    /* hevonen sisältää
     * $hevonen['info'] sisältää kaikki hevosen perustiedot joita tarvitaan porrastetuissa
     *      "max_taso_per_ika":10,
     *      "sakakorkeus":"125"
     *      "ika": sisältää taulukon kaikista ikääntymiseen liittyvistä tiedoista, esim:
     *          "syntymaaika":"2007-12-22",
     *          "3vuotta":"2007-12-22",
     *          "4vuotta":"2008-07-01",
     *          "5vuotta":"2008-08-18",
     *          "6vuotta":"2008-10-05",
     *          "7vuotta":"2008-11-22",
     *          "8vuotta":"2009-01-09",
     *          "ika": 7,
     *          "ikaantyminen_paivaa": 0,
     *          "ikaantyminen_ika" = 0}
     *          Ikä sisältää iän syntymäpäivien mukaan, ikääntyminen sisältää hevosen ikääntymisen (montako päivää on yksi vuosi),
     *          ja ikaantyminen_ika on sen perusteella laskettu ikä. Porrastetuissa käytetään kuitenkin aina "ika" kentässä olevaa ikää.
     * $hevonen['ominaisuudet'] sisältää taulukon hevosen ominaisuuspisteistä ominaisuuksittain esim.
     *          1=>{"ominaisuus":1,"pisteet":230.25},
     *          2=>{"ominaisuus":2,"pisteet":230.25}
     * $hevonen['tasot] sisältää taulukon hevosen tasosta jaoksittain, esim.
     *          2=>{"jaos":2,"pisteet":2523.5,"taso":6,"max_taso_per_pisteet":6,"taso_rajoitus":"4"}
     *          jossa
     *              taso on hevosen taso on taso, jossa kaikki vaikuttavat tekijät on otettu huomioon
     *              max_taso_per_pisteet on taso jos vain pisteet vaikuttavat
     *              taso_rajoitus on hevoselle VRL:ssä asetettu kilpailurajoitus
     *
     *          Mikäli hevonen ei jostain syystä voi kilpailla porrastetuissa, $hevonen['tasot'] sisältää myös
     *              "error" = 1 (muussa tapauksessa "error" = 0)
     *              sekä "error_message" = "Ongelman kuvaus"
     */
    
    /***************************************************************************************
     *     ESIMERKKEJÄ TULOSTAMISEEN
     *     *********************************************************************************/
    
    //ESIMERKKI 1: Näin tulostetaan ominaisuuspisteet
    foreach ($hevonen['ominaisuudet'] as $id=>$ominaisuus){
        $ominaisuusnimi = $info['ominaisuudet'][$id];
        $ominaisuuspisteet = $ominaisuus ['pisteet'];
        
        //Tulostus
        echo $ominaisuusnimi . ": " . $ominaisuuspisteet . "<br>";
    }
    
    //ESIMERKKI 2: Näin tulostetaan tasot
    if($hevonen['error'] == 1){
        echo $hevonen['error_message'];
    }else {
        foreach ($hevonen['tasot'] as $jaos=>$tasoinfo){
            $jaosnimi = $info['jaokset'][$jaos]['jaos_nimi'];
            $jaoslyhenne = $info['jaokset'][$jaos]['jaos_lyhenne'];
            $taso = $tasoinfo['taso'];
            $max_taso_per_ika = $hevonen['info']['max_taso_per_ika'];
            $max_taso_per_pisteet = $tasoinfo['max_taso_per_pisteet'];
            $max_taso_rajoitus = $tasoinfo['taso_rajoitus'];
            
            //Tulostus
            echo $jaosnimi . " (". $jaoslyhenne.") : ";
            echo "Taso: " . $taso;
            echo " (Maksimi iän perusteella: " . $max_taso_per_ika . ", maksimi pisteiden perusteella: " . $max_taso_per_pisteet . ", valittu maksimitaso: " . $max_taso_rajoitus . ")";
            echo "<br>";
            
            
        }
    }
        
    //ESIMERKKI 3: Näin tulostetaan vain yksi jaos (pitää tietää jaoksen ID)
    $jaos = 1; //tämä on erj
    
    if($hevonen['error'] == 1){
        echo $hevonen['error_message'];
    }else {
        $tasoinfo = $hevonen['tasot'][$jaos];
        $jaosnimi = $info['jaokset'][$jaos]['jaos_nimi'];
        $jaoslyhenne = $info['jaokset'][$jaos]['jaos_lyhenne'];
        $taso = $tasoinfo['taso'];
        $max_taso_per_ika = $hevonen['info']['max_taso_per_ika'];
        $max_taso_per_pisteet = $tasoinfo['max_taso_per_pisteet'];
        $max_taso_rajoitus = $tasoinfo['taso_rajoitus'];
        $jaoksenominaisuudet = $info['jaokset'][$jaos]['ominaisuudet'];
        
        //Tulostus
        echo $jaosnimi . " (". $jaoslyhenne.") : ";
        echo "Taso: " . $taso;
        echo " (Maksimi iän perusteella: " . $max_taso_per_ika . ", maksimi pisteiden perusteella: " . $max_taso_per_pisteet . ", valittu maksimitaso: " . $max_taso_rajoitus . ")";
        echo "<br>";
        //Tulostetaan vain tähän jaokseen vaikuttavat ominaisuudet
        foreach ($jaoksenominaisuudet as $id){
                $ominaisuusnimi = $info['ominaisuudet'][$id];
                $ominaisuuspisteet = $hevonen['ominaisuudet'][$id]['pisteet'];
                
                //Tulostus
                echo $ominaisuusnimi . ": " . $ominaisuuspisteet . "<br>";
        }
        
    }
    
    /***************************************************************************************
     *     ESIMERKIT LOPPUU TÄHÄN
     *     *********************************************************************************/
    
    
    
    
    
} else {
    echo '<p>Tapahtui virhe tietoja haettaessa.</p>';
}

?>