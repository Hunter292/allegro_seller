function zegar(){
    var dzisiaj=new Date();
    var dzien=dzisiaj.getDate();
    var miesiac=dzisiaj.getMonth()+1;
    var rok=dzisiaj.getFullYear();
    var godzina=dzisiaj.getHours();
    if(godzina<10) godzina="0"+godzina;
    var minuta=dzisiaj.getMinutes();
    if(minuta<10) minuta="0"+minuta;
    var sekunda=dzisiaj.getSeconds();
    if(sekunda<10) sekunda="0"+sekunda;
    document.getElementById("zegar").innerHTML=dzien+"/"+miesiac+"/"+rok+" | "+godzina+":"+minuta+":"+sekunda;
    setTimeout("zegar()",1000);
}
var licznik=Math.floor(Math.random()*13+1)
function zanikanie(){
    $("#slider").fadeOut(500);
}
function zmiana_slajdu(){
    var slajd="<img src=\"images/IMG_"+licznik+".jpg\">";
    document.getElementById("slider").innerHTML=slajd;
    $("#slider").fadeIn(500);
    licznik++;
    if(licznik>13) licznik=1;
    setTimeout("zmiana_slajdu()",7000);
    setTimeout("zanikanie()",6500);
}