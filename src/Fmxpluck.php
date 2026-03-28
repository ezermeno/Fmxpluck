<?php

namespace Ezermeno\Fmxpluck;
use Illuminate\Support\Str;

trait Fmxpluck
{

   
  public function scopeFmxPluck($query, $columnas = [], $id = null, $separador = " ")
   {
       $selects = [];
       $joins   = [];
  
       foreach ($columnas as $col) {
  
           if (strpos($col, '.') !== false) {
  
  
               [$relacion, $campo] = explode('.', $col);
  
                $tablaRelacion = $relacion;
                $fk = $relacion.'_id';
  
                $joins[$tablaRelacion] = [$tablaRelacion.'.id', '=', $query->getModel()->getTable().'.'.$fk];
  
                $selects[] = $tablaRelacion.'.'.$campo;
  
            } else {
               $selects[] = $query->getModel()->getTable().'.'.$col;
            }
  
       }
     
       foreach ($joins as $tabla => $on) {
           $query->leftJoin($tabla, $on[0], $on[1], $on[2]);
       }
       
       $campos = implode(', " '.$separador.' ", ', $selects);
  
       $registros = $query->selectRaw('CONCAT('.$campos.') as opcion, '. $query->getModel()->getTable().'.'.$id)->get();
  
       $aArray = [];
       foreach ($registros as $registro) {
           $aArray[$registro->$id] = $registro->opcion;
       }
  
       return $aArray;
   }

}
