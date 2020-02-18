<?php ob_start();

$vt = new mysqli("localhost","root","","bbm473v9") or die ("Database Connection Error!");
$vt->set_charset("utf8");

class users {
    private function uyari($tip, $metin, $sayfa) {   
        echo '<div class="alert alert-'.$tip.'">'.$metin.'</div>';
        header('refresh:2, url = '.$sayfa.'');
    }

    private function genelsorgu($dv, $sql) {
        $sqlm = $dv->prepare($sql);
        $sqlm->execute();
        return $sqlson = $sqlm->get_result();
    }
    
    public function ciktiSorgusu($dv, $sql) {
        $sqlm = $dv->prepare($sql);
        $sqlm->execute();
        return $sqlson = $sqlm->get_result();
    }
    
    public function anliktoplamsiparis($vt) {
        $anliktoplamsiparis = $this->genelsorgu($vt, "select COUNT(*) from orders_menu")->fetch_assoc();
        echo $anliktoplamsiparis['COUNT(*)'];
    }

    public function anliktoplamsiparis2($vt) {
        $anliktoplamsiparis = $this->genelsorgu($vt, "select COUNT(*) from dinnertable_orders")->fetch_assoc();
        echo $anliktoplamsiparis['COUNT(*)'];
    }

    public function anliktoplamMasa($vt) {
        echo $this->genelsorgu($vt, "select * from dinnertable")->num_rows;
    }

    public function anliktoplamUrun($vt) {
        echo $this->genelsorgu($vt, "select * from items")->num_rows;
    }
    
    public function toplamGarson($vt) {
        echo $this->genelsorgu($vt, "select * from users where User_Type = 'waiter'")->num_rows;
    }

    public function toplamGarson2($vt) {
        echo $this->genelsorgu($vt, "select * from users where User_Type = 'waiter' and status = 1")->num_rows;
    }

    public function toplamGarson3($vt) {
        echo $this->genelsorgu($vt, "select * from users where User_Type = 'chief'")->num_rows;
    }

    public function toplamGarson4($vt) {
        echo $this->genelsorgu($vt, "select * from users where User_Type = 'chief' and status = 1")->num_rows;
    }

    public function takebill ($vt) {
        $tableid = $_GET["tableid"];
        if ($tableid != "" && is_numeric($tableid)):
            $tablenumber = mysqli_fetch_assoc($this->genelsorgu($vt, "select * from dinnertable where ID = '$tableid'"));
            $tablenumber2 = $tablenumber['DinnerTable_Number'];
            $this->genelsorgu($vt, "update dinnertable set Closed = 1 where id = '$tableid'");
            $this->genelsorgu($vt, "insert into dinnertable (DinnerTable_Number) values ('$tablenumber2')");
            $this->uyari("success", "Bill Paid!", "control.php?islem=orderyon");
        else:
            $this->uyari("danger", "Error!", "control.php?islem=orderyon");
        endif;
    }

    public function orderyon ($vt, $option) {
        $so=$this->genelsorgu($vt, "select * from dinnertable where Closed = 0 order by dinnertable_number");
        if ($option == 1):
            $aramabuton = $_POST["aramabuton"];
            $number = $_POST["number"];
            if ($aramabuton):
                $so=$this->genelsorgu($vt, "select * from dinnertable where DinnerTable_Number = '$number' and Closed = 0 order by dinnertable_number");
            else:
                $so=$this->genelsorgu($vt, "select * from dinnertable order by dinnertable_number");
            endif;
        endif;
        echo'<table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-1 table-dark">
                <thead>
                    <tr>
                        <th><form action="control.php?islem=orderyon2" method="post"><input type="search" name="number" class="form-control" placeholder="Table number..."/></th>
                        <th><input type="submit" name="aramabuton" value="Search" class="btn btn-danger"/></form></th>
                </thead>
                </table>
                <table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-4">
        <thead>
            <tr>
                <th scope="col">TABLE NAME</th>
                <th scope="col">BILL TOTAL</th>
                <th scope="col">ADD ORDER</th>
                <th scope="col">TAKE BILL</th>
            </tr>
        </thead>
        <tbody>';

        while ($result=$so->fetch_assoc()):
            echo'<tr>
                     <td>TABLE - '.$result["DinnerTable_Number"].'</td>
                     <td>'.$result["Bill_Total"].'</td>
                     <td><a href = "control.php?islem=addorder&tableid='.$result["ID"].'" class="btn btn-success"</a>ADD ORDER</td>
                     <td><a href = "control.php?islem=takebill&tableid='.$result["ID"].'" class="btn btn-dark" data-confirm="Are you sure?"</a>TAKE BILL</td>
                 </tr>';
        endwhile;
        echo '</tbody></table>';
    }

