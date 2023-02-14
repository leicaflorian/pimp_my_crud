<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
  use HasFactory;
  
  protected $fillable = [
    "title", "content", "cover_img", "public",
    "category_id"
  ];
}
