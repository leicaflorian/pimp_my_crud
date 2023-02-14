<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait WithTableAnalyzer {
  /* const TABLE_ANALYZER = [
     "string"             => "text",
     "text"               => "textarea",
     "integer"            => "number",
     "float"              => "number",
     "double"             => "number",
     "decimal"            => "number",
     "boolean"            => "checkbox",
     "date"               => "date",
     "datetime"           => "datetime-local",
     "timestamp"          => "datetime-local",
     "time"               => "time",
     "year"               => "number",
     "enum"               => "select",
     "json"               => "textarea",
     "jsonb"              => "textarea",
     "array"              => "textarea",
     "binary"             => "file",
     "uuid"               => "text",
     "ipAddress"          => "text",
     "macAddress"         => "text",
     "geometry"           => "text",
     "point"              => "text",
     "linestring"         => "text",
     "polygon"            => "text",
     "geometrycollection" => "text",
     "multipoint"         => "text",
     "multilinestring"    => "text",
     "multipolygon"       => "text"
   ];*/
  
  protected function getTabelColumns(Model $modelInstance): Collection {
    $table    = $modelInstance->getTable();
    $columns  = Schema::getColumnListing($table);
    $fillable = $modelInstance->getFillable();
    $unfillable = ["id", "created_at", "updated_at"];
    
    return collect(array_map(function ($column) use ($table, $fillable, $unfillable) {
        $isFillable = (count($fillable) > 0) ? in_array($column, $fillable) : !in_array($column, $unfillable);
        
        return [
          "name"     => $column,
          "type"     => Schema::getColumnType($table, $column),
          "fillable" => $isFillable
        ];
      }, $columns)
    );
  }
}
