<?php
require('check_log.php');

if(!isset($_SESSION['date'])){
    header('Location: zam-dodaj.php');
    exit();
}
require_once 'connect.php';
if(isset($_POST['nazwa0'])){
    $check=true;
    isset($_SESSION['nie'])? $oplacone=0:$oplacone=1;
    try{
        $parametry= array($_SESSION['liczba']);
        for($i=0;$i<$_SESSION['liczba'];$i++){
            $parametry[$i]=$_POST['nazwa'.$i];
            $parametry[$i]=str_replace(',','.',$parametry[$i]);
            if(!preg_match('/\d+x \d+\.\d{2}/',$parametry[$i])){
                $check=false;
                $error="<p class=error>Produkt ".($i+1)." jest blednie wprowadzony</p>";
                break;
            }
        }
        if($check){
            if(!isset($_SESSION['kup-dane'])||$_POST['kupujacy']!=$_SESSION['kup-dane']){
                $query=$polaczenie->query("INSERT INTO kupujacy values(null,'".$_SESSION['nick']."','".$_POST['kupujacy']."')");
                $query=$polaczenie->query("SELECT id_kup from kupujacy where nick='".$_SESSION['nick']."' and kup_dane='".$_POST['kupujacy']."' order by id_kup desc limit 1");
                $rezultat=$query->fetch();
                $_SESSION['id-kup']=$rezultat['id_kup'];
            }
            if($_SESSION['operator']=='kup'){$przelew=1;$platforma="kup";}
            else {$przelew=0; $platforma="all";}
            $suma=0;
            if($_GET['tryb']!="nieoplacone"){
                $query=$polaczenie->query("INSERT INTO zamówienie values(null,'".$_SESSION['date']."','".$_SESSION['id-kup'].
                "','".$_SESSION['operator']."',0,'".$_SESSION['przesylka']."','$platforma','$przelew',$oplacone)");

                $query=$polaczenie->query("SELECT id_zamowienia from zamówienie where id_kup='".$_SESSION['id-kup']."' order by id_zamowienia desc limit 1");
                $rezultat=$query->fetch();
                $id=$rezultat['id_zamowienia'];
            } else $id=$_SESSION['id_zamowienia'];
            for($i=0;$i<$_SESSION['liczba'];$i++){
                $dane=preg_split('/\d+x \d+\.\d{2}/',$parametry[$i]);
                $parametry[$i]=str_replace($dane[0],'',$parametry[$i]);
                $parametry[$i]=str_replace($dane[1],'',$parametry[$i]);
                array_push($dane,preg_replace('/\d+x/','',$parametry[$i]));
                array_push($dane,str_replace('x'.$dane[2],'',$parametry[$i]));
                $dane[1]=str_replace('zł nr:','',$dane[1]);

                $dane[0]=trim($dane[0]);
                $dane[1]=trim($dane[1]);
                $dane[2]=trim($dane[2]);
                $dane[3]=trim($dane[3]);
                if(isset($_SESSION['typ'])){
                    $dane[0]=$_SESSION['typ'].$dane[0];
                    $dane[2]=0-$dane[2];
                }
                $suma+=$dane[2]*$dane[3];
                if($_GET['tryb']!="nieoplacone"||(isset($_SESSION['orginalna_liczba'])&&$_SESSION['orginalna_liczba']<=$i))
                $query=$polaczenie->query("INSERT INTO sprzedane_produkty values(null,'$dane[0]','$dane[1]','$dane[2]','$dane[3]','$id')");
                else $query=$polaczenie->query("UPDATE sprzedane_produkty set nazwa_towaru='$dane[0]', nr_oferty='$dane[1]', cena='$dane[2]', sztuki='$dane[3]' where id_prod=".$_SESSION['id_prod'.$i]);
            }
            if($_GET['tryb']!="nieoplacone")$query=$polaczenie->query("UPDATE zamówienie set kwota='$suma' where id_zamowienia='$id'");
            else{
                $query=$polaczenie->query("UPDATE zamówienie set data='".$_SESSION['date']."', id_kup='".$_SESSION['id-kup']."', operator='".$_SESSION['operator']."', kwota='$suma',platforma='$platforma',przelew='$przelew',oplacone=1 where id_zamowienia='".$_SESSION['id_zamowienia']."'");
                if($_SESSION['orginalna_liczba']>$_SESSION['liczba']) for($i=$_SESSION['orginalna_liczba']-1;$i>=$_SESSION['liczba'];$i--){
                $query=$polaczenie->query("DELETE from sprzedane_produkty where id_prod=".$_SESSION['id_prod'.$i]);}
            } 
        }
    }
    catch(PDOException $e){
        echo "Coś poszło nie tak, proszę spróbować później";
    } 
    if($check){
        session_destroy();
        if($_GET['tryb'!='nieoplacone'])header('Location: zam-dodaj.php?tryb='.$_GET['tryb']);
        else header('Location: nieoplacone-oplac.php');
        exit();
    }
    
}
if(isset($_POST['zmien'])) $_SESSION['liczba']=$_POST['zmien'];
if($_GET['tryb']=="nieoplacone"){
    try{ 
        $query=$polaczenie->query("SELECT * from sprzedane_produkty where id_zamowienia='".$_SESSION['id_zamowienia']."'");
        $rezultat=$query->fetchall();
        $parametry=array();
        $id_produktow=array();
        $i=0;
        foreach($rezultat as $rez){
            $produkt=$rez['nazwa_towaru'].' '.$rez['sztuki'].'x '.$rez['cena'].'zł nr:'.$rez['nr_oferty'];
            array_push($parametry,$produkt);
            $_SESSION['id_prod'.$i]=$rez['id_prod'];
            $i++;
        }
    }catch(PDOException $e){echo "Coś poszło nie tak, proszę spróbować później";}
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zam-produkt</title>
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
        <h1>Dodawanie <?php switch($_GET['tryb']){
                case'zwrot-rabat': echo"zwrotu lub rabatu"; break;
                case'zamowienie': echo"zamówienia"; break;
                case'nieoplacone': echo"nieoplaconego";break;
            }
             ?></h1>
            <div id="zegar"></div>
        </header>
        <main>
            <article>
                <form method="post" action=<?='"zam-produkt.php?tryb='.$_GET['tryb'].'"'?>>
                    <?php
                    $query=$polaczenie->query("SELECT id_kup,kup_dane from kupujacy where nick='".$_SESSION['nick']."' ORDER BY id_kup desc limit 1" );
                    $rezultat=$query->fetch();
                    echo '<label>Podaj dane kupujacego</label>';
                    if(!$rezultat) echo '<input type="text" name="kupujacy" id="kupujacy" required>';
                    else {
                        $_SESSION['id-kup']=$rezultat['id_kup'];
                        $kup_dane=$rezultat['kup_dane'];
                        $_SESSION['kup-dane']=$kup_dane;
                        echo'<input type="text" name="kupujacy" id="kupujacy" required value="'.$kup_dane.'">';
                    }
                    for($i=0;$i<$_SESSION['liczba'];$i++) echo'<div><label>Produkt '.($i+1).'</label><input class="produkt" value="'.
                    (isset($parametry[$i])? $parametry[$i]:'').'" required type="text" name="nazwa'.$i.'"></div>';
                    ?>
                    <br>
                    <input type="submit" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                    <br/>
                </form>
                <div style="margin-bottom: 150px;"></div>
                <form method="post" action=<?='"zam-produkt.php?tryb='.$_GET['tryb'].'"'?>>
                    <h3>Zmień liczbę produktów</h3>
                    <input name="zmien" type="number" required>
                    <br>
                    <input type="submit" value="Zmień!">

                </form>
            </article>
        </main>

    </div>
</body>
</html>