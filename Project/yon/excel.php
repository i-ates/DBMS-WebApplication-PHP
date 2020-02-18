<?php

include_once("fonk/yonfonk.php");
$yonclas = new users;

function excelal ($filename = 'ExportExcel', $columns=array(),$columns2=array(),$data=array(),$data2=array(),$virgulnerede=array(),$veri1,$veri2,$veri3,$veri4) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-disposition: attachment; filename='.$filename.'.xls');
   echo "\xEF\xBB\xBF"; //bom
   
   @$tarih1 = $_GET["tar1"];
   @$tarih2 = $_GET["tar2"];
   
   $sayim = count($columns);
   
   // Başlık yazmak için
   
   echo '<table border = "1"><th style="background-color:#000000" colspan="3"><font color="#FDFDFD">'.$tarih1.' - '.$tarih2.'</font></th><tr>';
   
   foreach ($columns as $v):
       
       echo '<th style="background-color:#FFA500">'.trim($v).'</th>';
       
   endforeach;
   
    echo '</tr>';
    
    foreach ($data as $val):
        
        echo '<tr>';
        
            for ($i=0; $i<$sayim; $i++):
                if(in_array($i, $virgulnerede)):
                    echo '<td>'.str_replace('.',',',$val[$i]).'</td>';
                else:
                    echo '<td>'.$val[$i].'</td>';
                    
                endif;
            endfor;
            echo '</tr>';
       
      
       
   endforeach;
   
    echo '<tr style="background-color:#56d2ec">
        <td>TOTAL</td>
        <td>'.$veri1.'</td>
        <td>'.$veri2.'</td>
        </tr>
        ';

    $sayim2 = count($columns2);
   
   echo '<tr>';
   
   foreach ($columns2 as $v):
       
       echo '<th style="background-color:#FFA500">'.trim($v).'</th>';
       
   endforeach;
   
   // Verileri yazmak için
   
    echo '</tr>';
    
    foreach ($data2 as $val2):
        
        echo '<tr>';
        
            for ($i=0; $i<$sayim2; $i++):
                if(in_array($i, $virgulnerede)):
                    echo '<td>'. str_replace('.',',',$val2[$i]).'</td>';
                else:
                    echo '<td>'.$val2[$i].'</td>';
                    
                endif;
                
            endfor;
            
            echo '</tr>';
  
   endforeach;

    echo '<tr style="background-color:#56d2ec">
        <td>TOTAL</td>
        <td>'.$veri3.'</td>
        <td>'.$veri4.'</td>
        </tr>
        ';

}
// Fonksiyonun sonu

$masadizi=array();
$masadata=array();

$urundizi=array();
$urundata=array();


$urundizi=array(
    'Table Name',
    'Order Number',
    'Revenue'
);

$masadizi=array(
    'Menu Name',
    'Ordered Menu Number',
    'Revenue'
);

$virgulnerede=array(2);

@$tarih1 = $_GET["tar1"];
@$tarih2 = $_GET["tar2"];

$son = $yonclas->ciktiSorgusu($vt, "select distinct Table_Number, count(Table_Number) 
                                        as 'Order_Number', sum(Cost) as 'Revenue' from report where DATE(date) 
                                        BETWEEN '$tarih1' AND '$tarih2' group by table_number order by SUM(Cost) desc;");
$son2 = $yonclas->ciktiSorgusu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Menu_Name order by count(Menu_Name) desc;");
$Masatoplamadet = 0;
$Masatoplamhasilat = 0;

while ($listele = $son->fetch_assoc()):
    @$masadata[]=array(
        $listele["Table_Number"],
        $listele["Order_Number"],
        $listele["Revenue"]
    );

$Masatoplamadet += $listele["Order_Number"];
$Masatoplamhasilat += $listele["Revenue"];
endwhile;

$toplamadet2 = 0;
$toplamhasilat2 = 0;

while ($listele2 = $son2->fetch_assoc()):
    @$urundata[]=array(
        $listele2["Table_Number"],
        $listele2["Order_Number"],
        $listele2["Revenue"]
    );
$toplamadet2 += $listele2["Menu_Number"];
$toplamhasilat2 += $listele2["Revenue"];
endwhile;

excelal(date("d.m.Y"), $masadizi, $urundizi, $masadata, $urundata, $virgulnerede, $Masatoplamadet, $Masatoplamhasilat,$toplamadet2,$toplamhasilat2);

?>





