<?php
ob_start();
session_start();
include("fonksiyon/fonksiyon.php");
@$tableid = $_GET["tableid"];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="dosya/jqu.js"></script>
    <link rel="stylesheet" href="dosya/boost.css">
    <link rel="stylesheet" href="dosya/stil.css">
    <link rel="stylesheet" href="dosya/tema2.css">

    <script>
        $(document).ready(function () {

            $('#iskontoform').hide();
            $('#parcaform').hide();
            $('#degistirform').hide();
            $('#birlestirform').hide();

            $('#degistir a').click(function () {

                $('#birlestirform').slideUp();
                $('#degistirform').slideDown();
            });

            $('#birlestir a').click(function () {

                $('#degistirform').slideUp();
                $('#birlestirform').slideDown();
            });

            $('#hesapalbtn').click(function () {
                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=hesap',
                    data: $('#hesapalform').serialize(),
                    success: function (donen_veri) {
                        $('#hesapalform').trigger("reset");
                        window.location.reload();
                    }

                });
            });

            $('#degistirbtn').click(function () {
                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=masaislem',
                    data: $('#degistirformveri').serialize(),
                    success: function (donen_veri) {
                        $('#degistirformveri').trigger("reset");
                        window.location.reload();
                    }

                });
            });

            $('#birlestirbtn').click(function () {
                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=masaislem',
                    data: $('#birlestirformveri').serialize(),
                    success: function (donen_veri) {
                        $('#birlestirformveri').trigger("reset");
                        window.location.reload();
                    }

                });
            });

            $('#yakala a').click(function () {
                var sectionId = $(this).attr('sectionId');
                var sectionId2 = $(this).attr('sectionId2');
                $.post("islemler.php?islem=sil", {"urunid": sectionId, "tableid": sectionId2}, function (post_veri) {
                    window.location.reload();
                });
            });


            $('#bildirimlink a').click(function () {
                var sectionId = $(this).attr('sectionId');
                $.post("islemler.php?islem=hazirurunsil", {"id": sectionId}, function () {

                    $('#uy' + sectionId).hide();
                    $("#bekleyenler").load("islemler.php?islem=garsonbilgigetir");

                });
            });

            $('#iskontoAc a').click(function () {
                $('#iskontoform').toggle();
            });
            $('#iskontobtn ').click(function () {

                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=iskontoUygula',
                    data: $('#iskontoForm').serialize(),
                    success: function (donen_veri) {
                        $('#iskontoForm').trigger("reset");
                        window.location.reload();
                    },
                })
            });

            $('#parcaHesapAc a').click(function () {


                $('#parcaform').toggle();


            });
            $('#parcabtn').click(function () {

                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=parcaHesapOde',
                    data: $('#parcaForm').serialize(),
                    success: function (donen_veri) {
                        $('#parcaForm').trigger("reset");
                        window.location.reload();
                    },
                })
            });
        });

        var popupWindow = null;

        function ortasayfa(url, winName, w, h, scroll) {
            LeftPosition = (screen.width) ? (screen.width - w) / 2 : 0;
            TopPosition = (screen.height) ? (screen.height - h) / 2 : 0;
            settings = 'height=' + h + ', width=' + w + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=' + scroll + ', resizable';
            popupWindow = window.open(url, winName, settings)
        }
    </script>
</head>


<?php

function myconn($vt, $sql, $option) {
    $a = $sql;
    $b = $vt->prepare($a);
    $b->execute();
    if ($option == 1):
        return $c = $b->get_result();
    endif;
}

function sifrele($veri){
    return base64_encode(gzdeflate(gzcompress(serialize($veri))));
}

function coz($veri){
    return unserialize(gzuncompress(gzinflate(base64_decode($veri))));
}

function uyari($mesaj, $color) {
    echo '<div class="alert alert-' . $color . ' mt-4 text-center">' . $mesaj . '</div>';
}

