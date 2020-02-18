<?php
include("fonksiyon/fonksiyon.php");
include_once("yon/fonk/temaiki.php");
$system = new system;
$tema2 = new temadestek;

$veri = $system->myconn($db, "select * from users where user_type = 'waiter' and status=1", 1)->num_rows;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="dosya/jqu.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
          integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="dosya/stil.css">
    <title>Restaurant Management System</title>
    <script>
        $(document).ready(function () {
            $("#bekleyenler").hide();
            $("#rezerveformalan").hide();
            $("#rezervelistesi").hide();

            var deger = "<?php echo $veri; ?>";
            if (deger == 0) {
                $('#girismodal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('body').on('hidden.bs.modal', '.modal', function () {
                    $(this).removeData('bs.modal');
                });
            } else {
                $('#girismodal').modal('hide');
            }

            $('#girisbak').click(function () {
                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=kontrol',
                    data: $('#garsonform').serialize(),
                        success: function (donen_veri) {
                        $('#garsonform').trigger("reset");
                        $('.modalcevap').html(donen_veri);
                    },
                })
            });

            setInterval(function () {
                window.location.reload();
            }, 60000);

            $("#ac").click(function () {
                $("#bekleyenler").load("islemler.php?islem=garsonbilgigetir");
                // burada bişe yapacağız
                $("#bekleyenler").animate({
                    opacity: 'show',
                    width: 'show'
                }, 'fast', 'linear', function () {
                });
            });


            $("#kapa").click(function () {
                // burada bişe yapacağız
                $("#bekleyenler").animate({
                    opacity: 'hide',
                    width: 'hide'
                }, 'fast', 'linear', function () {
                });

                window.location.reload();


            });

            $("#rezerveformac").click(function () {
                $("#rezerveformalan").show();
                // burada bişe yapacağız
                $("#rezerveformalan").animate({
                    opacity: 'show',
                    width: 'show'
                }, 'fast', 'linear', function () {
                });


                $("#rezervelistesi").hide();

            });

            $("#rezerveformkapa").click(function () {
                // burada bişe yapacağız
                $("#rezerveformalan").animate({
                    opacity: 'hide',
                    width: 'hide'
                }, 'fast', 'linear', function () {
                });

                $("#rezervelistesi").animate({
                    opacity: 'hide',
                    width: 'hide'
                }, 'fast', 'linear', function () {
                });

                window.location.reload();


            });

            $("#rezerveliste").click(function () {
                $("#rezervelistesi").load("islemler.php?islem=rezervelistesi");
                // burada bişe yapacağız
                $("#rezervelistesi").animate({
                    opacity: 'show',
                    width: 'show'
                }, 'fast', 'linear', function () {
                });

                $("#rezerveformalan").hide();


            });


            $('#rezervebtn').click(function () {
                $.ajax({
                    type: "POST",
                    url: 'islemler.php?islem=rezerveet',
                    data: $('#rezerveform').serialize(),
                    success: function (donen_veri) {
                        $('#rezerveform').trigger("reset");
                        window.location.reload();
                    },
                })
            });
        });
    </script>


</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php $tema2->temaikimasalar($db); ?>
    </div>
    <div class="row bg-white border border-light" id="rezervelistesi"
         style="position:absolute; bottom:80px; right:0; width:300px; max-height:600px; overflow-y:auto; overflow-x:hidden; margin-right:10px;">
    </div>
    <div class="row bg-white border border-light" id="bekleyenler"
         style="position:absolute; bottom:80px; left:0; min-width:450px; max-height:600px; overflow-y:auto; overflow-x:hidden">
    </div>
    <div class="row bg-white border border-light" id="rezerveformalan"
         style="position:absolute; bottom:80px; right:0; width:250px; max-height:600px; overflow-y:auto; overflow-x:hidden; margin-right:10px;">
        <div class="row mx-auto text-center">
            <div class="col-md-12 font-weight-bold p-1">Masa Ad</div>
            <div class="col-md-12">
                <form id="rezerveform">
                    <select name="tableid" class="form-control mt-2">
                        <option value="0">Seç</option>
                        <?php

                        $b = $system->myconn($db, "select * from dinnertable where closed = 0", 1);

                        while ($dinnertables = $b->fetch_assoc()) :
                            echo '<option value="' . $dinnertables["ID"] . '">TABLE - ' .$dinnertables["DinnerTable_Number"] . '</option>';
                        endwhile;
                        ?>
                    </select>
            </div>
            <div class="col-md-12 font-weight-bold p-1">Kişi (Opsiyonel)</div>
            <div class="col-md-12">
                <input name="kisi" type="text" class="form-control  mt-2"/>
            </div>
            <div class="col-md-12">
                <input type="button" id="rezervebtn" value="REZERVE ET"
                       class="btn btn-info mt-4 mb-2"/>
            </div>
        </div>
    </div>


    <div class="row fixed-bottom altrow">
        <div class="col-md-12  border-info font-weight-bold ">
            <div class="row">
                <div class="col-md-1  text-center">
                    <span id="kapa"
                          class="fas fa-chevron-circle-left border border-dark text-dark p-2 m-2 mt-3 icon1"></span>
                </div>
                <div class="col-md-2   Sagcizgi Solcizgi pl-5 pt-2">
                    <i class="fas fa-chart-bar mt-4 icon3"></i>
                    <span class="icon2">Total Order<kbd
                                class="bg-danger text-white"><?php $system->siparistoplam($db); ?></kbd></span></div>

                <div class="col-md-2  Sagcizgi pl-5 pt-2"><i class="fas fa-utensils mt-4 icon3"></i><span class="icon2">Total Table<kbd
                                class="bg-danger text-white"><?php $system->masatoplam($db); ?></kbd></span></div>

                <div class="col-md-2  Sagcizgi pl-5 pt-2"><i class="fas fa-street-view mt-4 icon3"></i><span
                            class="icon2">Waiter
                        <span class="text-danger"><?php $system->garsonbak($db); ?></span></span></div>

                <div class="col-md-1 border-right text-center">
                    <i id="rezerveformac" class="fas fa-address-card mt-4 mr-2 icon1"></i>
                    <i id="rezerveliste" class="fas fa-address-book mt-4 ml-2 icon1"></i>

                </div>
                <div class="col-md-1  text-center">
                    <span id="rezerveformkapa" class="fas fa-chevron-circle-right  p-2 m-2 mt-3 icon1"></span>
                    <span id="rezerveformasdc" class="fas fa-chevron-circle-right  p-2 m-2 mt-3 icon1"> </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="girismodal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title">WAITER LOGIN</h4>
                </div>
                <div class="modal-body">
                    <form id="garsonform">
                        <div class="row mx-auto text-center">
                            <div class="col-md-12">WAITER NAME</div>
                            <div class="col-md-12"><select name="ad" class="form-control mt-2">
                                    <option value="0">Seç</option>
                                    <?php
                                    $b = $system->myconn($db, "select * from users where User_Type = 'waiter'", 1);
                                    while ($garsonlar = $b->fetch_assoc()) :
                                        echo '<option value="' . $garsonlar["User_Name"] . '">' . $garsonlar["User_Name"] . '</option>';
                                    endwhile;
                                    ?>
                                </select></div>
                            <div class="col-md-12">PASSWORD</div>
                            <div class="col-md-12">
                                <input name="password" type="password" class="form-control  mt-2"/>
                            </div>
                            <div class="col-md-12">
                                <input type="button" id="girisbak" value="GİR" class="btn btn-info mt-4"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modalcevap">
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>