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
        <script>
            function yazdir() {
                window.print();
                window.close();
            }
        </script>
    <title>Report Page</title>
    </head>
    <body>
    <div class = "container-fluid bg-light">
        <div class = "row row-fluid">
            <?php
            @$islem = $_GET["islem"];
            switch ($islem) :
                case "ciktial":
                    @$tarih1 = $_GET["tar1"];
                    @$tarih2 = $_GET["tar2"];
                    
                    $son = $yonclas->ciktiSorgusu($vt, "select distinct Table_Number, count(Table_Number) 
                                        as 'Order_Number', sum(Cost) as 'Revenue' from report where DATE(date) 
                                        BETWEEN '$tarih1' AND '$tarih2' group by table_number order by SUM(Cost) desc;");
                    $son2 = $yonclas->ciktiSorgusu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Menu_Name order by count(Menu_Name) desc;");

                    echo '<table class = "table text-center table-light table-bordered mx-auto mt-4 table-striped col-md-12">
                    <thead>
                        <tr>
                            <th colspan="5"><div class = "alert alert-info text-center mx-auto mt-4">Selected Date: '.$tarih1.' - '.$tarih2.'</div></th>
                                
                            <th colspan="2"><button onclick="yazdir()" class="btn btn-warning mx-auto mt-4">PRINT OR CONVERT PDF</a></th>
                       </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan = "4">
                            <table class="table text-center table-bordered col-md-12">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="table-dark">TABLE ORDERS AND REVENUE</th>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr class="table-danger">
                                        <th colspan="2">TABLE NAME</th>
                                        <th colspan="1">ORDER NUMBER</th>
                                        <th colspan="1">REVENUE</th>
                                    </tr>
                                </thead><tbody>';

                    $toplamadet = 0;
                    $toplamhasilat = 0;
                    while ($listele = $son->fetch_assoc()):
                        echo '<tr>
                                            <td colspan="2">'.$listele["Table_Number"].'</td>
                                            <td colspan="1">'.$listele["Order_Number"].'</td>
                                            <td colspan="1">'.number_format($listele["Revenue"],2,'.','.').'</td>
                                        </tr>';
                        $toplamadet += $listele["Order_Number"];
                        $toplamhasilat += $listele["Revenue"];
                    endwhile;

                    echo '<tr class="table-danger">
                                <td colspan="2">TOTAL</td>
                                <td colspan="1">' . $toplamadet . '</td>
                                <td colspan="1">' . substr($toplamhasilat, 0, 6) . '</td>
                          </tr>
                          </tbody> 
                                </table>                   
                                     </th>
                                     <th colspan = "4">
                                     <table class="table text-center table-bordered col-md-12">
                                <thead>
                                    <tr>
                                    <th colspan="4" class="table-dark">MENU ORDERS AND REVENUE</th>
                                    </tr>
                                    </thead>
                                <thead>
                                    <tr class="table-danger">
                                        <th colspan="2">MENU NAME</th>
                                        <th colspan="1">ORDERED MENU NUMBER</th>
                                        <th colspan="1">REVENUE</th>
                                    </tr>
                                </thead><tbody>';
                    $toplamadet2 = 0;
                    $toplamhasilat2 = 0;
                    while ($listele2 = $son2->fetch_assoc()):
                        echo '<tr>
                                            <td colspan="2">'.$listele2["Menu_Name"].'</td>
                                            <td colspan="1">'.$listele2["Menu_Number"].'</td>
                                            <td colspan="1">'.number_format($listele2["Revenue"],2,'.','.').'</td>
                                        </tr>';
                        $toplamadet2 += $listele2["Menu_Number"];
                        $toplamhasilat2 += $listele2["Revenue"];
                    endwhile;

                    echo '<tr class="table-danger">
                                <td colspan="2">TOTAL</td>
                                <td colspan="1">' . $toplamadet2 . '</td>
                                <td colspan="1">' . substr($toplamhasilat2, 0, 6) . '</td>
                            </tr></tbody></table></th></tr></tbody></table>';
                    break;
                    
                case "garsoncikti":
                    
                    @$tarih1 = $_GET["tar1"];
                    @$tarih2 = $_GET["tar2"];

                    $son = $yonclas->ciktiSorgusu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Waiter_ID order by COUNT(Waiter_ID) desc;");

                    echo '<table class = "table text-center table-light table-bordered mx-auto mt-4 table-striped col-md-12">
                    <thead>
                        <tr>
                            <th colspan="5"><div class = "alert alert-info text-center mx-auto mt-2">SELECTED DATE: '.$tarih1.' - '.$tarih2.'</div></th>            
                            <th colspan="1"><button onclick="yazdir()" class="btn btn-warning mx-auto mt-2">PRINT OR CONVERT PDF</a></th>
                       </tr>
                    </thead>
                    <tbody>
                    <tr>
   
                        <th colspan = "6">
                            <table class="table text-center table-bordered col-md-12">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="table-dark">WAITER REPORT</th>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr class="table-danger">
                                        <th colspan="2">WAITER NAME</th>
                                        <th colspan="2">ORDER COUNT</th>
                                    </tr>
                                </thead><tbody>';

                                $toplamadet = 0;

                                while ($listele = $son->fetch_assoc()):
                                    echo '<tr>
                                            <td colspan="2">'.$listele["Waiter_Name"].'</td>
                                            <td colspan="2">'.$listele["Order_Number"].'</td>
                                        </tr>';
                                        $toplamadet += $listele["Order_Number"];
                                endwhile;

                        echo '<tr class="table-danger">
                                <td colspan="2">TOTAL</td>
                                <td colspan="2">'.$toplamadet.'</td>
                            </tr></tbody></table></th></tr></tbody></table>';
                    break;
            endswitch;
            ?>
        </div>
    </div>
</body>
</html>