function formgetir($tableid, $db, $baslik, $durum, $btnvalue, $btnid, $formvalue) {
    echo '<div class="card border-success m-3" style="max-width:18rem;">
            <div class="card-header">' . $baslik . '</div><div class="card-body text-success">
                <form id = "' . $formvalue . '">

                        <input type="hidden" name="mevcuttableid" value="' . $tableid . '"/>
                            <select name="hedefmasa" class="form-control">';

    $masadeg = myconn($db, "select * from masalar where durum=$durum and rezervedurum=0", 1);
    while ($son = $masadeg->fetch_assoc()):
        if ($tableid != $son["id"]):
            echo '<option value="' . $son["id"] . '">' . $son["ad"] . '</option>';
        endif;
    endwhile;
    echo '</select><input type="button" id="' . $btnid . '" value="' . $btnvalue . '" class="btn btn-success btn-block mt-2"></form></div></div>';
}

function garsonbilgi($db) {
    $siparisler = myconn($db, "select * from mutfaksiparis where durum = 1 order by tableid desc", 1);
    echo '<div class="col-md-12" id="bildirimlink">';
    while ($geldiler = $siparisler->fetch_assoc()) :
        $tableid = $geldiler["tableid"];
        $tablename = myconn($db, "select * from masalar where id=$tableid", 1);
        $masabilgi = $tablename->fetch_assoc();
        echo '<div class="alert alert-success" id="uy' . $geldiler["id"] . '">Masa : <strong>' . $masabilgi["ad"] . '</strong> | Ürün : <strong>' . $geldiler["urunad"] . '</strong> | Adet : <strong>' . $geldiler["adet"] . '</strong>
	<a sectionId="' . $geldiler["id"] . '" class="fas fa-check float-right m-1 text-danger" style="font-size:20px;"></a></div>';
    endwhile;
    echo '</div>';
}

function iskontogetir($tableid) {
    echo '<div class="card border-success m-3" style="max-width:18rem;">
	<div class="card-header">İSKONTO UYGULA</div><div class="card-body text-success">
	<form id="iskontoForm"> 
    <input type="hidden" name="tableid" value="' . $tableid . '" />
    <select name="iskontoOran" class="form-control">
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="20">20</option>
    <option value="25">25</option>
    </select> <input type="button" id="iskontobtn" value="UYGULA"  class="btn btn-success btn-block mt-2" /> </form></div></div>';
}

function parcagetir($tableid) {
    echo '<div class="card border-success m-3 text-center" style="max-width:18rem;">
	<div class="card-header">PARÇA HESAP AL</div><div class="card-body text-success">
	<form id="parcaForm"> 
	<input type="hidden" name="tableid" value="' . $tableid . '" />
	<input type="text" name="tutar"  />
	<input type="button" id="parcabtn" value="ÖDE"  class="btn btn-success btn-block mt-2" /> </form></div></div>';
}

$islem = htmlspecialchars($_GET["islem"]);

