<html>

<?php
    include("../fonksiyon/fonksiyon.php");
    include_once("../yon/fonk/temaiki.php");
    $system = new system;
    $tema2 = new temadestek;  
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="../dosya/jqu.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <style>
    </style>
    <script>
        $(document).ready(function(){
            $('#mutfaklink a').click(function(){
			var urunid =$(this).attr('sectionId');
			var tableid =$(this).attr('sectionId2');
			$.post("../islemler.php?islem=mutfaksip",{"urunid":urunid,"tableid":tableid},function(post_veri){
			window.location.reload();
			});		
            });
		});
    </script>
    <title>EasyRest Mutfak</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
               <?php $tema2->mutfakbilgi($db); ?>
        </div>
</body>
</html>