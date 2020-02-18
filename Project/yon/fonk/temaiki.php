<?php
$vt = new mysqli("localhost", "root", "", "ogrenci") or die("Baglanamadi");
$vt->set_charset("utf8");

class temadestek {

protected $select = array();
protected $color = array(
    "danger" => "danger",
    "info" => "info",
    "dark" => "dark",
    "primary" => "primary",
    "warning" => "warning",
    "secondary" => "secondary"
);

function myconn($vt, $sql, $option) {
    $a = $sql;
    $b = $vt->prepare($a);
    $b->execute();
    if ($option == 1):
        return $c = $b->get_result();
    endif;
}

// genel sorgu

function tas2linkkontrol($vt)
{

    $id = $this->coz($_COOKIE["ID"]);

    $sql = "select * from users where id=$id";
    $gelensonuc = $this->genelsorgu($vt, $sql);
    $b = $gelensonuc->fetch_assoc();

    if ($b["User_Type"] == "Manager") :

        echo '<a href="control.php?islem=yonayar" id="lk" class="col-md-11"> <i class="fas fa-cog"></i><span style="margin-left:10px;">Yönetici Ayarları</span></a>';

    endif;
}

function coz($veri) {
    return unserialize(gzuncompress(gzinflate(base64_decode($veri))));
}

private function genelsorgu($dv, $sql) {
    $sqlm = $dv->prepare($sql);
    $sqlm->execute();
    return $sqlson = $sqlm->get_result();
}

// şifreleme

function defmasayon($vt)
{

    $so = $this->genelsorgu($vt, "select * from masalar order by id desc limit 5");

    echo '<table class="table text-center  table-bordered  col-md-12  " >
                <thead>
                    <tr>
                        <th scope="col" colspan="3">MASALAR</th>
                     </tr>    
                </thead>
                <tbody>';

    while ($result = $so->fetch_assoc()):

        echo '<tr>
                    <td>' . $result["ad"] . '</td>
                    <td><a href="control.php?islem=masaguncel&tableid=' . $result["id"] . '" class="btn btn-sm text-white" style="background-color:#548e20;">Güncelle </a></td>   
                    <td><a href="control.php?islem=deletetable&tableid=' . $result["id"] . '" class="btn btn-sm text-white" style="background-color:#fa2b5c;" data-confirm="Masayı silmek istediğinize emin misiniz ?">Sil </a></td>        
                </tr>';

    endwhile;

    echo '</tbody></table>';
}

// uyarı

function defurunyon($vt, $option)
{

    if ($option == 0) :
        $so = $this->genelsorgu($vt, "select * from urunler order by id desc limit 5 ");
    endif;

    echo '<table class="table text-center table-bordered" >
                <thead>
                    <tr>
                        <th scope="col" colspan="4">ÜRÜNLER</th>	       
                    </tr>    
                </thead>
            <tbody>';

    while ($result = $so->fetch_assoc()):

        echo '<tr>
                    <td>' . $result["ad"] . '</td>
                    <td>' . $result["fiyat"] . '</td>
                    <td><a href="control.php?islem=urunguncel&urunid=' . $result["id"] . '" class="btn btn-sm text-white" style="background-color:#548e20;">Güncelle </a></td>   
                    <td><a href="control.php?islem=urunsil&urunid=' . $result["id"] . '" class="btn btn-sm text-white" style="background-color:#fa2b5c;" data-confirm="Ürünü silmek istediğinize emin misiniz ?">Sil </a></td>        
                </tr>';
    endwhile;

    echo '</tbody></table>';
}

// tasarım 2 link  kontrol

function BolumAdGetir($db, $deger)
{

    $result = $this->genelsorgu($db, "select * from bolumler where id=" . $deger, 1);
    $masason = $result->fetch_assoc();

    echo '<div class="col-md-12 bg-white pt-2"><h3>Bölüm Adı : ' . $masason["ad"] . '</h3></div>';
}

//  default masa listele

function temaikimasalar($dv) {
    $result = $this->genelsorgu($dv, "select * from dinnertable");
    $bos = 0;
    $dolu = 0;
    while ($masason = $result->fetch_assoc()) :
        $siparisler = 'select * from dinnertable_orders where dinnertable_id =' . $masason["ID"] . ';';
        $satir = $this->genelsorgu($dv, $siparisler, 1)->num_rows;
        if ($satir == 0):
            $color = '#3ba62c;';
            $bord = "success";
        else:
            $color = '#f52f54;';
            $bord = "danger";
        endif;
        $this->genelsorgu($dv, $siparisler)->num_rows == 0 ? $bos++ : $dolu++;
        echo '<div class="col-md-2">  
                       <a href="masadetay.php?tableid=' . $masason["ID"] . '" id="lin">          
                           <div class="card border-' . $bord . ' m-1 col-md-12 p-2" > 
                               <div class="card-body text-secondary"> 
                                   <p class="card-text">
                                   <span style="font-size: 48px; color:' . $color . '" class="fas fa-user"></span>
                                   <span style="font-size: 20px;" class="ml-2 mb-4">Table -' . $masason["DinnerTable_Number"] . '</span>';
        if ($satir != 0): echo '<kbd style="float:right;">' . $satir . '</kbd>';
        endif;
        echo '</p></div></div></a></div>';
    endwhile;
}

//  default ürün listele

function dakikakontrolet($saat, $dakika)
{

    if ($saat != 0 && $dakika != 0) :

        if ($saat < date("H")) :

            $deger = (60 + date("i")) - $dakika;

            echo '<br><kbd class="ml-2 mb-0 float-right bg-light text-danger" style="position:absolute;">' . $deger . '  dakika önce </kbd>';

        else:

            $deger = date("i") - $dakika;

            if ($deger == 0):

                echo '<br><kbd class="ml-2 mb-0 float-right bg-light text-danger" style="position:absolute;">Yeni eklendi</kbd>';

            else:

                echo '<br><kbd class="ml-2 mb-0 float-right bg-light text-danger" style="position:absolute;">' . $deger . '  dakika önce </kbd>';

            endif;

        endif;

    endif;
}

function temaikiurungrup($db)
{
    $se = "select * from kategoriler";
    $gelen = $this->genelsorgu($db, $se, 1);
    while ($son = $gelen->fetch_assoc()) :
        echo '<a class="btn  mt-1 pt-2  text-center" sectionId="' . $son["id"] . '" style="margin:2px; background-color:#193d49; min-height:40px; min-width:80px; color:#58d0f8;">' . $son["ad"] . '</a>';
    endwhile;
}

function mutfakbilgi($db)
{

    $siparisler = $this->genelsorgu($db, "select * from mutfaksiparis where durum = 0");

    $idkontrol = array();

    while ($geldiler = $siparisler->fetch_assoc()) :
        $tableid = $geldiler["tableid"];

        if (!in_array($tableid, $idkontrol)) :

            $idkontrol[] = $tableid;

            $siparisler2 = $this->genelsorgu($db, "select * from mutfaksiparis where tableid=$tableid and durum = 0");

            $tablename = $this->genelsorgu($db, "select * from masalar where id=$tableid");
            $masabilgi = $tablename->fetch_assoc();

            echo '<div class="col-md-2 ">
                        <div class="card mt-1 p-1 bg-white border-dark" style="width:16rem;">
                            <div class="card-body">
                                <h5 class="card-title text-center"><kbd class="bg-dark">' . $masabilgi["ad"] . '</kbd></h5>
          <p class="card-text">
          <div class="row">
               <div class="col-md-7 mt-2 border-bottom bg-dark text-white">Ürün</div>
                  <div class="col-md-5 mt-2 border-bottom bg-dark text-white">Adet</div>
                     </div>';
            while ($geldiler2 = $siparisler2->fetch_assoc()) :

                echo '<div class="row">
       <div class="col-md-7 mt-2 border-bottom"><span class="text-danger">';
                $this->mutfakdakika($geldiler2["saat"], $geldiler2["dakika"]);
                echo '</span> ' . $geldiler2["urunad"] . '</div>
    <div class="col-md-3 mt-2 border-bottom">' . $geldiler2["adet"] . '</div>
    <div class="col-md-2 mt-2 border-bottom" id="mutfaklink">
   <a sectionId="' . $geldiler2["urunid"] . '" sectionId2="' . $geldiler2["tableid"] . '"><i class="fas fa-check" style="color:#6C6; font-size:20px;"></i></a></div>
						</div>';

            endwhile;

            echo '</p></div></div></div>';

        endif;

    endwhile;
}

// masalar

function mutfakdakika($saat, $dakika)
{

    if ($saat != 0 && $dakika != 0) :

        if ($saat < date("H")) :

            $deger = (60 + date("i")) - $dakika;

            echo $deger;

        else:

            $deger = date("i") - $dakika;

            echo $deger;

        endif;

    endif;
}

function GirisYetkiDurum($db, $tabloTip)
{

    echo '<select name="User_Name" class="form-control mt-2">';

    $b = $this->genelsorgu($db, "select * from " . $tabloTip);

    while ($garsonlar = $b->fetch_assoc()) :

        echo '<option value="' . $garsonlar["ad"] . '">' . $garsonlar["ad"] . '</option>';

    endwhile;

    echo ' </select>';
}

public function logincontrol($veritabani, $User_Name, $Password, $tablo, $aktifBolum)
{

    $sor = $veritabani->prepare("select * from $tablo where ad='$User_Name' and Password='$Password'");
    $sor->execute();
    $sonbilgi = $sor->get_result();
    $veri = $sonbilgi->fetch_assoc();
    if ($sonbilgi->num_rows == 0) :
        $this->uyari("danger", "Bilgiler Hatalı", "index.php");
    else:

        $sor = $veritabani->prepare("update $tablo set durum=1,AktifBolum=$aktifBolum where ad='$User_Name' and Password='$Password'");
        $sor->execute();

        if ($tablo == "garson") :

            $this->uyari("success", "Giriş yapılıyor", "masalar.php");

        elseif ($tablo == "kasiyer") :

            $this->uyari("success", "Giriş yapılıyor", "kasiyer/index.php");

        endif;

        setcookie("kul", $User_Name, time() + 60 * 60 * 24);
        $id = $this->Passwordle($veri["id"]);
        setcookie("Oturumid", $id, time() + 60 * 60 * 24);
        setcookie("OturumTipi", $tablo, time() + 60 * 60 * 24);
    endif;
}

private function uyari($tip, $metin, $sayfa)
{

    echo '<div class="alert alert-' . $tip . ' mt-5">' . $metin . '</div>';
    header("Location:" . $sayfa);
}

function Passwordle($veri)
{

    return base64_encode(gzdeflate(gzcompress(serialize($veri))));
}

// giris kontrol	

function AlanKontrol($deger)
{

    if ($deger == 1) :

        if ($_COOKIE["OturumTipi"] != "kasiyer") :

            header("Location:../index.php");

        endif;

    elseif ($deger == 2) :

        if ($_COOKIE["OturumTipi"] != "garson") :

            header("Location:kasiyer/index.php");

        endif;

    endif;
}

public function cookcon($d, $durum = false, $adres1, $adres2 = false)
{

    if (isset($_COOKIE["kul"])) :
        $User_Name = $_COOKIE["kul"];
        $OturumTipi = $_COOKIE["OturumTipi"];
        $id = $this->coz($_COOKIE["Oturumid"]);


        $sor = $d->prepare("select * from " . $OturumTipi . " where id=$id");
        $sor->execute();
        $sonbilgi = $sor->get_result();
        $veri = $sonbilgi->fetch_assoc();

        if ($User_Name != $_COOKIE["kul"]) :
            setcookie("kul", $User_Name, time() - 10);
            setcookie("Oturumid", $id, time() - 10);
            setcookie("OturumTipi", $OturumTipi, time() - 10);
            header("Location:" . $adres1);
        else:
            if ($durum == true) : header("Location:" . $adres2);
            endif;

        endif;

    else:

        if ($durum == false) : header("Location:" . $adres1);
        endif;

    endif;
}

function BolumleriGetir($db)
{

    echo '<div class="row">';
    $bolumler = $this->genelsorgu($db, "select * from bolumler");
    while ($bolumlerson = $bolumler->fetch_assoc()):

        echo '
		
		<div class="col-md-3 mx-auto text-center">
		<label class="btn m-1 p-2 btn-block diger r' . $bolumlerson["id"] . '" id="girisButon">
		<input name="bolum" type="radio" value="' . $bolumlerson["id"] . '"  />' . $bolumlerson["ad"] . '</label>
		
		</div>';

    endwhile;

    echo '</div>';
}

function bolyon($vt)
{

    $bolumler = $this->genelsorgu($vt, "select * from bolumler");

    echo '<table class="table text-center table-striped table-bordered mx-auto col-md-6 mt-4 " >
                <thead>
                    <tr>
                        <th scope="col"><a href="control.php?islem=bolekle" class="btn btn-success">+</a> Bölüm Adı</th>
                            <th scope="col">color</th>
                            <th scope="col">Güncelle</th>
                        <th scope="col">Sil</th>    
                    </tr>    
                </thead>
                <tbody>';

    while ($result = $bolumler->fetch_assoc()):

        echo ' <tr>
                        <td>' . $result["ad"] . '</td>
                        <td class="text-' . $result["color"] . '">' . $result["color"] . '</td>	
                        <td><a href="control.php?islem=bolguncel&bolid=' . $result["id"] . '" class="btn btn-warning">Güncelle </a></td>   
                        <td><a href="control.php?islem=bolsil&bolid=' . $result["id"] . '" class="btn btn-danger" data-confirm="bölüm silmek istediğinize emin misiniz ?">Sil </a></td>        
                </tr>';

    endwhile;

    echo '</tbody></table>';
}

// bolüm listele

function bolsil($vt)
{

    @$tableid = $_GET["bolid"];

    if ($tableid != "" && is_numeric($tableid)) :
        $this->genelsorgu($vt, "delete from bolumler where id=$tableid");
        @$this->uyari("success", "Bölüm Başarıyla silindi", "control.php?islem=bolyon");

    else:
        @$this->uyari("danger", "HATA OLUŞTU", "control.php?islem=bolyon");

    endif;
}

// bölüm SİL

function bolguncel($vt)
{

    @$buton = $_POST["buton"];

    echo '<div class="col-md-3  table-light  text-center mx-auto mt-5 table-bordered" style="border-radius:10px; ><div class="row">';

    if ($buton):

        @$ad = htmlspecialchars($_POST["ad"]);
        @$color = htmlspecialchars($_POST["color"]);
        @$bolid = htmlspecialchars($_POST["bolid"]);

        if ($ad == "" || $color == "") :
            $this->uyari("danger", "Bilgiler boş olamaz", "control.php?islem=bolyon");

        else:
            // veritabanı işlemleri

            $this->genelsorgu($vt, "update bolumler set ad='$ad',color='$color' where id=$bolid");

            $this->uyari("success", "BÖLÜM GÜNCELLENDİ", "control.php?islem=bolyon");

        endif;

    else:

        $tableid = $_GET["bolid"];

        $aktar = $this->genelsorgu($vt, "select * from bolumler where id=$tableid")->fetch_assoc();
        echo '<form action="" method="post">
                        <div class="col-md-12  border-bottom"><h4 class="mt-2">BÖLÜM GÜNCELLE</h4></div>
                            <div class="col-md-12  text-danger mt-2"><input type="text" name="ad" value="' . $aktar["ad"] . '" class="form-control">
                        </div>
                            <div class="col-md-12  text-danger mt-2"><select name="color" class="form-control">';

        foreach ($this->color as $key => $value) :

            if ($key == $aktar["color"]) :

                echo '<option value="' . $value . '" selected="selected">' . $value . '</option>';

            else:

                echo '<option value="' . $value . '" >' . $value . '</option>';

            endif;

        endforeach;


        echo ' </select><div class="col-md-12 text-danger mt-2"><input name="buton" type="submit" class="btn btn-success mt-3 mb-3" /></div>
                        <input type="hidden" name="bolid"  value="' . $aktar["id"] . '" />
    
                </form>  ';

    endif;

    echo '</div></div>';
}

// bölüm güncelleme

function bolekle($vt)
{

    @$buton = $_POST["buton"];

    echo '<div class="col-md-3 table-light  text-center mx-auto mt-5 table-bordered" style="border-radius:10px;>';

    if ($buton):

        @$ad = htmlspecialchars($_POST["ad"]);
        @$color = htmlspecialchars($_POST["color"]);


        if ($ad == "") :
            $this->uyari("danger", "Bilgiler boş olamaz", "control.php?islem=bolyon");

        else:
            // veritabanı işlemleri

            $this->genelsorgu($vt, "insert into bolumler (ad,color) VALUES('$ad','$color')");

            $this->uyari("success", "BÖLÜM EKLENDİ", "control.php?islem=bolyon");

        endif;

    else:

        echo '<div class="row">
                    <form action="" method="post" >  
                        <div class="col-md-12  border-bottom"><h4 class="mt-2">BÖLÜM EKLE</h4></div>
                            <div class="col-md-12  text-danger mt-2">
                               <input type="text" name="ad"  class="form-control" placeholder="Bölüm adı" required="required">
                    </div>
                        <div class="col-md-12 text-danger mt-2"><select name="color" class="form-control">';


        foreach ($this->color as $key => $value) :

            echo '<option value="' . $key . '" >' . $value . '</option>';

        endforeach;


        echo ' </select></div>
                    <div class="col-md-12 "><input name="buton" type="submit" class="btn btn-success mt-3 mb-3" value="EKLE" /></div> 
                </form></div>';

    endif;

    echo '</div>';
}

// bolüm ekleme		
// ----- MASA YÖNETİM
// ----- GARSON YÖNETİM KODLARI

function kasiyeryon($vt)
{

    $so = $this->genelsorgu($vt, "select * from kasiyer");

    echo '<table class="table text-center table-striped table-bordered mx-auto col-md-6 mt-4 " >
                <thead>
                    <tr>
                        <th scope="col"><a href="control.php?islem=kasiyerekle" class="btn btn-success">+</a> Kasiyer Adı</th>
                        <th scope="col">Güncelle</th>
                        <th scope="col">Sil</th>    
                    </tr>    
                </thead>
            <tbody>';

    while ($result = $so->fetch_assoc()):

        echo '<tr>
                    <td>' . $result["ad"] . '</td>
                    <td><a href="control.php?islem=kasiyerguncel&kasiyerid=' . $result["id"] . '" class="btn btn-warning">Güncelle </a></td>   
                    <td><a href="control.php?islem=kasiyersil&kasiyerid=' . $result["id"] . '" class="btn btn-danger" data-confirm="Kasiyeri silmek istediğinize emin misiniz ?">Sil </a></td>        
                </tr>';

    endwhile;

    echo '</tbody></table>';
}

// kasiyer listele

function kasiyersil($vt)
{

    @$kasiyerid = $_GET["kasiyerid"];

    if ($kasiyerid != "" && is_numeric($kasiyerid)) :
        $this->genelsorgu($vt, "delete from kasiyer where id=$kasiyerid");
        @$this->uyari("success", "Kasiyer Başarıyla silindi", "control.php?islem=kasiyeryon");
    else:
        @$this->uyari("danger", "HATA OLUŞTU", "control.php?islem=kasiyeryon");
    endif;
}

// kasiyer SİL

function kasiyerekle($vt) {


echo '<div class="col-md-3  table-light text-center mx-auto mt-5 table-bordered" style="border-radius:10px;">';

if ($_POST):

    @$kasiyerad = htmlspecialchars($_POST["kasiyerad"]);
    @$kasiyerPassword = htmlspecialchars($_POST["kasiyerPassword"]);

    // md5 sha şifreleme

    if ($kasiyerad == "" || $kasiyerPassword == "") :
        $this->uyari("danger", "Bilgiler boş olamaz", "control.php?islem=kasiyeryon");

    else:
        // veritabanı işlemleri

        $this->genelsorgu($vt, "insert into kasiyer (ad,Password) VALUES('$kasiyerad','$kasiyerPassword')");

        $this->uyari("success", "KASİYER EKLENDİ", "control.php?islem=kasiyeryon");

    endif;

else:
?>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <?php
    echo '<div class="col-md-12  border-bottom"><h4 class="mt-2">KASİYER EKLE</h4></div>
    
                    <div class="col-md-12 "><input type="text" name="kasiyerad" class="form-control mt-3" require placeholder="Kasiyer Adı"/></div>
	
                        <div class="col-md-12 "><input type="text" name="kasiyerPassword" class="form-control mt-3" require  placeholder="Kasiyer Şifresi"/></div>
    
                            <div class="col-md-12 "><input name="buton" type="submit" class="btn btn-success mt-3 mb-3"  value="EKLE"/></div>  
                </form>';
    endif;
    echo '</div>';
    }

