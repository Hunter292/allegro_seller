<!doctype html>
<?php
    if(isset($_POST['login'])){
        session_start();
        if($_POST['login']=="Moringmark"&&$_POST['pass']=="Lord and Saviour"){
            $_SESSION['logged']=true;
            if(isset($_SESSION['redir'])){
                header('Location: '.$_SESSION['redir']);
                unset($_SESSION['redir']);
                exit();  
            } else{
                header('Location: zam-dodaj.php');
                exit();
            }
        }
        //How it would work in application for many people
        /*$login=htmlspecialchars($_POST['login']);
        $pass=htmlspecialchars($_POST['pass']);
        require('connect.php');
        $query=$polaczenie->prepare("SELECT pass from uzytkownicy where login=:login");
        $query->bindValue(':login',$login,PDO::PARAM_STR);
        $query->execute();
        $user=$query->fetch();
        if($user &&password_verify($pass,$user['pass'])){
            if(isset($_SESSION['redir'])){
                header('Location: '.$_SESSION['redir']);
                unset($_SESSION['redir']);
                exit();  
            } else{
                header('Location: zam-dodaj.php');
                exit();
            }
        }*/
        $name=$_POST['login'];
    }
?>
<head>
	<meta charset="utf-8">
	<meta name="autor" content="Kacper Ćwiek">
	<meta name="keyword" content="zestawienie, fiskus, allegro">
	<meta name="description" content="prowadzenie zestawień dla skarbówki">
	<title> Biznes </title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="script.js"></script>

    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>
<body onload="zegar()">
<div id="container">
        <header>
            <h1>Logowanie</h1>
            <div id="zegar"></div>
        </header>
        <main>
            <article>
                <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
                    <label>Login</label>
                    <input type="text" name="login" value="<?= isset($name)? $name:""?>" >

                    <label>Hasło</label>
                    <input type="password"  name="pass">
                    <br>
                    <input type="submit" value="Dalej!">
                    <?= isset($error) ? $error:''?>
                </form>
            </article>
        </main>

    </div>


</body>