    public function addorder ($vt) {
        $so=$this->genelsorgu($vt, "select * from menu");
        $menus = array();
        while ($row=$so->fetch_assoc()):
            $menus[$row['ID']] = $row['Name'];
        endwhile;
        @$buton = $_POST["buton"];
        if ($buton) :
            @$menuid = htmlspecialchars($_POST["menuid"]);
            @$ordercount = htmlspecialchars($_POST["ordercount"]);
            @$tableid = htmlspecialchars($_POST["tableid"]);
            $userid = $this->User_ID($vt);

            $menucost = mysqli_fetch_assoc($this->genelsorgu($vt, "select * from menu where ID = $menuid"));
            $menucost2 = $menucost['Cost'];
            $this->genelsorgu($vt, "insert into orders (cost, status) values ('$menucost2',0)");
            $orderid = mysqli_fetch_assoc($this->genelsorgu($vt, "select * from orders where ID = (select max(id) from orders)"));
            $orderid2 = $orderid['ID'];
            $this->genelsorgu($vt, "insert into dinnertable_orders (DinnerTable_ID, Orders_ID, Date) values ('$tableid', '$orderid2', CURDATE())");
            $this->genelsorgu($vt, "insert into orders_waiter (Orders_ID, Users_Waiter_ID, Date) values ('$orderid2','$userid', CURDATE())");
            for ($x = 0; $x < $ordercount; $x++) {
                $this->genelsorgu($vt, "insert into orders_menu (Orders_ID, Menu_ID) values ('$orderid2','$menuid')");
            }
            $this->uyari("success", "Order Added!", "control.php?islem=orderyon");

        else:
            $tableid = $_GET["tableid"];
            echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">
                    <form action = "" method = "post">
                        <div class="col-md-12 table-light border-bottom"><h4>ADD ORDER</h4></div>
                        <div class="col-md-12 table-light">CHOOSE MENU';
                        echo '<select name = "menuid" class = "col-md-12 mt-3">';
                            foreach($menus as $key => $value):
                                echo '<option value = "'.$key.'">'.$value.'</option>';
                            endforeach;
                            echo '</select>';

            echo'</div><div class="col-md-12 mt-2 table-light">ORDER COUNT <select name="ordercount">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                    </select></div>
                        <div class="col-md-12 table-light"><input name = "buton" value = "Add" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                        <input type = "hidden" name = "tableid" value = "'.$tableid.'"/>
                    </form>
                </div>';
        endif;
    }

    public function masayon ($vt, $option) {
        $so=$this->genelsorgu($vt, "select * from dinnertable where Closed = 0 order by dinnertable_number");
        if ($option == 1):
            $aramabuton = $_POST["aramabuton"];
            $number = $_POST["number"];
            if ($aramabuton):
                $so=$this->genelsorgu($vt, "select * from dinnertable where DinnerTable_Number = '$number' and Closed = 0 order by dinnertable_number");
            endif;
            else:
                $so=$this->genelsorgu($vt, "select * from dinnertable where Closed = 0 order by dinnertable_number");
        endif;
        echo'<table class="table text-center table-striped table-bordered mx-auto col-md-6 mt-1 table-dark">
                <thead>
                    <tr>
                        <th><form action="control.php?islem=tablesonuc" method="post"><input type="search" name="number" class="form-control" placeholder="Table number..."/></th>
                        <th><input type="submit" name="aramabuton" value="Search" class="btn btn-danger"/></form></th>
                </thead>
                </table>
                <table class="table text-center table-striped table-bordered mx-auto col-md-6 mt-4">
        <thead>
            <tr>
                <th scope="col"> <a href = "control.php?islem=addtable" class="btn btn-success ">+</a>TABLE NAME</th>
                <th scope="col">UPDATE</th>
                <th scope="col">DELETE</th>
            </tr>
        </thead>
        <tbody>';

        while ($result=$so->fetch_assoc()):
            echo'<tr>
                     <td>TABLE - '.$result["DinnerTable_Number"].'</td>
                     <td><a href = "control.php?islem=updatetable&tableid='.$result["ID"].'" class="btn btn-warning"</a>UPDATE</td>
                     <td><a href = "control.php?islem=deletetable&tableid='.$result["ID"].'" class="btn btn-danger" data-confirm="Are you sure?"</a>DELETE</td>
                 </tr>';
        endwhile;
        echo '</tbody></table>';
    }

    public function deletetable ($vt) {
        $tableid = $_GET["tableid"];
        if ($tableid != "" && is_numeric($tableid)):
            $this->genelsorgu($vt, "delete from dinnertable where id=$tableid");
            $this->uyari("success", "Dinnertable Deleted!", "control.php?islem=masayon");
        else:
            $this->uyari("danger", "Error!", "control.php?islem=masayon");
        endif;
    }

    public function updatetable($vt) {
        @$buton = $_POST["buton"];
        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';
        if ($buton) :
            @$tablename = htmlspecialchars($_POST["tablename"]);
            @$tableid = htmlspecialchars($_POST["tableid"]);
            if ($tablename == "" || $tableid == "") :
                $this->uyari("danger", "Error!", "control.php?islem=masayon");
            else:
                $this->genelsorgu($vt, "update dinnertable set dinnertable_number = '$tablename' where id = $tableid");
                $this->uyari("success", "Table Updated", "control.php?islem=masayon");
            endif;

        else:
            $tableid = $_GET["tableid"];
            $aktar = $this->genelsorgu($vt, "select * from dinnertable where id = $tableid and closed = 0")->fetch_assoc();

            echo '<form action = "" method = "post">
                    <div class="col-md-12 table-light border-bottom"><h4>Update Table</h4></div>
                    <div class="col-md-12 table-light">TABLE - '.$aktar["DinnerTable_Number"].'<input type = "text" name = "tablename" class = "form-control mt-3" value = "'.$aktar["DinnerTable_Number"].'"/></div>
                    <div class="col-md-12 table-light"><input name = "buton" value = "Update" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                    <input type = "hidden" name = "tableid" value = "'.$aktar["ID"].'"/></form>';
        endif;
        echo '</div>';
    }

    public function addtable ($vt) {
        @$buton = $_POST["buton"];
        if ($buton) :
            @$tablename = htmlspecialchars($_POST["tablename"]);
            if ($tablename == "") :
                $this->uyari("danger", "This place cannot be empty!", "control.php?islem=masayon");
            else:
                $this->genelsorgu($vt, "insert into dinnertable (DinnerTable_Number) values ('$tablename')");
                $this->uyari("success", "Table Added!", "control.php?islem=masayon");
            endif;
        else:
            echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">
                    <form action = "" method = "post">
                        <div class="col-md-12 table-light border-bottom"><h4>Add Table</h4></div>
                        <div class="col-md-12 table-light"><input type = "text" name = "tablename" placeholder="Table Number" class = "form-control mt-3"/></div>
                        <div class="col-md-12 table-light"><input name = "buton" value = "Add" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                    </form>
                </div>';
        endif;
    }

