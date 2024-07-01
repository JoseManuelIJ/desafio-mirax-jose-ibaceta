<?php
    include('config.php');
    include('querys.php');
    include('validacion.php');
    //coneccion con la base de datos
    $connection= connection();
    //probando que funcione la conexion
    if (!$connection) {
        die("Conexion fallida: " . mysqli_connect_error()."\n");
    }
    //Solicitud de sucursal:
    $branch=validateBranch();
    //Solicitud de los dias a revisar
    $days=validateDays();

    //manejo de datos
    //se obtiene el dia actual y el de 10 dias atras
    $today = date('Y-m-d');
    $xDaysAgo= date('Y-m-d',strtotime($today."- ".$days." days"));
    //Se obtienen todos los productos que tiene la tienda
    $products=mysqli_query($connection,products());
    $finalProducts=[['codigo', 'descripcion', 'stock '.$branch]];
    $index=0;
    while($product=mysqli_fetch_assoc($products)){
        $index=$index+1;
        echo $index;
        echo "\n";
        //se obtiene el dia de la ultima compra
        $productLastReplacementDayData=mysqli_query($connection,lastReplacement($branch, $product["codigo"]));
        $productLastReplacementDay= mysqli_fetch_array($productLastReplacementDayData, MYSQLI_NUM)[0];
        //se asigna el stock
        if($productLastReplacementDay === null){
            $productLastReplacementDay="2024-12-31";
            $stock=10;
        }
        else{
            $stockData=mysqli_query($connection,StockBranch($branch,$product["codigo"],$productLastReplacementDay));
            $stock=mysqli_fetch_array($stockData, MYSQLI_NUM)[0];
        }
        //se procesa el stock segun las transacciones
        $productMovements=mysqli_query($connection,SalesBranch($branch,$product["codigo"],$productLastReplacementDay));
        while($productMovement=mysqli_fetch_assoc($productMovements)){
            if ($productMovement["entrada"]>0){
                $stock=$stock+$productMovement["entrada"];                        
            }
            else{
                if ($productMovement["boleta"]!==null){
                    $ticket=mysqli_query($connection,payedBillOrTickets("boleta", $productMovement["boleta"],$branch));
                    $count = mysqli_fetch_array($ticket, MYSQLI_NUM)[0];
                    if($count==1){
                        $stock=$stock-$productMovement["salida"];
                    }
                }
                elseif ($productMovement["factura"]!==null){
                    $ticket=mysqli_query($connection,payedBillOrTickets("factura", $productMovement["factura"],$branch));
                    $count = mysqli_fetch_array($ticket, MYSQLI_NUM)[0];
                    if($count==1){
                        $stock=$stock-$productMovement["salida"];
                    }
                }
            }
        }
        //se revisa el producto si su stock final es mayor a 0
        if($stock>0){
            //se obtienen los movimientos ocurridos desde hace x dias
            $countMovementDatas=mysqli_query($connection,movementInXDays($xDaysAgo,$product["codigo"]));
            $countMovement=false;
            while($countMovementData=mysqli_fetch_assoc($countMovementDatas)){
                if ($countMovementData["boleta"]!==null){
                    $ticket=mysqli_query($connection,payedBillOrTickets("boleta", $countMovementData["boleta"],$branch));
                    $count = mysqli_fetch_array($ticket, MYSQLI_NUM)[0];
                    if($count==1){
                        $countMovement=true;
                        break;
                    }
                }
                elseif ($countMovementData["factura"]!==null){
                    $ticket=mysqli_query($connection,payedBillOrTickets("factura", $countMovementData["factura"],$branch));
                    $count = mysqli_fetch_array($ticket, MYSQLI_NUM)[0];
                    if($count==1){
                        $countMovement=true;
                        break;
                    }
                }
            }            
            //si no ocurrio un movimiento, se agrega al los productos finales
            if(!$countMovement){
                array_push($finalProducts,[$product["codigo"], $product["descripcion"], $stock]);
            }   
        }
    }
    //se guarda en un archivo 'productos_sin_movimiento.csv' en la misma carpeta del codigo
    $fp = fopen('productos_sin_movimiento.csv', 'wb');
    foreach ($finalProducts as $line) {
        print_r($line);
        fputcsv($fp, $line);
    }
    fclose($fp);
    mysqli_close($connection);    
?>