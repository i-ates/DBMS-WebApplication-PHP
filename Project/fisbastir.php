<?php session_start(); include("function/function.php");
$masam = new system;
@$tableid = $GET["tableid"];

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="dosya/jqu.js"></script>
    <link rel="stylesheet" href="dosya/boost.css">
    <link rel="stylesheet" href="dosya/stil.css">

    <script>
        $(document).ready(function(){

            $('#hesapalbtn').click(function(){
                $.ajax({
                    type : "POST",
                    url : 'islemler.php?islem=hesap',
                    data : $('#hesapform').serialize(),
                    success: function(donen_veri){
                    $('#hesapform').trigger("reset");
                    window.opener.location.reload(true);
                    window.close();
                    }

                });
            });

        });
    </script>

<title>FİŞ BASTIR</title>
</head>
<body>

    <div class="container-fluid"> 

        <div class = "row">

            <div class = "col-md-2 mx-auto">

            <?php
            
            if($tableid!=""):
            
            $son=$masam->masagetir($db, $tableid);
            $dizi=$son->fetch_assoc();
            $dizi["ad"];

                $id=htmlspecialchars($_GET["tableid"]);

                $a="select * from anliksiparis where tableid=$id";
                $d=$masam->myconn($db,$a,1);
         
                if($d->num_rows==0) :
                    
                    echo 'Henüz Sipariş Yok';

                else:

                echo '<table class="table">
                        
                                <tr>
                                    <td colspan="3" class="border-top-0 text-center"><strong>Masa :</strong>'.$dizi["ad"].'</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="3" class="border-top-0 text-left"><strong>Tarih :</strong>'.date("d.m.Y").'</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="3" class="border-top-0 text-left"><strong>Saat :</strong>'.date("h:i:s").'</td>
                                </tr>';

                $sontutar = 0;

                while($gelenson=$d->fetch_assoc()) :

                    $tutar = $gelenson["adet"] * $gelenson["urunfiyat"];

                    $sontutar += $tutar;
                    $tableid = $gelenson["tableid"];
                    
                    echo '<tbody><tr>
                            <td colspan="1" class="border-top-0 text-center">'.$gelenson["urunad"].'</td>
                            <td colspan="1" class="border-top-0 text-center">'.$gelenson["adet"].'</td>
                            <td colspan="1" class="border-top-0 text-center">'.number_format($tutar,2,'.',',').'</td>
                          </tr>';

                endwhile;

                echo '<tr>
                        <td colspan="2" class="border-top-0 font-weight-bold"><strong>GENEL TOPLAM :</strong></td>
                        <td colspan="2" class="border-top-0 text-center">'.number_format($sontutar,2,'.',',').' TL</td>
                     </tr>

                </tbody>
                
            </table>
                
                        <form id = "hesapform">

                        <input type="hidden" name="tableid" value="'.$id.'"/>
                        <button type="button" id="hesapalbtn" value="HESAP AL" style="font-weight:bold; height:40px;" class="btn btn-dark btn-block mt-2">HESAP AL</button>
                        
                        </form>';

            endif;
endif;
            ?>
            </div>

        </div>

    </div>

    </body>
             
</html>