    public function urunyon ($vt, $option) {
        if ($option == 1):
            $aramabuton = $_POST["aramabuton"];
            $urun = $_POST["urun"];
            if ($aramabuton):
                $so=$this->genelsorgu($vt, "select * from items where Name LIKE '%$urun%'");
            endif;
            else:
                $so=$this->genelsorgu($vt, "select * from items");
        endif;
        echo '<table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-2 table-dark">
                <thead>
                    <tr>
                        <th><form action="control.php?islem=aramasonuc" method="post"><input type="search" name="urun" class="form-control" placeholder="Item name..."/></th>
                        <th><input type="submit" name="aramabuton" value="Search" class="btn btn-danger"/></form></th>
                </thead>
                </table>
                <table class="table text-center table-bordered table-hover mx-auto col-md-12 mt-4">
                <thead>
                    <tr>
                        <th scope="col"> <a href = "control.php?islem=urunekle" class="btn btn-success">+</a>ITEM NAME</th>
                        <th scope="col">ITEM COUNT</th>
                        <th scope="col">EXPIRTY DATE</th>
                        <th scope="col">UPDATE</th>
                        <th scope="col">DELETE</th>
                    </tr>
                </thead><tbody>';

        while ($result=$so->fetch_assoc()):
            echo    '<tr>
                        <td>'.$result["Name"].'</td>
                        <td>'.$result["Count"].'</td>
                        <td>'.$result["Expirty_Date"].'</td>
                        <td><a href = "control.php?islem=urunguncelle&urunid='.$result["ID"].'" class="btn btn-warning"</a>Update</td>
                        <td><a href = "control.php?islem=urunsil&urunid='.$result["ID"].'" class="btn btn-danger" data-confirm="Are you sure?"</a>Delete</td>
                    </tr>';
            
        endwhile;

        echo '</tbody>
            </table>';

    }

    public function urunsil ($vt) {
        @$urunid = $_GET["urunid"];

        if ($urunid != "" && is_numeric($urunid)):

            $this->genelsorgu($vt, "delete from items where id=$urunid");
            $this->uyari("success", "Items Deleted!", "control.php?islem=urunyon");

        else:
            $this->uyari("danger", "Error!", "control.php?islem=urunyon");

        endif;
    }

    public function urunguncelle($vt) {

        @$buton = $_POST["buton"];

        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';

        if ($buton) :
            @$itemname = htmlspecialchars($_POST["itemname"]);
            @$itemid = htmlspecialchars($_POST["itemid"]);
            @$itemcount = htmlspecialchars($_POST["itemcount"]);
            @$itemdate = htmlspecialchars($_POST["itemdate"]);

            if ($itemname == "" || $itemid == "" || $itemcount == "") :
                $this->uyari("danger", "Error!", "control.php?islem=urunyon");
            else:
                $this->genelsorgu($vt, "update items set name = '$itemname', count = '$itemcount', expirty_date = '$itemdate' where id = $itemid");
                $this->uyari("success", "Item Updated!", "control.php?islem=urunyon");
            endif;

        else:
            $urunid = $_GET["urunid"];
            $aktar = $this->genelsorgu($vt, "select * from items where id = $urunid")->fetch_assoc();

            ?>
                <form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
            <?php

            echo '<div class="col-md-12 table-light border-bottom"><h4>UPDATE ITEM</h4></div>
                <div class="col-md-12 table-light">Item Name<input type = "text" name = "name" class = "form-control mt-2" value = "'.$aktar["Name"].'"/></div>
                <div class="col-md-12 table-light">Item Price<input type = "text" name = "count" class = "form-control mt-2" value = "'.$aktar["Count"].'"/></div>
                <div class="col-md-12 table-light">Item Expirty Date<form action="control.php?islem=rapor&tar=tarih" method="post">
                <input type="date" name = "date" class="form-control col-md-12 mt-2" value = "'.$aktar["Expirty_Date"].'"></div>
                <div class="col-md-12 table-light"><input name = "buton" value = "Update" type = "submit" class = "btn btn-success mt-4 mb-3"/></div>
                <input type = "hidden" name = "urunid" value = "'.$urunid.'"/></form>';
        endif;

        echo '</div>';
    }

    public function urunekle($vt) {

            @$buton = $_POST["buton"];
    
            echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';
    
            if ($buton) :
                    // db işlemleri
                    @$name = htmlspecialchars($_POST["name"]);
                    @$count = htmlspecialchars($_POST["count"]);
                    @$date = htmlspecialchars($_POST["date"]);

                    if ($name == "" || $count == "") :
                        $this->uyari("danger", "Error!", "control.php?islem=urunyon");
    
                    else:
                        $this->genelsorgu($vt, "insert into items (Name, Count, Expirty_Date) values ('$name', '$count', '$date')");
                        $this->uyari("success", "Item Added!", "control.php?islem=urunyon");
                    endif;
                    else:
                       
                ?>
                        <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post">
                
                <?php
    
                        echo '<div class="col-md-12 table-light border-bottom"><h4>ÜRÜN EKLE</h4></div>
                            <div class="col-md-12 table-light">Item Name<input type = "text" name = "name" class = "form-control mt-3"/></div>
                            <div class="col-md-12 table-light">Item Count<input type = "text" name = "count" class = "form-control mt-3"/></div>
                            <div class="col-md-12 table-light">Item Expirty Date
                            <form action="control.php?islem=rapor&tar=tarih" method="post">
                                <input type="date" name = "date" class="form-control col-md-12">
                            </div>
                            <div class="col-md-12 table-light"><input name = "buton" value = "EKLE" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                            </form>';
    
                endif;
    
            echo '</div>';
        
        }

