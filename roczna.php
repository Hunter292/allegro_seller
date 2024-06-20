<?php
require('check_log.php');

if(isset($_POST['date'])){
    $date=$_POST['date'];
    require_once 'connect.php';
    $dane=array("1, 2, 3","4, 5, 6","7, 8, 9","10, 11, 12");
    $kwoty=array();
    $suma=0;
    for($i=0;$i<4;$i++){
        $kwartal=$dane[$i];
        $query=$polaczenie->query("SELECT sum(kwota+przesylka) as suma from zamówienie where przelew=1 and year(data)=$date and month(data) in($kwartal)");
        $rez=$query->fetch();
        if($rez['suma']!=0){
            $suma+=$rez['suma'];
            array_push($kwoty,str_replace('.',',',$rez['suma']));
        }
        else array_push($kwoty,'0,00');
    }
    if(preg_match('/\.\d$/',$suma))$suma.='0';
    if(!preg_match('/\.\d{2}/',$suma))$suma.=',00';
    $suma=str_replace('.',',',$suma);

}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zestawienie roczne</title>
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
            <h1>Zestawienie roczne</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <?php if(isset($date)){?>
            <div>
                <table class="zestawienie" border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr><th></th><th>ROK</th><th><?php echo $date ?></th><th colspan="6">EWIDENCJA PRZYCHODÓW - ZESTAWIENIE ZBIORCZE</th><th colspan="7"></th></tr>

                        <tr><th>LP</th><th>DATA WPISU</th><th>DATA UZYSKANIA PRZYCHODU</th><th>NR DOWODU NA PODST. KT. DOKONANO WPISU</th>
                        <th colspan="10">KWOTA PRZYCHODU OPODATKOWANA WEDŁUG STAWKI</th><th><div style="width:150px">OGÓŁEM PRZYCHODY /5+6+7+8+9+10+11+12+13+14/</div></th><th>UWAGI</th></tr>

                        <tr><th colspan="4"></th><th>17,00%</th><th>15,00%</th><th>14,00%</th><th>12,50%</th><th>12,00%</th><th>10,00%</th><th>8,50%</th><th>5,50%</th><th>2,00%</th><th>3,00%</th><th colspan="2"></th></tr>
                        
                        <tr><th colspan="4"></th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th><th>ZŁ,GR</th>
                        <th>ZŁ,GR</th><th></th></tr>

                        <tr><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th>
                        <th>11</th><th>12</th><th>13</th><th>14</th><th>15</th><th>16</th></tr>
                    </thead>
                    <tbody>
                        <tr><th>1</th><th><?php echo $date ?>-04-01</th><th>1 Q <?php echo $date ?></th><th>EWIDENCJA KWARTALNA 1Q<?php echo $date ?></th><th>0.00</th><th>0.00</th><th>0.00</th>
                        <th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th><?php echo $kwoty[0] ?></th><th><?php echo $kwoty[0] ?></th><th></th></tr>

                        <tr><th>2</th><th><?php echo $date ?>-07-01</th><th>2 Q <?php echo $date ?></th><th>EWIDENCJA KWARTALNA 2Q<?php echo $date ?></th><th>0.00</th><th>0.00</th><th>0.00</th>
                        <th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th><?php echo $kwoty[1] ?></th><th><?php echo $kwoty[1] ?></th><th></th></tr>

                        <tr><th>3</th><th><?php echo $date ?>-10-01</th><th>3 Q <?php echo $date ?></th><th>EWIDENCJA KWARTALNA 3Q<?php echo $date ?></th><th>0.00</th><th>0.00</th><th>0.00</th>
                        <th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th><?php echo $kwoty[2] ?></th><th><?php echo $kwoty[2] ?></th><th></th></tr>

                        <tr><th>4</th><th><?php echo $date+1 ?>-01-01</th><th>4 Q <?php echo $date ?></th><th>EWIDENCJA KWARTALNA 4Q<?php echo $date ?></th><th>0.00</th><th>0.00</th><th>0.00</th>
                        <th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th>0.00</th><th><?php echo $kwoty[3] ?></th><th><?php echo $kwoty[3] ?></th><th></th></tr>

                        <tr><th colspan="4">PODSUMOWANIE STRONY</th><th colspan="9"></th><th><?php echo $suma ?></th><th><?php echo $suma ?></th><th></th></tr>
                        <tr><th colspan="4">PRZENIESIENIE Z POPRZEDNIEJ STRONY</th><th colspan="9"></th><th></th><th></th><th></th></tr>
                        <tr><th colspan="4">SUMA PRZYCHODÓW OD POCZĄTKU ROKU</th><th colspan="9"></th><th></th><th><?php echo $suma ?></th><th></th></tr>


                    </tbody>
                </table>
                <br/>
            </div>
            <?php } ?>
            <article>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
                    <label>Rok</label>
                    <input type="text" required  pattern="\d{4}" name="date" value="<?=isset($date)?$date:''?>" >

                    <br>
                    <input type="submit" class="tabele" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>
</body>
</html>