<?php
require('check_log.php');

if(isset($_POST['date'])){
    if(!isset($_POST['operator'])) $error='<p class=error>Prosze określić operatora</p>';
    else $operator=$_POST['operator'];
    $liczba=$_POST['liczba'];
    $kwota=$_POST['kwota'];
    $date=$_POST['date'];
    if(!isset($error)){
        require_once 'connect.php';
        try{
            $query=$polaczenie->query("SELECT id_zamowienia,(kwota+przesylka) as suma from zamówienie where operator='".$operator."'and przelew=0 order by id_zamowienia asc limit ".$liczba);
            $rezultaty=$query->fetchall();
            $suma=0;
            $ids='';
            foreach($rezultaty as $rezultat){
                $suma+=$rezultat['suma'];
                $ids.=$rezultat['id_zamowienia'].', ';
            }
            $ids.='0';
            $suma=round($suma,2,PHP_ROUND_HALF_UP);
            if($suma!=$kwota) $error="<p class=error>Kwota się nie zgadza, upewnij się, że wpisałaś poprawną kwotę i operatora. Może brakować zamówienia w systemie</p>";
            else $query=$polaczenie->query("UPDATE zamówienie set przelew=1, data='".$date."' where id_zamowienia IN(".$ids.")");
        }
        catch(PDOException $e){
            echo "Coś poszło nie tak";
        }
        unset($liczba);
        unset($operator);

    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dodaj Przelew</title>
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
            <h1>Dodawanie przelewu</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <article>
                <form method="post" action="przelew.php">
                    <label>Data</label>
                    <input type="date" name="date" max="<?php echo date("Y-m-d") ?>"value="<?= isset($data)? $data:date("Y-m-d")?>" >

                    <p>Operator</p>
                    <input type="radio" name="operator" value="PayU" id="pu" <?php if(isset($operator)&& $operator=='PayU') echo 'checked' ?>> <label style="display:inline;" for="pu">PayU</label>
                    <input type="radio" name="operator" value="P24" id="p24" <?php if(isset($operator)&& $operator=='P24') echo 'checked' ?>><label for="p24" style="display:inline;">P24</label>

                    <label>Liczba tranzakcji</label>
                    <input type="number"  name="liczba" min="1" required value="<?= isset($liczba)? $liczba:''?>">

                    <label>Kwota</label>
                    <input type="number" step="0.01" name="kwota" min="1" required value="<?= isset($kwota)? $kwota:''?>">
                    <br>
                    <input type="submit" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>
</body>
</html>