    // kasiyer ekleme

    function kasiyerguncel($vt) {

    //  md5 sha gelen veri şifrelenerek şifreli haliyle kayıt edilecek
    // çift şifre koruması yapılacak diyelim. yeni girilen şifreler eşleştirecek


    echo '<div class="col-md-3 table-light  text-center mx-auto mt-5 table-bordered" style="border-radius:10px;" >';

    if ($_POST):

        @$kasiyerad = htmlspecialchars($_POST["kasiyerad"]);
        @$kasiyerPassword = htmlspecialchars($_POST["kasiyerPassword"]);
        @$kasiyerid = htmlspecialchars($_POST["kasiyerid"]);

        if ($kasiyerad == "" || $kasiyerPassword == "") :
            $this->uyari("danger", "Bilgiler boş olamaz", "control.php?islem=kasiyeryon");

        else:
            // veritabanı işlemleri

            $this->genelsorgu($vt, "update kasiyer set ad='$kasiyerad',Password='$kasiyerPassword' where id=$kasiyerid");

            $this->uyari("success", "KASİYER GÜNCELLENDİ", "control.php?islem=kasiyeryon");

        endif;

    else:

    $kasiyerid = $_GET["kasiyerid"];

    $aktar = $this->genelsorgu($vt, "select * from kasiyer where id=$kasiyerid")->fetch_assoc();
    ?>

    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        <?php
        echo ' <div class="col-md-12  border-bottom"><h4 class="mt-2">KASİYER GÜNCELLE</h4></div>
    
                        <div class="col-md-12  text-danger mt-2">Kasiyer adı<input type="text" name="kasiyerad" class="form-control mt-3"  value="' . $aktar["ad"] . '"/></div>
                     
                            <div class="col-md-12  text-danger mt-2">Kasiyer Şifre<input type="text" name="kasiyerPassword" class="form-control mt-3"  value="' . $aktar["Password"] . '"/></div>	
                            
                                <div class="col-md-12 "><input name="buton" value="Güncelle" type="submit" class="btn btn-success mt-3 mb-3" /></div>
                                    
                                    <input type="hidden" name="kasiyerid"  value="' . $kasiyerid . '" />
    
                    </form>';

        endif;

        echo '</div>';
        }

