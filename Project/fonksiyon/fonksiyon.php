<?php

$db = new mysqli("localhost","root","","bbm473v9") or die ("Database Connection Error!");
$db->set_charset("utf8");

class system {

    private function myprivconn($vt,$sql,$option) {
        $a=$sql;
        $b=$vt->prepare($a);
        $b->execute();
        if ($option==1):
            return $c=$b->get_result();  
        endif;
    }

     function myconn($vt,$sql,$option) {
        $a=$sql;
        $b=$vt->prepare($a);
        $b->execute();
        if ($option==1):
            return $c=$b->get_result();  
        endif;
    }

    function masacek($dv){
        $dinnertables="select * from dinnertable";
        $result=$this->myprivconn($dv,$dinnertables,1);

        $emptydinnertablenumber = 0;
        $fulldinnertablenumber = 0;
      
        while ($masason=$result->fetch_assoc()) :
            $siparisler='select * from dinnertable_orders where dinnertable_id = '.$masason["id"].';';
            $this->myprivconn($dv,$siparisler,1)->num_rows==0 ? $color="success" : $color="danger";
            $this->myprivconn($dv,$siparisler,1)->num_rows==0 ? $emptydinnertablenumber++ : $fulldinnertablenumber++;
            echo '<div id="mas" class="col-md-2 col-sm-6 mr-2 mx-auto p-4 text-center">
                <a href="masadetay.php?tableid='.$masason["ID"].'">           
                <div class="bg-'.$color.' mx-auto p-4 text-center text-white" id="masa">'.$masason["Table_Number"].'</div></a>
                </div>';
        endwhile;
    }

    function masatoplam($dv){
        echo $result=$this->myprivconn($dv,"select * from dinnertable",1)->num_rows;
    }

    function siparistoplam($dv){
        echo $result=$this->myprivconn($dv,"select * from dinnertable_orders",1)->num_rows;
    }

    function masagetir($vt, $id) {
        $get="select * from dinnertable where id=$id";
        return $this->myprivconn($vt,$get,1);
    }

    function urungrup($db) {
        $se = "select * from kategoriler";
        $gelen=$this->myprivconn($db,$se,1);
        while($son = $gelen->fetch_assoc()) :
            echo '<a class="btn btn-dark mt-1" sectionId="'.$son["id"].'">'.$son["ad"].'</a><br>';
        endwhile;
    }

    function garsonbak($db) {
        $gelen = $this->myconn($db, "select * from users where status = 1", 1)->fetch_assoc();
        if ($gelen["Name"]!=""):
            echo $gelen["Name"];
            echo '<a href = "islemler.php?islem=cikis" class="m-3"> <kbd class="bg-info">EXIT</kbd></a>';
        else:
           echo "No logged Waiter!";
        endif;
    }
}