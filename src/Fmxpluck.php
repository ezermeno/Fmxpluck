<?php

namespace Ezermeno\Fmxpluck;

trait Fmxpluck
{
   use Illuminate\Support\Str;
   
   public function scopeFmxPluck($query, $columnas = [], $id = null, $separador = " ")
   {
       $selects = [];
       $joins   = [];
   
       foreach ($columnas as $col) {
           if (strpos($col, '.') !== false) {
               [$relacion, $campo] = explode('.', $col);
   
               // Si el primer segmento ya es el nombre de la tabla (ej: "puestos.nombre")
              
               if (Str::plural($relacion) === $relacion || $relacion === $query->getModel()->getTable()) {
                   $selects[] = $relacion.'.'.$campo;
               } else {
                   // Caso: relación tipo belongsTo (ej: "puesto.nombre")
                   $tablaRelacion = Str::plural($relacion); // "puesto" → "puestos"
                   $fk = $relacion.'_id';
   
                   $joins[$tablaRelacion] = [$tablaRelacion.'.id', '=', $query->getModel()->getTable().'.'.$fk];
                   $selects[] = $tablaRelacion.'.'.$campo;
               }
           } else {
               $selects[] = $query->getModel()->getTable().'.'.$col;
           }
       }
   
       // Agregar los joins
       foreach ($joins as $tabla => $on) {
           $query->leftJoin($tabla, $on[0], $on[1], $on[2]);
       }
   
       // Concatenar columnas
       $campos = implode(', " '.$separador.' ", ', $selects);
   
       $registros = $query->selectRaw('CONCAT('.$campos.') as opcion, '.$id)->get();
   
       $aArray = [];
       foreach ($registros as $registro) {
           $aArray[$registro->$id] = $registro->opcion;
       }
   
       return $aArray;
   }

}