        // kasiyer güncelleme

        function kasiyerper($vt)
        {

            @$option = $_GET["tar"];

            switch ($option) :

                case "ay":
                    $this->genelsorgu($vt, "Truncate gecicikasiyer");

                    $veri = $this->genelsorgu($vt, "select * from rapor where tarih >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");

                    break;

                case "tarih":
                    $this->genelsorgu($vt, "Truncate gecicikasiyer");
                    $tarih1 = $_POST["tarih1"];
                    $tarih2 = $_POST["tarih2"];
                    echo '<div class="alert alert-info text-center mx-auto mt-4">
		
		' . $tarih1 . ' - ' . $tarih2 . '
		
		</div>';
                    $veri = $this->genelsorgu($vt, "select * from rapor where DATE(tarih) BETWEEN '$tarih1' AND '$tarih2'");

                    break;


                default;
                    $this->genelsorgu($vt, "Truncate gecicikasiyer");
                    $veri = $this->genelsorgu($vt, "select * from rapor where tarih >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");

            endswitch;

            echo ' <table  class="table text-center table-light table-bordered mx-auto mt-4  col-md-8">';

            if (@$tarih1 != "" || @$tarih2 != "") :

                echo '<thead>
                        <tr>
                            <th colspan="4"><a href="cikti.php?islem=kasiyercikti&tar1=' . $tarih1 . '&tar2=' . $tarih2 . '" onclick="ortasayfa(this.href,\'mywindow\',\'900\',\'800\',\'yes\');return false" class="btn btn-warning m-2">ÇIKTI</a>';

                echo '<a href="kasiyerexcel.php?tar1=' . $tarih1 . '&tar2=' . $tarih2 . '" class="btn btn-info">EXCEL AKTAR</a></th></tr>';

            endif;

            echo '<thead>
                        <tr>
                            <th><a href="control.php?islem=kasiyerper&tar=ay">Bu Ay</a></th> 
				<form action="control.php?islem=kasiyerper&tar=tarih" method="post">
				
                            <th><input type="date" name="tarih1" class="form-control col-md-10"></th> 
                            <th><input type="date" name="tarih2" class="form-control col-md-10"></th> 
                            <th><input name="buton" type="submit" class="btn btn-danger" value="GETİR" ></form></th>  
                        </tr>                
                    </thead>
                <tbody>
                <tr>
                    <th colspan="4">
                        <table class="table text-center table-bordered col-md-12 table-striped">
                            <thead>
                                <tr class="bg-dark text-warning">
                                <th colspan="2">Kasiyer Ad</th>   
                                <th colspan="1">Adet</th> 
                                <th colspan="1">Hasılat</th>
                </tr>                         
                            </thead><tbody>';


            if ($this->genelsorgu($vt, "select * from gecicikasiyer")->num_rows == 0) :
                while ($gel = $veri->fetch_assoc()):

                    // garson adını çekiyoruz
                    $kasiyerid = $gel["kasiyerid"];
                    $masaveri = $this->genelsorgu($vt, "select * from kasiyer where id=$kasiyerid")->fetch_assoc();
                    $kasiyerad = $masaveri["ad"];
                    // garson adını çekiyoruz

                    $raporbak = $this->genelsorgu($vt, "select * from gecicikasiyer where kasiyerid=$kasiyerid");

                    if ($raporbak->num_rows == 0) :
                        //ekleme

                        $tutar = $gel["urunfiyat"];
                        $adet = $gel["adet"];
                        $hasilat = $adet * $tutar;


                        $this->genelsorgu($vt, "insert into gecicikasiyer (kasiyerid,kasiyerad,adet,hasilat) VALUES($kasiyerid,'$kasiyerad',$adet,$hasilat)");
                    else:
                        $raporson = $raporbak->fetch_assoc();

                        $gelenadet = $raporson["adet"]; //4 
                        $gelenhasilat = $raporson["hasilat"]; //20


                        $sonadet = $gelenadet + $gel["adet"];
                        $toplamHasilat = ($gel["adet"] * $gel["urunfiyat"]) + $gelenhasilat;

                        $this->genelsorgu($vt, "update gecicikasiyer set adet=$sonadet,hasilat=$toplamHasilat where kasiyerid=$kasiyerid");

                        //güncelleme

                    endif;

                endwhile;

            endif;

            $son = $this->genelsorgu($vt, "select * from gecicikasiyer order by hasilat desc;");
            $toplamadet = 0;
            $toplamhasilat = 0;

            while ($listele = $son->fetch_assoc()) :

                echo '<tr>
                         <td colspan="2">' . $listele["kasiyerad"] . '</td>   
                         <td colspan="1">' . $listele["adet"] . '</td>
                         <td colspan="1">' . number_format($listele["hasilat"], 2, '.', '.') . '</td>  
                    </tr>';
                $toplamadet += $listele["adet"];
                $toplamhasilat += $listele["hasilat"];


            endwhile;

            echo '<tr class="bg-dark text-white">
                         <td colspan="2">TOPLAM</td>   
                         <td colspan="1">' . $toplamadet . '</td> 
			 <td colspan="1">' . number_format($toplamhasilat, 2, '.', '.') . '</td> 
                 </tr>
			</tbody> </table> 
   
                 </th>          
                </tr>
                
                </tbody>
                </table>';

            echo '</div>';
        }

        // kasiyer perfor
        // ----- KASİYER KODLARI
        }
        ?>



