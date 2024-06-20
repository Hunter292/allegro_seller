<?php
require('check_log.php');

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Statystyki</title>
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
            <h1>Statystyki</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <div>
                <?php 
                    function process_date(){
                        $dane=explode('-',$_POST['date']);
                        if($dane[0]==1) $dane[0]="1, 2, 3";
                        if($dane[0]==2) $dane[0]="4, 5, 6";
                        if($dane[0]==3) $dane[0]="7, 8, 9";
                        if($dane[0]==4) $dane[0]="10, 11, 12";
                        return $dane;
                    }
                    if(isset($_POST['nick'])){
                        $nick=$_POST['nick'];
                        echo '<table border="1" cellpadding="10" cellspacing="0">';
                        echo '<thead><tr><th colspan="5" >'.$nick.'</th></tr>';
                        
                        echo '<tr><th>Data</th><th>Towary</th><th>Kwota</th><th>Przesyłka</th><th>Przelew</th></tr></thead>';
                        require_once 'connect.php';
                        $query=$polaczenie->query("SELECT id_kup from kupujacy where nick='$nick'");
                        $kupujacy=$query->fetchAll();
                        if($_POST['date']){
                            $dane=process_date();
                            $date=$_POST['date'];
                        }
                        $suma=0;
                        $sum_przesylka=0;
                        foreach($kupujacy as $kup){
                            $zapytanie="SELECT id_zamowienia, data, kwota, przesylka, przelew from zamówienie where id_kup=".$kup['id_kup'];
                            if($_POST['date'])$zapytanie.=" and YEAR(data)=$dane[1] and MONTH(data) IN($dane[0])";
                            $query=$polaczenie->query($zapytanie);
                            $zamowienia=$query->fetchall();
                            foreach($zamowienia as $zamowienie){
                                $query=$polaczenie->query("SELECT nazwa_towaru, nr_oferty, cena, sztuki from sprzedane_produkty where id_zamowienia=".$zamowienie['id_zamowienia']);
                                $produkty=$query->fetchall();
                                $dane_towaru='';
                                $suma+=$zamowienie['kwota'];
                                $sum_przesylka+=$zamowienie['przesylka'];

                                foreach($produkty as $produkt) $dane_towaru.=$produkt['nazwa_towaru'].' '.$produkt['cena'].'zł nr_oferty:'.$produkt['nr_oferty'].' '.$produkt['sztuki']."szt.<br/>";
                                echo '<tr><td>'.$zamowienie['data'].'</td><td>'.$dane_towaru.'</td><td>'.$zamowienie['kwota'].
                                '</td><td>'.$zamowienie['przesylka'].'</td><td>'.($zamowienie['przelew']==1? 'Tak':'Nie').'</td></tr>';
                            }                            
                        }
                        echo '<tr><td colspan="2" >Suma</td><td>'.$suma.
                        '</td><td>'.$sum_przesylka.'</td><td>'.$suma-$sum_przesylka.'</td></tr></table>';
                    }
                    ?>
                <br/>
            </div>
            <article>
                <h3>Kupujący</h3>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                    <label>kwartał i rok-puste dla całego okresu</label>
                    <input type="text" placeholder="0-0000"  pattern="[1-4]-\d{4}" name="date" value="<?=isset($date)?$date:''?>" >
                    <label>nick</label>
                    <input type="text"   name="nick">
                    <br>
                    <input type="submit" class="tabele" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
                <?php 
                if(isset($_POST['produkt'])){
                    $produkt =$_POST['produkt']? $_POST['produkt']:'Najlepiej się sprzedające';
                    require_once 'connect.php';
                    echo '<table border="1" cellpadding="10" cellspacing="0">';
                    echo '<thead><tr><th colspan="2" >'.$produkt.'</th></tr>';
                    
                    echo '<tr><th>Produkt</th><th>Sprzedane</th></tr></thead>';
                    $zapytanie="SELECT nazwa_towaru, sum(sztuki) as sprzedane from sprzedane_produkty INNER JOIN zamówienie using(id_zamowienia) where";
                    if($_POST['date']){
                        $dane=process_date();
                        $date=$_POST['date'];
                        $zapytanie.=" YEAR(data)=$dane[1] and MONTH(data) IN($dane[0]) and";
                    }
                    if($_POST['produkt']) $zapytanie.=" nazwa_towaru='$produkt' group by nazwa_towaru";
                    else $zapytanie.=" cena>0 group by nazwa_towaru order by sprzedane desc limit 10";
                    $query=$polaczenie->query($zapytanie);
                    $rezultat=$query->fetchall();
                    foreach($rezultat as $rez) echo '<tr><td>'.$rez['nazwa_towaru'].'</td><td>'.$rez['sprzedane'].'</td></tr>';
                    echo '</table>';
                }
                ?>

                <h3>Produkt</h3>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                    <label>kwartał i rok-puste dla całego okresu</label>
                    <input type="text" placeholder="0-0000"  pattern="[1-4]-\d{4}" name="date" value="<?=isset($date)?$date:''?>" >
                    <label>Produkt - puste da 10 najlepiej się sprzedających</label>
                    <input type="text"   name="produkt">
                    <br>
                    <input type="submit" class="tabele" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>

                <?php
                if(isset($_POST['zwrot'])){
                    $produkt =$_POST['zwrot']? 'Zwrot-'.$_POST['zwrot']:'Najczęściej wracające';
                    require_once 'connect.php';
                    echo '<table border="1" cellpadding="10" cellspacing="0">';
                    echo '<thead><tr><th colspan="2" >'.$produkt.'</th></tr>';
                    
                    echo '<tr><th>Produkt</th><th>Zwrócenia</th></tr></thead>';
                    $zapytanie='SELECT nazwa_towaru, sum(sztuki) as sprzedane from sprzedane_produkty INNER JOIN zamówienie using(id_zamowienia) where nazwa_towaru LIKE "Zwrot-%"';
                    if($_POST['date']){
                        $dane=process_date();
                        $date=$_POST['date'];
                        $zapytanie.=" and YEAR(data)=$dane[1] and MONTH(data) IN($dane[0])";
                    }
                    if($_POST['zwrot']) $zapytanie.=" and nazwa_towaru='$produkt' group by nazwa_towaru";
                    else $zapytanie.="  group by nazwa_towaru order by sprzedane desc limit 10";
                    $query=$polaczenie->query($zapytanie);
                    $rezultat=$query->fetchall();
                    foreach($rezultat as $rez){
                        $err=0;
                        $zapytanie="SELECT sum(sztuki) as sprzedane from sprzedane_produkty INNER JOIN zamówienie using(id_zamowienia) where nazwa_towaru='".substr($rez['nazwa_towaru'],6)."'";
                        if($_POST['date'])$zapytanie.=" and YEAR(data)=$dane[1] and MONTH(data) IN($dane[0])";
                        $zapytanie.=" group by nazwa_towaru";
                        $query=$polaczenie->query($zapytanie);
                        $sztuki=$query->fetch();
                        if(isset($sztuki['sprzedane'])&&$rez['sprzedane']/$sztuki['sprzedane']>=0.5) $err=1;
                        echo '<tr'.($err==1? ' class="error" ':'').'><td>'.$rez['nazwa_towaru'].'</td><td>'.$rez['sprzedane'].'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
                <h3>Zwroty</h3>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                    <label>kwartał i rok-puste dla całego okresu</label>
                    <input type="text" placeholder="0-0000"  pattern="[1-4]-\d{4}" name="date" value="<?=isset($date)?$date:''?>" >
                    <label>Produkt - puste da 10 najczęściej wracających</label>
                    <input type="text"   name="zwrot">
                    <br>
                    <input type="submit" class="tabele" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>
</body>
</html>