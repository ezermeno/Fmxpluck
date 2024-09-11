<?php

namespace Ezermeno\Fmxpluck;

trait Fmxpluck
{
   public function scopeFmxPluck($query,$columnas = [] ,$id = null, $separador = "")
   {

          $campos = implode(', " '. $separador .' ",', $columnas);

          $registros = $query->selectRaw('CONCAT (' . $campos .') as opcion ,' . $id)->get();

          $aArray=[];

          foreach ($registros as $registro ) {

               $aArray[$registro->$id] = $registro->opcion;
          }

          return $aArray;
   }
}
