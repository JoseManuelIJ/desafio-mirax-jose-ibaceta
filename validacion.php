<?php
    function validateBranch(){
        echo "Ingrese nombre de la sucursal, pueden ser 'costanera' o 'apumanque'"."\n";
        fscanf(STDIN,"%s",$branch);
        if($branch==="apumanque" || $branch==="costanera"){
            return $branch;
        }
        else{
            echo "porfavor, ingrese una opcion valida";
            return validateBranch();
        }
    }
    function validateDays(){
        echo "Ingrese la cantidad de dias para revisar"."\n";
            fscanf(STDIN,"%d",$days);
            if($days==null){
                echo "Ingrese un numero entero\n";
                return validateDays();
            }
        if ($days>=0){
            return $days;
        }
        else{
            echo "Ingrese un numero positivo\n";
            return validateDays();
        }        
    }
?>