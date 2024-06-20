<?php
require('check_log.php');

if(isset($_POST['date'])){
    if(!isset($_POST['operator'])) $error='<p class="error">Prosze określić operatora</p>';
    if($_GET['tryb']=="zwrot-rabat"&&!isset($_POST['typ'])) $error='<p class="error">Prosze określić rodzaj</p>';
    $przesylka=$_POST['przesylka'];
    $przesylka=str_replace(',','.',$przesylka);
    if(!preg_match('/^\d+(\.\d{2})?$/',$przesylka)) $error='<p class="error">Niepoprawny koszt przesylki</p>';
    if(!isset($error)){
        $_SESSION['date']=$_POST['date'];
        $_SESSION['operator']=$_POST['operator'];
        $_SESSION['liczba']=$_POST['liczba'];
        $_SESSION['nick']=$_POST['nick'];
        $_SESSION['przesylka']=$przesylka;
        if(isset($_POST['nie']))$_SESSION['nie']=$_POST['nie'];
        if(isset($_POST['typ']))$_SESSION['typ']=$_POST['typ'];
        header('Location: zam-produkt.php?tryb='.$_GET['tryb']);
        exit();
    }else{
        $date=$_POST['date'];
        if(isset($_POST['operator']))$operator=$_POST['operator'];
        $liczba=$_POST['liczba'];
        $nick=$_POST['nick'];
    }
}
if($_GET['tryb']=="nieoplacone"){
    require "connect.php";
    try{
        $query=$polaczenie->query("SELECT data,nick,operator,przesylka, count(id_prod) as liczba from zamówienie join kupujacy using(id_kup) join sprzedane_produkty produkty using(id_zamowienia)
        where id_zamowienia=".$_GET['id_zamowienia']." group by id_zamowienia");
        $rezultat=$query->fetch();
        $date=$rezultat['data'];
        $operator=$rezultat['operator'];
        $nick=$rezultat['nick'];
        $przesylka=$rezultat['przesylka'];
        $liczba=$rezultat['liczba'];
        $_SESSION['id_zamowienia']=$_GET['id_zamowienia'];
        $_SESSION['orginalna_liczba']=$rezultat['liczba'];
    }catch(PDOException $e){echo "Coś poszło nie tak, proszę spróbować później";}
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dodaj Zamowienie</title>
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
                <form method="post" action=<?='"zam-dodaj.php?tryb='.$_GET['tryb'].'"'?>>
                    <label>Data</label>
                    <input type="date" name="date" max="<?php echo date("Y-m-d") ?>"value="<?= isset($date)? $date:date("Y-m-d")?>" >

                    <label>Przesyłka</label>
                    <input type="text" name="przesylka" value="<?= isset($przesylka)? $przesylka:''?>" required>

                    <p>Operator</p>
                    <input type="radio" name="operator" value="Pu" id="pu" <?php if(isset($operator)&& $operator=='Pu') echo 'checked' ?>> <label style="display:inline;" for="pu">Pu</label>
                    <input type="radio" name="operator" value="P24" id="p24" <?php if(isset($operator)&& $operator=='P24') echo 'checked' ?>><label for="p24" style="display:inline;">P24</label>
                    <input type="radio" name="operator" value="kup" id="kup" <?php if(isset($operator)&& $operator=='kup') echo 'checked' ?>><label for="kup" style="display:inline;">kup</label>


                    <label>Liczba produktów</label>
                    <input type="number" name="liczba" min="1" required value="<?= isset($liczba)? $liczba:''?>">

                    <label>Nick kupujacego</label>
                    <input type="text" name="nick" required <?= $_GET['tryb']=="nieoplacone"? "readonly":""?> value="<?= isset($nick)? $nick:''?>">

                    <?php if($_GET['tryb']=="zwrot-rabat"){ ?>
                    <p>Rodzaj</p>
                    <input type="radio" name="typ" value="Zwrot-" id="zwrot" <?php if(isset($_POST['typ'])&& $_POST['typ']=='Zwrot-') echo 'checked' ?>> <label style="display:inline;" for="zwrot">Zwrot</label>
                    <input type="radio" name="typ" value="Rabat-" id="rabat" <?php if(isset($_POST['typ'])&& $_POST['typ']=='Rabat-') echo 'checked' ?>><label for="rabat" style="display:inline;">Rabat</label>
                    <?php } if($_GET['tryb']=="zamowienie"){ ?>
                    <br/>
                    <input type="checkbox" name="nie" value="nie" id="nie"<?= isset($_POST['nie'])? 'checked':'' ?>><label for="nie" style="display:inline;" style="display:inline;">Nieoplacone</label>
                   <?php }?>
                    <br>
                    <input type="submit" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>
</body>
</html>