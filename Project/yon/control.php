<?php
include_once("fonk/yonfonk.php");
$yonclas = new users;
$yonclas->cookcon($vt, false);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="../dosya/jqu.js"></script>
    <link rel="stylesheet" href="../dosya/boost.css">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <style>
            body {
                height:100%;
                width:100%;
                position:absolute;
            }
            .container-fluid,
            .row-fluid {
                height:inherit;
            }
            #lk:link, #lk:visited {
                color:#888;
                text-decoration:none;
            }
            #lk:hover {
                color:#000;
            }
            #div2 {
                min-height:100%; 
                background-color:#EEE;
            }
            #div1 {
                background-color:#fff;
                border:1px solid #F1F1F1;
                border-radius:5px;
            }
        </style>
        <script type="text/javascript">
            var popupWindow = null;

            function ortasayfa(url, winName, w, h, scroll) {
                LeftPosition = (screen.width) ? (screen.width - w) / 2 : 0;
                TopPosition = (screen.height) ? (screen.height - h) / 2 : 0;
                settings = 'height=' + h + ', width=' + w + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=' + scroll + ', resizable'
                popupWindow = window.open(url, winName, settings)
            }

            $(document).ready(function () {
                $('a[data-confirm]').click(function (ev) {
                    var href = $(this).attr('href');

                    if (!$('#dataConfirmModal').length) {
                        $('body').append('<div class="modal fade" id="dataConfirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">\n\
                                                <div class="modal-dialog modal-dialog-centered" role="document">\n\
                    <div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">CONFIRMATION</h5></div>\n\
                        <div class="modal-body"></div>   \n\
                            <div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">NO</button><a class="btn btn-primary" id="dataConfirmOK">YES</a></div></div></div></div></div>');
            
                            $('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
                            $('#dataConfirmOK').attr('href',href);
                            $('#dataConfirmModal').modal({show:true});                            
                            return false;
                            //window.location.reload();
                    }
                });
            });
            
        </script>
        <title>RESTAURANT MANAGEMENT SYSTEM</title>
    </head>
    <body>
    <div class = "container-fluid bg-light">
        <div class = "row row-fluid">
            <div class = "col-md-2 border-right " style = "min-height:750px;">
                <div class = "row">
                    <div class = "col-md-12 bg-dark p-4 mx-auto text-center text-white font-weight-bold">
                        <h4>WELCOME</h4>
                        <h4><?php echo $yonclas->User_Name($vt); ?></h4>
                    </div>
                </div>
                <div class = "row">

                    <?php
                    if ($yonclas->User_Type($vt) == "manager"):
                        echo'
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom border-top text-white">
                        <a href="control.php?islem=masayon" id="lk">TABLE MANAGEMENT</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=urunyon" id="lk">ITEM MANAGEMENT</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=menuyon" id="lk">MENU MANAGEMENT</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=garsonyon" id="lk">EMPLOYEE MANAGEMENT</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=garsonper" id="lk">WAITER REPORT</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=rapor" id="lk">REPORT MANAGEMENT</a>
                    </div>';
                    elseif ($yonclas->User_Type($vt) == "waiter"):
                        echo '<div class = "col-md-12 bg-light p-2 pl-3 border-bottom border-top text-white">
                        <a href="control.php?islem=orderyon" id="lk">ORDER MANAGEMENT</a></div>';
                    endif;
                    ?>


                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=Passworddegistir" id="lk">CHANGE PASSWORD</a>
                    </div>
                    <div class = "col-md-12 bg-light p-2 pl-3 border-bottom text-white">
                        <a href="control.php?islem=cikis" id="lk" data-confirm="ARE YOU SURE?">EXIT</a>
                    </div>

                    <table class = "table text-center table-light table-bordered mt-2 table-striped">
                        <thead>
                            <tr class = "table-danger">
                                <th scope = "col" colspan = "4">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL WAITER</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->toplamGarson($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL ONLINE WAITER</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->toplamGarson2($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL CHIEF</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->toplamGarson3($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL ONLINE CHIEF</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->toplamGarson4($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL ORDER</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->anliktoplamsiparis2($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL MENU ORDER</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->anliktoplamsiparis($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL TABLE</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->anliktoplamMasa($vt); ?></th>
                            </tr>
                            <tr>
                                <th scope = "col" colspan = "3">TOTAL ITEM TYPE</th>
                                <th scope = "col" colspan = "1" class = "text-danger"><?php $yonclas->anliktoplamUrun($vt); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class = "col-md-10">
                <div class = "row" id="div2">
                    <div class = "col-md-12 mt-4" id="div1">

                        <?php
                        @$islem = $_GET["islem"];

                        switch ($islem) :

                            case "orderyon":
                                $yonclas->orderyon($vt, 0);
                                break;

                            case "orderyon2":
                                $yonclas->orderyon($vt, 1);
                                break;

                            case "addorder":
                                $yonclas->addorder($vt);
                                break;

                            case "takebill":
                                $yonclas->takebill($vt);
                                break;

                            case "masayon":
                                $yonclas->masayon($vt, 0);
                                break;

                            case "deletetable":
                                $yonclas->deletetable($vt);
                                break;

                            case "updatetable":
                                $yonclas->updatetable($vt);
                                break;

                            case "addtable":
                                $yonclas->addtable($vt);
                                break;

                            case "tablesonuc":
                                $yonclas->masayon($vt, 1);
                                break;

                            case "urunyon":
                                $yonclas->urunyon($vt, 0);
                                break;

                            case "urunsil":
                                $yonclas->urunsil($vt);
                                break;

                            case "urunguncelle":
                                $yonclas->urunguncelle($vt);
                                break;

                            case "urunekle":
                                $yonclas->urunekle($vt);
                                break;

                            case "aramasonuc":
                                $yonclas->urunyon($vt, 1);
                                break;

                            case "menuyon":
                                $yonclas->menuyon($vt, 0);
                                break;

                            case "menuresult":
                                $yonclas->menuyon($vt, 1);
                                break;

                            case "menuekle":
                                $yonclas->menuekle($vt);
                                break;

                            case "menusil":
                                $yonclas->menusil($vt);
                                break;
                            case "menuimage":
                                $yonclas->menuimage($vt);
                                break;

                            case "menuguncelle":
                                $yonclas->menuguncelle($vt);
                                break;

                            case "garsonyon";
                                $yonclas->garsonyon($vt, 0);
                                break;

                            case "garsonekle";
                                $yonclas->garsonekle($vt);
                                break;

                            case "garsonsil";
                                $yonclas->garsonsil($vt);
                                break;

                            case "garsonguncelle";
                                $yonclas->garsonguncelle($vt);
                                break;

                            case "employeeresult":
                                $yonclas->garsonyon($vt, 1);
                                break;

                            case "garsonper";
                                $yonclas->garsonper($vt);
                                break;

                            case "rapor":
                                $yonclas->rapor($vt);
                                break;

                            case "Passworddegistir":
                                $yonclas->Passworddegistir($vt);
                                break;

                            case "cikis":
                                $yonclas->cikis($vt, $yonclas->User_Name($vt));
                                break;

                        endswitch;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>




