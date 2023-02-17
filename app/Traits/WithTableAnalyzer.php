<?php

namespace App\Traits;

use App\Models\Post;
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
  
  /**
   * @param  Model  $modelInstance
   *
   * @return Collection<[name=>string, type=>string, fillable=>bool, required=>bool, foreign=>[table=>string, column=>string]]>
   */
  protected function getTableColumns(Model $modelInstance): Collection {
    $table = $modelInstance->getTable();
//    $columns    = Schema::getColumnListing($table);
    $columns     = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($table)->getColumns();
    $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table);
    $fillable    = $modelInstance->getFillable();
    $unfillable  = ["id", "created_at", "updated_at"];
    
    $toReturn = collect();
    
    foreach ($columns as $key => $column) {
      $isFillable = (count($fillable) > 0) ? in_array($column->getName(), $fillable) : !in_array($column->getName(), $unfillable);
      $fks        = array_filter($foreignKeys, function ($foreignKey) use ($column) {
        return $foreignKey->getLocalColumns()[0] === $column->getName();
      });
      
      if (count($fks) > 0) {
        $foreignKey = array_values($fks)[0];
        $isForeign  = [
          "table"  => $foreignKey->getForeignTableName(),
          "column" => $foreignKey->getForeignColumns()[0],
        ];
      } else {
        $isForeign = false;
      }
      
      $toReturn->push([
        "name"     => $column->getName(),
        "type"     => $column->getType()->getName(),
        "fillable" => $isFillable,
        "required" => $column->getNotnull(),
        "foreign"  => $isForeign,
      ]);
    }
    
    return $toReturn;
  }
}

use Doctrine\DBAL\Schema\Column;