switch ($islem) :

    case "parcaHesapOde":

        $tutar = $_POST["tutar"];
        $tableid = $_POST["tableid"];

        if (!empty($tutar)) :


            $verilericek = myconn($db, "select * from masabakiye where tableid=$tableid", 1);

            if ($verilericek->num_rows == 0) :
                //insert
                myconn($db, "insert into masabakiye (tableid,tutar) VALUES($tableid,$tutar)", 1);

            else:
                $mevcutdeger = $verilericek->fetch_assoc();
                $sontutar = $mevcutdeger["tutar"] + $tutar;
                myconn($db, "update masabakiye set tutar=$sontutar where tableid=$tableid", 1);


            endif;

        endif;


        break;

    case "iskontoUygula":


        $iskontoOran = $_POST["iskontoOran"];
        $tableid = $_POST["tableid"];


        $verilericek = myconn($db, "select * from anliksiparis where tableid=$tableid", 1);

        while ($don = $verilericek->fetch_assoc()):
            $urunid = $don["urunid"];
            $urunhesap = ($don["urunfiyat"] / 100) * $iskontoOran; // 0.50
            $sonfiyat = $don["urunfiyat"] - $urunhesap;     // 4.50


            myconn($db, "update anliksiparis set urunfiyat=$sonfiyat where urunid=$urunid", 1);


        endwhile;


        break;

    case "masaislem":

        $mevcuttableid = $_POST["mevcuttableid"];
        $hedefmasa = $_POST["hedefmasa"];
        $bakiyesifirla = myconn($db, "select * from  masabakiye where tableid=$mevcuttableid", 1);
        $hedefmasabak = myconn($db, "select * from  masabakiye where tableid=$hedefmasa", 1);
        if ($bakiyesifirla->num_rows != 0):
            $masaninBakiyesi = $bakiyesifirla->fetch_assoc();

            $odenenTutar = $masaninBakiyesi["tutar"];

            if ($hedefmasabak->num_rows != 0):
                $HedefMasaBakiyesi = $hedefmasabak->fetch_assoc();
                $gunceltutar = $odenenTutar + $HedefMasaBakiyesi["tutar"];
                myconn($db, "update masabakiye set tutar=$gunceltutar where tableid=$hedefmasa", 1);
                myconn($db, "delete from masabakiye where tableid=$mevcuttableid", 1);
            else:
                myconn($db, "update masabakiye set tableid=$hedefmasa where tableid=$mevcuttableid", 1);
            endif;


        endif;


        myconn($db, "update anliksiparis set tableid=$hedefmasa where tableid=$mevcuttableid", 1);

        // Masanın durumunu güncelleyeceğim
        $ekleson2 = $db->prepare("update masalar set durum=0 where id=$mevcuttableid");
        $ekleson2->execute();

        // Masanın durumunu güncelleyeceğim
        $ekleson2 = $db->prepare("update masalar set durum=1 where id=$hedefmasa");
        $ekleson2->execute();

        break;

    case "hesap":

        if (!$_POST):

            echo "Postdan Gelmiyorsun";

        else:

            $tableid = htmlspecialchars($_POST["tableid"]);

            $verilericek = myconn($db, "select * from anliksiparis where tableid = $tableid", 1);

            while ($dongu = $verilericek->fetch_assoc()):
                $a = $dongu["tableid"];
                $b = $dongu["urunid"];
                $c = $dongu["urunad"];
                $d = $dongu["urunfiyat"];
                $e = $dongu["adet"];
                $f = $dongu["employeeid"];
                $bugun = date("Y-m-d");

                $raporekle = "insert into rapor (tableid, employeeid, urunid, urunad, urunfiyat, adet, tarih) values ($a, $f, $b, '$c', $d, $e, '$bugun')";
                $raporekles = $db->prepare($raporekle);
                $raporekles->execute();

            endwhile;


            $silme = $db->prepare("delete from anliksiparis where tableid = $tableid");
            $silme->execute();


            $silme2 = $db->prepare("delete from masabakiye where tableid = $tableid");
            $silme2->execute();

            // Masanın durumunu güncelleyeceğim
            $ekleson2 = $db->prepare("update masalar set durum=0 where id=$tableid");
            $ekleson2->execute();

            // Masanın log kaydı
            $ekleson23 = $db->prepare("update masalar set saat=0, dakika=0 where id=$tableid");
            $ekleson23->execute();

        endif;

        break;


    case "mutfaksip":
        if (!$_POST):
            echo "Postdan Gelmiyorsun";
        else:

            $urunid = htmlspecialchars($_POST["urunid"]);
            $tableid = htmlspecialchars($_POST["tableid"]);

            $sql2 = "update mutfaksiparis set durum = 1 where urunid = $urunid and tableid = $tableid";
            $silme2 = $db->prepare($sql2);
            $silme2->execute();
        endif;
        break;

    case "sil":
        if (!$_POST):
            echo "Postdan Gelmiyorsun";
        else:
            // Silme işlemi yapılacak
            $urunid = htmlspecialchars($_POST["urunid"]);
            $tableid = htmlspecialchars($_POST["tableid"]);

            $sql = "delete from anliksiparis where urunid = $urunid and tableid = $tableid";
            $silme = $db->prepare($sql);
            $silme->execute();

            $mutfaksorgu = "delete from mutfaksiparis where urunid = $urunid and tableid = $tableid";
            $mutfaksilme = $db->prepare($mutfaksorgu);
            $mutfaksilme->execute();
        endif;
        break;

    case "goster":
        $id = htmlspecialchars($_GET["id"]);
        $d = myconn($db, "select * from anliksiparis where tableid=$id", 1);
        $verilericek = myconn($db, "select * from masabakiye where tableid=$id", 1);
        if ($d->num_rows == 0) :
            echo '<div class="alert alert-danger mt-4 text-center">Henüz Sipariş Yok</div>';
            //uyari("Henüz Sipariş Yok", "danger");
            myconn($db, "delete from masabakiye where tableid=$id", 1);
            // Masanın durumunu güncelleyeceğim
            $ekleson2 = $db->prepare("update masalar set durum=0 where id=$id");
            $ekleson2->execute();

            // Masanın log kaydı
            $ekleson2 = $db->prepare("update masalar set saat=0, dakika=0 where id=$id");
            $ekleson2->execute();

        else:

            echo '<table class="table table-bordered table-striped text-center mt-2">
                    <thead>
                            <tr class="bg-dark text-white">
                                <th scope="col" id="hop1">Ürün Adı</th>
                                <th scope="col" id="hop2">Adet</th>
                                <th scope="col" id="hop3">Tutar</th>
                                <th scope="col" id="hop4">İşlem</th>
                            </tr>
                        </thead>
                    <tbody>';

            $adet = 0;
            $sontutar = 0;

            while ($gelenson = $d->fetch_assoc()):

                $tutar = $gelenson["adet"] * $gelenson["urunfiyat"];

                $adet += $gelenson["adet"];
                $sontutar += $tutar;

                $tableid = $gelenson["tableid"];

                echo '<tr>
                <td class="mx-auto text-center p-4">' . $gelenson["urunad"] . '</td>
                <td class="mx-auto text-center p-4">' . $gelenson["adet"] . '</td>
                <td class="mx-auto text-center p-4">' . number_format($tutar, 2, '.', ',') . '</td>
                <td id="yakala"><a class="btn btn-danger mt-2 text-white" sectionId="' . $gelenson["urunid"] . '" sectionId2="' . $tableid . '" >SİL</a></td>
                </tr>';

            endwhile;

            echo
                '<tr class="bg-dark text-white text-center">
                <td class="font-weight-bold">TOPLAM</td>
                <td class="font-weight-bold">' . $adet . '</td>
          <td class="font-weight-bold text-warning" colspan="2">';
            if ($verilericek->num_rows != 0) {
                $masaninBakiyesi = $verilericek->fetch_assoc();
                $odenenTutar = $masaninBakiyesi["tutar"];
                $kalantutar = $sontutar - $odenenTutar;
                echo "<del>" . number_format($sontutar, 2, '.', ',') . " TL</del><br>
			Ödenen : " . number_format($odenenTutar, 2, '.', ',') . "<br>
			Kalan : " . number_format($kalantutar, 2, '.', ',') . "";


            } else {
                echo number_format($sontutar, 2, '.', ',') . " TL";
            }


            echo ' </td>
            </tr>
            
            </tbody></table>
            
            <div class = "row">
            
               

                <div class = "col-md-12">

                    <form id = "hesapalform">

                        <input type="hidden" name="tableid" value="' . $tableid . '"/>
                        <button type="button" id="hesapalbtn" value="HESAP AL" style="font-weight:bold; height:40px;" class="btn btn-dark btn-block mt-2">HESAP AL</button>
                        
                    </form>
                    
                    <p><a href="fisbastir.php?tableid=' . $tableid . '" onclick="ortasayfa(this.href,\'mywindow\',\'450\',\'450\',\'yes\');return false" class="btn btn-dark btn-block mt-2">FİŞ BASTIR</a></p>

                </div>
				
				 <div class = "col-md-12">
                     <div class = "row">
                        <div class = "col-md-6" id="degistir"><a class="btn btn-warning btn-block mt-1" style="height:40px;" class="fas fa-exchange-alt mt-1">MASA DEĞİŞTİR</a></div>
                        <div class = "col-md-6" id="birlestir"><a class="btn btn-warning btn-block mt-1" style="height:40px;" class="fas fa-stream mt-1">M.BİRLEŞTİR</a></div>
                     </div>
                     
                     <div class = "row">
                        <div class = "col-md-12" id="degistirform">';
            formgetir($id, $db, "Masa Değiştir", 0, "DEĞİŞTİR", "degistirbtn", "degistirformveri");
            echo '</div>
                        <div class = "col-md-12" id="birlestirform">';
            formgetir($id, $db, "Masa Birleştir", 1, "BİRLEŞTİR", "birlestirbtn", "birlestirformveri");
            echo '</div>
                     </div> 
                </div>
				

 <div class = "col-md-12">
                     <div class = "row">
                        <div class = "col-md-6" id="iskontoAc"><a class="btn btn-warning btn-block mt-1" style="height:40px;" class="fas fa-hand-holding-use float-left mt-1ml-2">İSKONTO UYGULA</a></div>
						
                        <div class = "col-md-6" id="parcaHesapAc"><a class="btn btn-warning btn-block mt-1" style="height:40px;" class="fas fa-stream mt-1">PARÇA HESAP</a></div>
                     </div>
					 
					 
					 <div class = "row">
      <div class = "col-md-12" id="iskontoform">';
            iskontogetir($tableid);
            echo '</div>
      <div class = "col-md-12" id="parcaform">';
            parcagetir($tableid);
            echo '</div>
                       
                     </div> 
                     
                   
                </div>

				

            </div>';

        endif;

        break;

    case "ekle":

        if ($_POST) :

            @$tableid = htmlspecialchars($_POST["tableid"]);
            @$urunid = htmlspecialchars($_POST["urunid"]);
            @$adet = htmlspecialchars($_POST["adet"]);
            @$iskonto = htmlspecialchars($_POST["iskonto"]);

            if ($tableid == "" || $urunid == "" || $adet == ""):
                uyari("Ürün ve Adet Seçiniz", "danger");

            else:

                $d = myconn($db, "select * from urunler where id = $urunid", 1);
                $son = $d->fetch_assoc();
                $urunad = $son["ad"];
                $katid = $son["katid"];
                $urunfiyat = $son["fiyat"];

                $saat = date("H");
                $dakika = date("i");

                $mutfak = "select * from mutfaksiparis where urunid = $urunid and tableid = $tableid";
                $var2 = myconn($db, $mutfak, 1);

                if ($var2->num_rows != 0):

                    $urundizi = $var2->fetch_assoc();
                    $sonadet = $adet + $urundizi["adet"];
                    $islemid = $urundizi["id"];

                    $guncel = "update mutfaksiparis set adet = $sonadet where id = $islemid";
                    $guncelson = $db->prepare($guncel);
                    $guncelson->execute();

                else:
                    // Mutfağa bilgi gönderiliyor
                    $durumba = myconn($db, "select * from kategoriler where id = $katid", 1);
                    $durumbak = $durumba->fetch_assoc();

                    if ($durumbak["mutfakdurum"] == 0):
                        myconn($db, "insert into mutfaksiparis (tableid, urunid, urunad, adet, saat, dakika) values ($tableid, $urunid, '$urunad', $adet, $saat, $dakika)", 0);

                    endif;
                endif;

                // Sepette aynı üründen varmı kontrolü

                $var = myconn($db, "select * from anliksiparis where urunid = $urunid and tableid = $tableid", 1);

                if ($var->num_rows != 0):

                    $urundizi = $var->fetch_assoc();
                    $sonadet = $adet + $urundizi["adet"];
                    $islemid = $urundizi["id"];


                    $guncelson = $db->prepare("update anliksiparis set adet = $sonadet where id = $islemid");
                    $guncelson->execute();
                    uyari("Adet Güncellendi", "success");

                    // Masanın Log kaydı

                    $ekleson2 = $db->prepare("update masalar set saat=$saat, dakika=$dakika where id=$tableid");
                    $ekleson2->execute();
                else:
                    if ($iskonto != "") {
                        $result = ($urunfiyat / 100) * $iskonto;
                        $urunfiyat = $urunfiyat - $result;
                    }


                    // Garsonun id sini alıyorum, garson performans için
                    $gelen = myconn($db, "select * from garson where durum = 1", 1)->fetch_assoc();
                    $employeeidyaz = $gelen["id"];


                    // Masanın durumunu güncelleyeceğim
                    $ekleson2 = $db->prepare("update masalar set durum=1 where id=$tableid");
                    $ekleson2->execute();

                    // Masanın log kaydı
                    $saat = date("H");
                    $dakika = date("i");
                    $ekleson2 = $db->prepare("update masalar set saat=$saat, dakika=$dakika where id=$tableid");
                    $ekleson2->execute();


                    $ekle = "insert into anliksiparis (tableid, employeeid, urunid, urunad, urunfiyat, adet) VALUES ($tableid, $employeeidyaz, $urunid, '$urunad', $urunfiyat, $adet)";
                    $ekleson = $db->prepare($ekle);
                    $ekleson->execute();
                    uyari("Ürün Eklendi", "success");
                endif;
            endif;
        else:
            uyari("Hata var", "danger");
        endif;
        break;

    case "urun":
        $katid = htmlspecialchars($_GET["katid"]);
        $a = "select * from urunler where katid = $katid";
        $d = myconn($db, $a, 1);
        while ($result = $d->fetch_assoc()):
            echo '<label class="btn btn-dark m-2"><input name="urunid" type="radio" value="' . $result["ID"] . '"/>  ' . $result["ad"] . '</label>';
        endwhile;
        break;

    case "kontrol":
        $ad = htmlspecialchars($_POST["ad"]);
        $password = htmlspecialchars($_POST["password"]);
        $sifre = sifrele($password);
        if ($ad != "" && $sifre != "") :
            $var = myconn($db, "select * from users where name='$ad' and Password='$sifre'", 1);
            if ($var->num_rows == 0):
                echo '<div class = "alert alert-danger text-center">Incorrect User Or Password!</div>';
            else:
                $garson = $var->fetch_assoc();
                $employeeid = $garson["id"];
                myconn($db, "update users set status = 1 where id = $employeeid", 1);
                ?>
                <script>
                    window.location.reload();
                </script>
            <?php
            endif;
        else:
            echo '<div class = "alert alert-danger text-center">Please enter user name and password</div>';
        endif;
        break;

    case "cikis":
        myconn($db, "update users set durum=0", 1);
        header("Location:index.php");
        break;

    case "garsonbilgigetir" :
        garsonbilgi($db);
        break;

    case "hazirurunsil":
        if (!$_POST):
            echo "Postdan Gelmiyorsun";
        else:
            $id = htmlspecialchars($_POST["id"]);
            $mutfaksorgu = "delete from mutfaksiparis where id = $id";
            $mutfaksilme = $db->prepare($mutfaksorgu);
            $mutfaksilme->execute();
        endif;
        break;
endswitch;

?>