    public function menuyon ($vt, $option) {
        $so=$this->genelsorgu($vt, "select * from menu");
        $so2=$this->genelsorgu($vt, "select * from dinners");
        $so3=$this->genelsorgu($vt, "select * from items");

        $items = array();
        $dinners = array();
        while ($row=$so3->fetch_assoc()):
            $items[$row['ID']] = $row['Name'];
        endwhile;
        while ($row=$so2->fetch_assoc()):
            $dinners[$row['ID']] = $row['Name'];
        endwhile;

        if ($option == 1):
            $aramabuton = $_POST["aramabuton"];
            $name = $_POST["name"];
            if ($aramabuton):
                $so=$this->genelsorgu($vt, "select * from menu where Name LIKE '%$name%'");
            endif;
            else:
                $so=$this->genelsorgu($vt, "select * from menu");
        endif;
        echo '<table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-2 table-dark">
                <thead>
                    <tr>
                        <th><form action="control.php?islem=menuresult" method="post"><input type="search" name="name" class="form-control" placeholder="Menu Name..."/></th>
                        <th><input type="submit" name="aramabuton" value="Search" class="btn btn-danger"/></form></th>
                </thead>
                </table>
                <table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-4">
        <thead>
            <tr>
                <th scope="col"> <a href = "control.php?islem=menuekle" class="btn btn-success">+</a>MENU NAME</th>
                <th scope="col">DINNER NAME</th>
                <th scope="col">DRINK NAME</th>
                <th scope="col">COST</th>
                <th scope="col">ADD IMAGE</th>
                <th scope="col">UPDATE</th>
                <th scope="col">DELETE</th>
            </tr>
        </thead>
        <tbody>';
        while ($result=$so->fetch_assoc()):
            echo'<tr>
                 <td>'.$result["Name"].'</td>
                 <td>'.$dinners[$result["Dinners_ID"]].'</td>
                 <td>'.$items[$result["Drink_ID"]].'</td>
                 <td>'.$result["Cost"].'</td>
                 <td><a href = "control.php?islem=menuimage&menuid='.$result["ID"].'" class="btn btn-success"</a>Add Image</td>
                 <td><a href = "control.php?islem=menuguncelle&menuid='.$result["ID"].'" class="btn btn-warning"</a>Update</td>
                 <td><a href = "control.php?islem=menusil&menuid='.$result["ID"].'" class="btn btn-danger" data-confirm="Are you sure?"</a>Delete</td></tr>';
        endwhile;
        echo '</tbody></table>';
    }

    public function menuimage ($vt) {
        @$buton = $_POST['upload'];
        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';
        if ($buton) :
            @$menuid = htmlspecialchars($_POST["menuid"]);
            $name       = $_FILES['file']['name'];
            $temp_name  = $_FILES['file']['tmp_name'];
            if(isset($name)):
                if(!empty($name)):
                    $location = '../uploadimages/';
                    $filename = $location.$name;
                    if(move_uploaded_file($temp_name, $filename)):
                        $this->genelsorgu($vt, "update menu set ImagePath = '.$filename.' where ID = $menuid;");
                        $this->uyari("success", "File Uploaded!", "control.php?islem=menuyon");
                    endif;endif;endif;
        else:
            $menuid = $_GET["menuid"];
            ?>
                <form action = "<?php $_SERVER['PHP_SELF'];?>" method = "post" enctype="multipart/form-data">
            <?php
                    echo '<div class="col-md-12 table-light border-bottom"><h4 class = "mt-2">ADD IMAGE</h4></div>
                        <input type = "file" name = "file">
                        <input type = "submit" name ="upload" value="Submit" class = "btn btn-success mt-3 mb-3">
                        <input type = "hidden" name = "menuid" value = "'.$menuid.'"/>
                        </form>
                    </form>';
        endif;
        echo '</div>';
    }

    public function menusil ($vt) {
        $menuid = $_GET["menuid"];

        if ($menuid != "" && is_numeric($menuid)):
            $this->genelsorgu($vt, "delete from menu where id=$menuid");
            $this->uyari("success", "Menu Deleted!", "control.php?islem=menuyon");
        else:
            $this->uyari("danger", "Error!", "control.php?islem=menuyon");
        endif;
    }

    public function menuekle ($vt) {
        $so2=$this->genelsorgu($vt, "select * from dinners");
        $so3=$this->genelsorgu($vt, "select * from items");

        $items = array();
        $dinners = array();
        while ($row=$so3->fetch_assoc()):
            $items[$row['ID']] = $row['Name'];
        endwhile;
        while ($row=$so2->fetch_assoc()):
            $dinners[$row['ID']] = $row['Name'];
        endwhile;

        @$buton = $_POST["buton"];
        if ($buton) :
                @$menuname = htmlspecialchars($_POST["menuname"]);
                @$menucost = htmlspecialchars($_POST["cost"]);
                @$dinnerid = htmlspecialchars($_POST["dinnerid"]);
                @$drinkid = htmlspecialchars($_POST["drinkid"]);

                if ($menuname == "" || $menucost == "" || $dinnerid == "" || $drinkid == "") :
                    $this->uyari("danger", "Fields cannot be empty!", "control.php?islem=menuyon");
                else:
                    $this->genelsorgu($vt, "insert into menu (name, cost, dinners_id, drink_id) values ('$menuname', '$menucost', '$dinnerid', '$drinkid')");
                    $this->uyari("success", "Menu Added!", "control.php?islem=menuyon");
                endif;
        else:
            ?>

            <div class="col-md-3 text-center mx-auto mt-5 table-bordered">
                    <form action = "<?php $_SERVER['PHP_SELF'];?>" method = "post">
            <?php
                    echo '<div class="col-md-12 table-light border-bottom"><h4 class = "mt-2">ADD MENU</h4></div>
                        <div class="col-md-12 table-light"><input type = "text" name = "menuname" class = "form-control mt-3" placeholder="Menu Name"/></div>
                        <div class="col-md-12 table-light"><input type = "text" name = "cost" class = "form-control mt-3" placeholder="Cost"/></div>    
                        <div class="col-md-12 table-light">';
                            echo '<select name = "dinnerid" class = "col-md-12 mt-3">';
                            foreach($dinners as $key => $value):
                                echo '<option value = "'.$key.'">'.$value.'</option>';
                            endforeach;
                            echo '</select>';
                            echo'</div><div class="col-md-12 table-light">';

                            echo '<select name = "drinkid" class = "col-md-12 mt-3">';
                            foreach($items as $key => $value):
                                echo '<option value = "'.$key.'">'.$value.'</option>';
                            endforeach;
                            echo '</select>';
                            echo'</div><div
                        <div class="col-md-12 table-light"><input name = "buton" value = "Ekle" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
        
                    </form>';

        endif;
        
        echo '</div>';
    }

