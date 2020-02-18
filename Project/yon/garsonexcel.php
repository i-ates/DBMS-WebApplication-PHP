<?php

include_once("fonk/yonfonk.php");
$yonclas = new users;

function excelal ( $filename = 'ExportExcel', $columns=array(),$data=array(),$toplamadet) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-disposition: attachment; filename='.$filename.'.xls');
   echo "\xEF\xBB\xBF"; //bom
   
   @$tarih1 = $_GET["tar1"];
   @$tarih2 = $_GET["tar2"];
   
   $sayim = count($columns);
   
   echo '<table border = "1"><th style="background-color:#000000" colspan="2"><font color="#FDFDFD">'.$tarih1.' - '.$tarih2.'</font></th><tr>';
   
   foreach ($columns as $v):
       
       echo '<th style="background-color:#FFA500">'.trim($v).'</th>';
       
   endforeach;
   
    echo '</tr>';
    
    foreach ($data as $val):
        
        echo '<tr>';
        
            for ($i=0; $i<$sayim; $i++):
                
                echo '<td>'.$val[$i].'</td>';

            endfor;
        echo '</tr>';
  
   endforeach;
   
    echo '<tr style="background-color:#56d2ec">
        <td>TOTAL</td>
        <td>'.$toplamadet.'</td>
        </tr>
        ';

}


$garsondizi=array();
$garsondata=array();

@$tarih1 = $_GET["tar1"];
@$tarih2 = $_GET["tar2"];

$garsondizi=array(
    'WAITER NAME',
    'ORDER COUNT'
);

$son = $yonclas->ciktiSorgusu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Waiter_ID order by COUNT(Waiter_ID) desc;");

$Masatoplamadet = 0;
while ($listele = $son->fetch_assoc()):

    @$garsondata[]=array(
        $listele["Waiter_Name"],
        $listele["Order_Number"]
    );

$Masatoplamadet += $listele["Order_Number"];
endwhile;


excelal(date("d.m.Y"), $garsondizi, $garsondata, $Masatoplamadet);
?>





