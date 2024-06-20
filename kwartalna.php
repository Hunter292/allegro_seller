<?php
require('check_log.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zestawienie kwartalne</title>
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
    <div id="container" style="width:100%">
        <header>
            <h1>Zestawienie kwartalne</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <div>
                <table class="zestawienie" id="zestawienie" border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr><th>LP</th><th>DATA WPISU</th><th>DATA UZYSKANIA PRZYCHODU</th><th colspan="4">NR DOWODU NA PODST. KT. DOKONANO WPISU</th>
                        <th colspan="10">KWOTA PRZYCHODU OPODATKOWANA WEDŁUG STAWKI</th><th>OGÓŁEM PRZYCHODY /5+6+7+8+9+ 10+11+12+13+14/</th><th>UWAGI WPLATA: ALLEGRO CZY KLIENT</th>
                        <th>KOSZT PRZESYŁEK wg allegro</th><th>CENA PRZEDMIOTU</th></tr>
                        <tr><th colspan="3"></th><th><div style="width:150px" >NAZWA TOWARU</div></th><th><div style="width:150px">NAZWA KUPUJACEGO</div></th><th>NICK KUPUJĄCEGO</th><th>NAZWA PLIKU OPERATORA</th>
                        <th>17,00%</th><th>15,00%</th><th>14,00%</th><th>12,50%</th><th>12,00%</th><th>10,00%</th><th>8,50%</th><th>5,50%</th><th>2,00%</th><th><div style="width:50px">3,00%</div></th><th colspan="4"></th></tr>
                        <tr><th colspan="7"></th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th>
                        <th colspan="4"></th></tr>
                        <tr><th>1</th><th>2</th><th>3</th><th>4a</th><th>4b</th><th>4c</th><th>4d</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th>
                        <th>11</th><th>12</th><th>13</th><th>14</th><th>15</th><th>16</th><th></th><th></th></tr>
                    </thead>
                    <?php 
                    require_once 'connect.php';
                    try{
                    if(isset($_POST['date'])){
                        $date=$_POST['date'];
                        $dane=explode('-',$date);
                        if($dane[0]==1) $dane[0]="1, 2, 3";
                        if($dane[0]==2) $dane[0]="4, 5, 6";
                        if($dane[0]==3) $dane[0]="7, 8, 9";
                        if($dane[0]==4) $dane[0]="10, 11, 12";
            
                        $zapytanie="SELECT id_zamowienia, data, operator,kwota,przesylka,platforma,nick,kup_dane from zamówienie inner join kupujacy using(id_kup)
                        where MONTH(data) IN($dane[0]) and przelew=1 and YEAR(data)=$dane[1] order by data ";
                        if(isset($_POST['limit'])&& !empty($_POST['limit'])) $zapytanie.= "desc limit ".$_POST['limit'];
                        else  $zapytanie.="asc";

                        $query=$polaczenie->query($zapytanie);
                        $rezultaty=$query->fetchall();
                        $i=1;
                        $suma_sum=0;
                        $suma_przesylek=0;
                        $suma_produktow=0;

                        foreach($rezultaty as $rez){
                            $query=$polaczenie->query("SELECT nazwa_towaru,nr_oferty,sztuki,cena from sprzedane_produkty where id_zamowienia=".$rez['id_zamowienia']);
                            $wyniki=$query->fetchall();
                            $dane_towaru='';
                            foreach($wyniki as $wynik){
                                $dane_towaru.=$wynik['nazwa_towaru'].' '.$wynik['cena'].'zł nr oferty:'.$wynik['nr_oferty'].' '.$wynik['sztuki']."szt.<br/>";
                            }
                            $suma=$rez['kwota']+$rez['przesylka'];
                            $suma_sum+=$suma;
                            $suma_przesylek+=$rez['przesylka'];
                            if(preg_match('/\.\d$/',$suma))$suma.='0';
                            if(!preg_match('/\.\d{2}$/',$suma))$suma.=',00';
                            $suma=str_replace('.',',',$suma);

                            $kup=$rez['kup_dane'];
                            if($rez['operator']=='kup')$operator='Wyciąg bankowy';
                           else $operator=str_replace('-','_',$rez['data']).'_'.$rez['operator'];

                            echo"<tr>";
                            echo "<td>{$i}</td><td>".$rez['data']."</td><td>".$rez['data']."</td><td>".$dane_towaru."</td><td>".$kup."</td><td>".$rez['nick']."</td>
                            <td>".$operator."</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>$suma</td><td>$suma</td>
                            <td>".$rez['platforma']."</td><td>".str_replace('.',',',$rez['przesylka'])."</td><td>".str_replace('.',',',$rez['kwota'])."</td>";
                            echo'</tr>';
                            $i++;
                        }
                        $suma_produktow=$suma_sum-$suma_przesylek;
                        if($suma_sum>=10000) $suma_sum=substr($suma_sum,0,2).' '.substr($suma_sum,2);
                        else if($suma_sum>=1000) $suma_sum=substr($suma_sum,0,1).' '.substr($suma_sum,1);
                        if(preg_match('/\.\d$/',$suma_sum))$suma_sum.='0';
                        if(preg_match('/\.\d$/',$suma_przesylek))$suma_przesylek.='0';
                        if(preg_match('/\.\d$/',$suma_produktow))$suma_produktow.='0';

                        if(!preg_match('/\.\d{2}$/',$suma_sum))$suma_sum.=',00';
                        if(!preg_match('/\.\d{2}$/',$suma_przesylek))$suma_przesylek.=',00';
                        if(!preg_match('/\.\d{2}$/',$suma_produktow))$suma_produktow.=',00';
                        $suma_sum=str_replace('.',',',$suma_sum);
                        $suma_przesylek=str_replace('.',',',$suma_przesylek);
                        $suma_produktow=str_replace('.',',',$suma_produktow);

                        echo '<tr><td colspan="16">Podsumowanie</td><td>'.$suma_sum.'</td><td>'.$suma_sum.'</td><td></td><td>'.$suma_przesylek.'</td><td>'.$suma_produktow.'</td>';
                        echo'</tr>';
                    }
                    $miesiac=date("m");
                    $rok=date("Y");
                    if($miesiac==1){ $miesiac=12; $rok--;}
                    else $miesiac--;

                    $query=$polaczenie->query("SELECT sum(kwota + przesylka) as suma from zamówienie where przelew=1 and year(data)=$rok and month(data)=$miesiac");
                    $rezultat=$query->fetch();
                    $przychod_m=$rezultat['suma'];
                    if(!preg_match('/\.\d{2}$/',$przychod_m))$przychod_m.=',00';
                    $przychod_m=str_replace('.',',',$przychod_m);

                    $query=$polaczenie->query("SELECT sum(kwota + przesylka) as suma from zamówienie where przelew=1 and year(data)=$rok and month(data)<=$miesiac");
                    $rezultat=$query->fetch();
                    $przychod_k=$rezultat['suma'];
                    if(!preg_match('/\.\d{2}$/',$przychod_k))$przychod_k.=',00';
                    $przychod_k=str_replace('.',',',$przychod_k);
                    } catch(PDOException $e){ echo "Coś poszło nie tak, proszę spróbować później";}
                    ?>
                </table>
                <br/>
            </div>
            <article>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr><td>Pzechód z poprzedniego miesiaca</td><td><?php echo $przychod_m ?></td></tr>
                    <tr><td>Pzechód kumulatywny do obecnego miesiaca</td><td><?php echo $przychod_k ?></td></tr>

                </table>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                    <label>kwartał i rok</label>
                    <input type="text" placeholder="0-0000"  pattern="[1-4]-\d{4}" name="date" value="<?=isset($date)?$date:''?>" >
                    <label>limit pozycji</label>
                    <input type="number"   name="limit">
                    <br>
                    <input type="submit" class="tabele" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>
</body>
</html>