    public function menuguncelle($vt) {
        @$buton = $_POST["buton"];

        $so2=$this->genelsorgu($vt, "select * from dinners");
        $so3=$this->genelsorgu($vt, "select * from items");

        $items = array();
        $dinners = array();
        while ($row=$so3->fetch_assoc()):
            $items[$row['ID']] = $row['Name'];
        endwhile;
        while ($row=$so2->fetch_assoc()):
            $dinners[$row['ID']] = $row['Name'];
        endwhile;

        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';
        if ($buton) :
                @$menuid = htmlspecialchars($_POST["menuid"]);
                @$menuname = htmlspecialchars($_POST["menuname"]);
                @$menucost = htmlspecialchars($_POST["cost"]);
                @$dinnerid = htmlspecialchars($_POST["dinnerid"]);
                @$drinkid = htmlspecialchars($_POST["drinkid"]);

                if ($menuname == "" || $menucost == "" || $dinnerid == "" || $drinkid == "") :
                    $this->uyari("danger", "Fields cannot be empty!", "control.php?islem=menuyon");

                else:
                    $this->genelsorgu($vt, "update menu set name ='$menuname', cost = '$menucost', 
                                    dinners_id = '$dinnerid', drink_id = '$drinkid' where id = $menuid");
                    $this->uyari("success", "Menu Updated", "control.php?islem=menuyon");
                endif;
        else:
                $menuid = $_GET["menuid"];
                $aktar = $this->genelsorgu($vt, "select * from menu where id = $menuid")->fetch_assoc();
            ?>
                    <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post">
            <?php
                echo '<div class="col-md-12 table-light border-bottom"><h4>UPDATE MENU</h4></div>
                        <div class="col-md-12 table-light">Kategori Adı<input type = "text" name = "menuname" class = "form-control mt-3" value = "'.$aktar["Name"].'"/></div>        
                        <div class="col-md-12 table-light"><input type = "text" name = "cost" class = "form-control mt-3" value = "'.$aktar["Cost"].'"/></div>    
                        <div class="col-md-12 table-light">';
                            echo '<select name = "dinnerid" class = "col-md-12 mt-3">';
                            foreach($dinners as $key => $value):
                                echo '<option value = "'.$key.'">'.$value.'</option>';
                            endforeach;
                            echo '</select>';
                            echo'</div><div class="col-md-12 table-light">';

                            echo '<select name = "drinkid" class = "col-md-12 mt-3">';
                            foreach($items as $key => $value):
                                echo '<option value = "'.$key.'">'.$value.'</option>';
                            endforeach;
                            echo '</select>';
                echo '</div><div
                        <div class="col-md-12 table-light"><input name = "buton" value = "Update" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                        <input type = "hidden" name = "menuid" value = "'.$menuid.'"/></form>';
        endif;
        echo '</div>';
    }

    public function garsonyon ($vt, $option) {
        if ($option == 1):
            $aramabuton = $_POST["aramabuton"];
            $name = $_POST["name"];
            if ($aramabuton):
                $so=$this->genelsorgu($vt, "select * from users where user_name LIKE '%$name%' and user_type <> 'manager'");
            endif;
            else:
                $so=$this->genelsorgu($vt, "select * from users where user_type <> 'manager' ");
        endif;
        echo '<table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-2 table-dark">
                <thead>
                    <tr>
                        <th><form action="control.php?islem=employeeresult" method="post"><input type="search" name="name" class="form-control" placeholder="User name..."/></th>
                        <th><input type="submit" name="aramabuton" value="Search" class="btn btn-danger"/></form></th>
                </thead>
                </table>
                <table class="table text-center table-striped table-bordered mx-auto col-md-12 mt-6">
        <thead>
            <tr>
                <th scope="col"> <a href = "control.php?islem=garsonekle" class="btn btn-success">+</a>USER NAME</th>
                <th scope="col">PASSWORD</th>
                <th scope="col">NAME</th>
                <th scope="col">USER TYPE</th>
                <th scope="col">UPDATE</th>
                <th scope="col">DELETE</th>
            </tr>
        </thead><tbody>';

        while ($result=$so->fetch_assoc()):
            echo'<tr>
                        <td>'.$result["User_Name"].'</td>
                        <td>'.$this->coz($result["Password"]).'</td>
                        <td>'.$result["Name"].'</td>
                        <td>'.$result["User_Type"].'</td>
                        <td><a href = "control.php?islem=garsonguncelle&employeeid='.$result["ID"].'" class="btn btn-warning"</a>Update</td>
                        <td><a href = "control.php?islem=garsonsil&employeeid='.$result["ID"].'" class="btn btn-danger" data-confirm="Garsonu silmek istediğinize emin misiniz?">Delete</a></td>
                    </tr>';
        endwhile;
        echo '</tbody></table>';
    }

    public function garsonsil ($vt) {
        $employeeid = $_GET["employeeid"];
        if ($employeeid != "" && is_numeric($employeeid)):
            $this->genelsorgu($vt, "delete from users where id=$employeeid");
            $this->uyari("success", "Employee Deleted", "control.php?islem=garsonyon");
        else:
            $this->uyari("danger", "Error!", "control.php?islem=garsonyon");
        endif;
    }

    public function garsonekle ($vt) {
        @$buton = $_POST["buton"];
        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';
        if ($buton) :
                @$employeeUsername = htmlspecialchars($_POST["employeeUsername"]);
                @$employeePassword = htmlspecialchars($_POST["employeePassword"]);
                @$employeeName = htmlspecialchars($_POST["employeeName"]);
                @$employeeType = htmlspecialchars($_POST["employeeType"]);

                if ($employeeUsername == "" || $employeePassword == "" || $employeeName == "" || $employeeType == "") :
                    $this->uyari("danger", "Places cannot be empty!", "control.php?islem=garsonyon");

                else:
                    $this->genelsorgu($vt, "insert into users (User_Name, Password, Name, User_Type) 
                        values ('$employeeUsername', '$employeePassword', '$employeeName', '$employeeType')");
                    $this->uyari("success", "Employee Added!", "control.php?islem=garsonyon");
                endif;
        else:
            ?>
                    <form action = "<?php $_SERVER['PHP_SELF']; ?>" method = "post">
            <?php
                    echo '<div class="col-md-12 table-light border-bottom"><h4>Add Employee</h4></div>
                            <div class="col-md-12 table-light">User Name<input type = "text" name = "employeeUsername" class = "form-control mt-3" /></div>
                            <div class="col-md-12 table-light">Password<input type = "text" name = "employeePassword" class = "form-control mt-3" /></div>
                            <div class="col-md-12 table-light">Name<input type = "text" name = "employeeName" class = "form-control mt-3" /></div>
                            <div class="col-md-12 table-light">Type<input type = "text" name = "employeeType" class = "form-control mt-3" /></div>
                            <div class="col-md-12 table-light"><input name = "buton" value = "Add" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                    </form>';
        endif;
    }

    public function garsonguncelle($vt) {

        @$buton = $_POST["buton"];

        echo '<div class="col-md-3 text-center mx-auto mt-5 table-bordered">';

        if ($buton) :
                @$employeeUsername = htmlspecialchars($_POST["employeeUsername"]);
                @$employeePassword = htmlspecialchars($_POST["employeePassword"]);
                @$employeeName = htmlspecialchars($_POST["employeeName"]);
                @$employeeType = htmlspecialchars($_POST["employeeType"]);
                @$employeeid = htmlspecialchars($_POST["employeeid"]);
                $sifrelenmis = $this->sifrele($employeePassword);

                if ($employeeUsername == "" || $employeePassword == "" || $employeeName == "" || $employeeType == "") :
                    $this->uyari("danger", "Places cannot be empty!", "control.php?islem=garsonyon");

                else:
                    $this->genelsorgu($vt, "update users set User_Name = '$employeeUsername', Password = '$sifrelenmis', 
                        Name = '$employeeName', User_Type = '$employeeType' where id = $employeeid");
                    $this->uyari("success", "Employee Updated!", "control.php?islem=garsonyon");
                endif;
        else:
            $employeeid = $_GET["employeeid"];
            $aktar = $this->genelsorgu($vt, "select * from users where id = $employeeid")->fetch_assoc();
            ?>
            <form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
            
            <?php
                echo '<div class="col-md-12 table-light border-bottom"><h4>UPDATE EMPLOYEE</h4></div>
                            <div class="col-md-12 table-light">Employee Username<input type = "text" name = "employeeUsername" class = "form-control mt-3" value = "'.$aktar["User_Name"].'"/></div>
                            <div class="col-md-12 table-light">Employee Password<input type = "text" name = "employeePassword" class = "form-control mt-3" value = "'.$this->coz($aktar["Password"]).'"/></div>
                            <div class="col-md-12 table-light">Employee Name<input type = "text" name = "employeeName" class = "form-control mt-3" value = "'.$aktar["Name"].'"/></div>
                            <div class="col-md-12 table-light">';
                            $katcek = [["User_Type" => "chief"], ["User_Type" => "waiter"]];
                            echo 'Employee Type  <select name = "employeeType" class = "mt-3">';
                            foreach ($katcek as $katson):
                                echo '<option value = "'.$katson["User_Type"].'">'.$katson["User_Type"].'</option>';
                            endforeach;
                            echo '</select>';
                echo'</div>
                            <div class="col-md-12 table-light"><input name = "buton" value = "Update" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                            <input type = "hidden" name = "employeeid" value = "'.$employeeid.'"/></form>';
        endif;
        echo '</div>';
    }

    public function garsonper($vt) {
            @$tarih = $_GET["tar"];
            switch ($tarih):
                case "ay":
                    $son = $this->genelsorgu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report where date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                                        group by Waiter_ID order by COUNT(Waiter_ID) desc;");
                break;
                case "hafta":
                   $son = $this->genelsorgu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report where YEARWEEK(date) = YEARWEEK(CURRENT_DATE)
                                        group by Waiter_ID order by COUNT(Waiter_ID) desc;");
                break;
                case "tarih":
                    $tarih1 = $_POST["tarih1"];
                    $tarih2 = $_POST["tarih2"];
                    echo '<div class = "alert alert-info text-center mx-auto mt-4">
                       SELECTED DATE: '.$tarih1.' - '.$tarih2.'</div>';
                    $son = $this->genelsorgu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Waiter_ID order by COUNT(Waiter_ID) desc;");
                break;
                default;
                    $son = $this->genelsorgu($vt, "select distinct Waiter_Name, count(Waiter_ID) as 'Order_Number' 
                                        from report group by Waiter_ID order by COUNT(Waiter_ID) desc;");
                break;
            endswitch;
     
            echo '<table class = "table text-center table-light table-bordered mx-auto mt-4 table-striped col-md-10">
                
                    <thead>
                        <tr>
                            <th colspan="5">';
            
                                if(@$tarih1!="" || @$tarih2!=""):
                                    echo '<a href="cikti.php?islem=garsoncikti&tar1='.$tarih1.'&tar2='.$tarih2.'" onclick="ortasayfa(this.href,\'mywindow\',\'900\',\'800\',\'yes\');return false" class = "btn btn-warning">PRINT OR CONVERT PDF</a>';
                                    echo '<a href="garsonexcel.php?tar1='.$tarih1.'&tar2='.$tarih2.'" class = "btn btn-info">CONVERT EXCEL</a>';
                                endif;

                            echo '<thead>
                            <tr>
                                <th><a href="control.php?islem=garsonper&tar=hafta">This Week</a></th>
                                <th><a href="control.php?islem=garsonper&tar=ay">This Month</a></th>
                                <form action="control.php?islem=garsonper&tar=tarih" method="post">
                                <th><input type="date" name = "tarih1" class="form-control col-md-10"></th>
                                <th><input type="date" name = "tarih2" class="form-control col-md-10"></th>        
                                <th><input name="buton" type="submit" class="btn btn-danger" value="REPORT"></form></th>
                            </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan = "5">
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
    }

    public function Passworddegistir ($vt) {
        @$buton = $_POST["buton"];
        if ($buton) :
            @$eskiPassword = htmlspecialchars($_POST["eskiPassword"]);
            @$yeni1 = htmlspecialchars($_POST["yeni1"]);
            @$yeni2 = htmlspecialchars($_POST["yeni2"]);

            if ($eskiPassword == "" || $yeni1 == "" || $yeni2 == "") :
                $this->uyari("danger", "Fields cannot be empty!", "control.php?islem=Passworddegistir");
            else:
                $eskiPasswordson = $this->sifrele($eskiPassword);
                if($this->genelsorgu($vt, "select * from users where Password = '$eskiPasswordson' ")->num_rows == 0) :
                $this->uyari("danger", "Incorrect old password", "control.php?islem=Passworddegistir");
                elseif($yeni1 != $yeni2):
                $this->uyari("danger", "New passwords are not same", "control.php?islem=Passworddegistir");
                else:
                    $yeniPasswordson = $this->sifrele($yeni1);
                    $id=$this->coz($_COOKIE["ID"]);
                    $this->genelsorgu($vt, "update users set Password = '$yeniPasswordson' where id=$id");
                    $this->uyari("success", "Password Changed!", "control.php");
                endif;
            endif;
        else:
            ?>
            <div class="col-md-3 text-center mx-auto mt-5 table-bordered"><form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
            <?php
            echo '<div class="col-md-12 table-light border-bottom"><h4>CHANGE PASSWORD</h4></div>
                  <div class="col-md-12 table-light"><input type = "text" name = "eskiPassword" class = "form-control mt-3" require placeholder="Old Password"/></div>
                  <div class="col-md-12 table-light"><input type = "text" name = "yeni1" class = "form-control mt-3" require placeholder="New Password"/></div>
                  <div class="col-md-12 table-light"><input type = "text" name = "yeni2" class = "form-control mt-3" require placeholder="New Password Enter Again"/></div>
                  <div class="col-md-12 table-light"><input name = "buton" value = "Değiştir" type = "submit" class = "btn btn-success mt-3 mb-3"/></div>
                  </form></div>';
        endif;
    }

    public function rapor($vt) {
        @$tarih = $_GET["tar"];
        switch ($tarih):
            case "bugun":
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report where date = CURDATE() group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where date = CURDATE()
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            case "dun":
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report where date =  DATE_SUB(CURDATE(), INTERVAL 1 DAY) group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            case "hafta":
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report where YEARWEEK(date) = YEARWEEK(CURRENT_DATE) group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where YEARWEEK(date) = YEARWEEK(CURRENT_DATE)
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            case "ay":
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report where date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            case "tum":
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            case "tarih":
                $tarih1 = $_POST["tarih1"];
                $tarih2 = $_POST["tarih2"];
                echo '<div class = "alert alert-info text-center mx-auto mt-4">
                Tarih Seçimi: '.$tarih1.' - '.$tarih2.'
                </div>';
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                        as 'Order_Number', sum(Cost) as 'Revenue' from report where DATE(date) 
                                        BETWEEN '$tarih1' AND '$tarih2' group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report where DATE(date) BETWEEN '$tarih1' AND '$tarih2'
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            default;
                $son = $this->genelsorgu($vt, "select distinct Table_Number, count(Table_Number) 
                                    as 'Order_Number', sum(Cost) as 'Revenue' from report group by table_number order by SUM(Cost) desc;");
                $son2 = $this->genelsorgu($vt, "select distinct Menu_Name, count(Menu_Name) 
                                        as 'Menu_Number', sum(Cost) as 'Revenue' from report 
                                        group by Menu_Name order by count(Menu_Name) desc;");
            break;
            endswitch;
            echo '<table class = "table text-center table-light table-bordered mx-auto mt-4 table-striped col-md-10">
                    <thead>
                        <tr>
                            <th colspan="8">';
                                if(@$tarih1!="" || @$tarih2!=""):
                                    echo '<a href="cikti.php?islem=ciktial&tar1='.$tarih1.'&tar2='.$tarih2.'" onclick="ortasayfa(this.href,\'mywindow\',\'900\',\'800\',\'yes\');return false" class = "btn btn-warning">PRINT OR CONVERT PDF</a>';
                                    echo '<a href="excel.php?tar1='.$tarih1.'&tar2='.$tarih2.'" class = "btn btn-info">CONVERT EXCEL</a>';
                                endif;
                            echo '</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th><a href="control.php?islem=rapor&tar=bugun">Today</a></th>
                            <th><a href="control.php?islem=rapor&tar=dun">Yesterday</a></th>
                            <th><a href="control.php?islem=rapor&tar=hafta">This Week</a></th>
                            <th><a href="control.php?islem=rapor&tar=ay">This Month</a></th>
                            <th><a href="control.php?islem=rapor&tar=tum">All Time</a></th>
                            <th colspan="2"><form action="control.php?islem=rapor&tar=tarih" method="post">
                                <input type="date" name = "tarih1" class="form-control col-md-10">
                                <input type="date" name = "tarih2" class="form-control col-md-10">
                            </th>
                            <th><input name="buton" type="submit" class="btn btn-danger" value="REPORT"></form></th>
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
                                        <th colspan="1">ORDER COUNT</th>
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
                                <td colspan="1">'.$toplamadet.'</td>
                                <td colspan="1">'. number_format($toplamhasilat,2,'.','.').'</td>
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
                                        <th colspan="1">ORDERED MENU COUNT</th>
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
                                <td colspan="1">'.$toplamadet2.'</td>
                                <td colspan="1">'.number_format($toplamhasilat2,2,'.','.').'</td>
                            </tr>
                        
                            </tbody> 
                        </table>    
                    </th>
                </tr>
            </tbody>
        </table>';
    }

    public function sifrele($veri){
       return base64_encode(gzdeflate(gzcompress(serialize($veri))));
    }

    public function coz($veri){
       return unserialize(gzuncompress(gzinflate(base64_decode($veri))));
    }

    public function User_Name($db) {
        $id=$this->coz($_COOKIE["ID"]);
        $sql = "select * from users where id=$id";
        $gelensonuc = $this->genelsorgu($db, $sql);
        $b = $gelensonuc->fetch_assoc();
        return $b["Name"];
    }

    public function User_Type($db) {
        $id=$this->coz($_COOKIE["ID"]);
        $sql = "select * from users where id=$id";
        $gelensonuc = $this->genelsorgu($db, $sql);
        $b = $gelensonuc->fetch_assoc();
        return $b["User_Type"];
    }

    public function User_ID($db) {
        $id=$this->coz($_COOKIE["ID"]);
        $sql = "select * from users where id=$id";
        $gelensonuc = $this->genelsorgu($db, $sql);
        $b = $gelensonuc->fetch_assoc();
        return $b["ID"];
    }

    public function cikis ($r, $deger) {
        $id=$this->coz($_COOKIE["ID"]);
        $sql = "update users set Status=0 where id=$id";
        $sor=$r->prepare($sql);
        $sor->execute();
        $deger=$this->sifrele($deger);
        setcookie("kul", $deger, time() - 10);
        setcookie("ID", $_COOKIE["ID"], time() - 10);
        $this->uyari("success", "Logging out", "index.php");
    }
  
    public function logincontrol($r, $k, $s){
        $sonhal=$this->sifrele($s);
        $sql = "select * from users where User_Name = '$k' and Password = '$sonhal'";
        $sor=$r->prepare($sql);
        $sor->execute();
        $sonbilgi=$sor->get_result();
        $veri=$sonbilgi->fetch_assoc();

        if ($sonbilgi->num_rows == 0) :
                $this->uyari("danger", "Incorrect Login", "index.php");

        else:
            $sql = "update users set status=1 where User_Name = '$k' and Password = '$sonhal' and User_Type = 'manager'";
            $sor=$r->prepare($sql);
            $sor->execute();
            $this->uyari("success", "Welcome", "control.php");
            $kulson=$this->sifrele($k);
            setcookie("kul", $kulson, time() + 60*60*24);
            $id=$this->sifrele($veri["ID"]);
            setcookie("ID", $id, time() + 60*60*24);
        endif;
    }

    public function waitercontrol($r, $k, $s){
        $sonhal=$this->sifrele($s);
        $sql = "select * from users where User_Name = '$k' and Password = '$sonhal' and User_Type = 'waiter'";
        $sor=$r->prepare($sql);
        $sor->execute();
        $sonbilgi=$sor->get_result();
        $veri=$sonbilgi->fetch_assoc();

        if ($sonbilgi->num_rows == 0) :
                $this->uyari("danger", "Incorrect Login", "index.php");

        else:
            $sql = "update users set status=1 where User_Name = '$k' and Password = '$sonhal'";
            $sor=$r->prepare($sql);
            $sor->execute();
            $this->uyari("success", "Welcome", "control.php");
            $kulson=$this->sifrele($k);
            setcookie("kul", $kulson, time() + 60*60*24);
            $id=$this->sifrele($veri["ID"]);
            setcookie("ID", $id, time() + 60*60*24);
        endif;
    }

    public function cookcon($d, $durum=false) {
        if (isset($_COOKIE["kul"])) :
             $deger = $_COOKIE["kul"];
             $id=$this->coz($_COOKIE["ID"]);
             $sql = "select * from users where id=$id";
             $sor=$d->prepare($sql);
             $sor->execute();
             $sonbilgi=$sor->get_result();
             $veri = $sonbilgi->fetch_assoc();
             $sonhal=$this->sifrele($veri["User_Name"]);
            if ($sonhal != $_COOKIE["kul"]) :
                setcookie("kul", $deger, time() - 10);
                header("Location:index.php");
            else:
                if ($durum==true) : header("Location:control.php");
                endif;
            endif;
            else:
               if ($durum==false) : header("Location:index.php");
               endif;
        endif;
   }
}
?>