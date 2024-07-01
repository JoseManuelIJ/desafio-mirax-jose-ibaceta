<?php
function products(){
    return "select * from productos";
}
function lastReplacement($branch,$code){
    return "SELECT MAX(fecha) from movimientos_de_productos where entrada>0 and codigo=".$code." and (descripcion = 'recibido en ".$branch."' or descripcion= 'compra')";
};
function StockBranch($branch,$code,$date){
    return "select SUM(entrada) from movimientos_de_productos where fecha='".$date."' and entrada>0 and codigo= ".$code." and (descripcion= 'recibido en ".$branch."' or descripcion='compra')";
}
function LastSaleDayBranch($branch,$code){
    return "select Max(fecha) from movimientos_de_productos where salida>0 and codigo= ".$code;
}
function SalesBranch($branch,$code,$date){
    return "select entrada,salida,boleta,factura from movimientos_de_productos where fecha>".$date." and codigo= ".$code." and descripcion != 'recibido en ".$branch."'";
}
function payedBillOrTickets($type, $code,$branch){
    if ($branch=="apumanque"){
        return "Select count(*) from ".$type."s where estado=2 and sucursal=0 and ".$type."=".$code;
    }
    elseif ($branch=="costanera"){
        return "Select count(*) from ".$type."s where estado=2 and sucursal=6 and ".$type."=".$code;
    }
}
function movementInXDays($date,$code){
    return "select boleta,factura from movimientos_de_productos where fecha>='".$date."' and codigo=".$code;
}

?>
