<?php
require('check_log.php');

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Biznes-Opłacanie</title>
    <meta name="keyword" content="zestawienie, fiskus, allegro">
	<meta name="description" content="prowadzenie zestawień dla skarbówki">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="script.js"></script>

    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body onload="zegar()">
    <div id="container">
        <header>
            <h1>Oplacanie zamówienia</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr><th colspan="3">Zamówienia </th></tr>
                    <tr><th>Nick</th><th>dane</th><th>kwota</th></tr>
                </thead>
                <?php
                require_once 'connect.php';
                try{
                    $query=$polaczenie->query("SELECT id_zamowienia, nick,(kwota+przesylka) as kwota from zamówienie join kupujacy using(id_kup) where oplacone=0");
                    $rezultaty=$query->fetchall();
                    foreach($rezultaty as $rez){
                        $query=$polaczenie->query("SELECT nazwa_towaru,nr_oferty,sztuki,cena from sprzedane_produkty where id_zamowienia=".$rez['id_zamowienia']);
                        $wyniki=$query->fetchall();
                        $dane_towaru='';
                        foreach($wyniki as $wynik){
                            $dane_towaru.=$wynik['nazwa_towaru'].' '.$wynik['cena'].'zł nr:'.$wynik['nr_oferty'].' '.$wynik['sztuki']."szt.<br/>";
                        }
                        echo '<tr>';
                            echo"<td>".$rez['nick']."</td>";
                            echo'<td>'.'<a href="zam-dodaj.php?tryb=nieoplacone&id_zamowienia='.$rez['id_zamowienia'].'" class="table-link">'.
                            $dane_towaru.'</a></td>';
                            echo"<td>".$rez['kwota']."</td>";
                        echo '</tr>';
                    }
                } catch(PDOException $e){echo "Coś poszło nie tak, proszę spróbować później";}
                ?>
            </table>
        </main>

    </div>
</body>
</html>