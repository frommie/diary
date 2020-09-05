<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
  protected $fillable = [
    'calendar_id', 'date', 'content', 'mood',
  ];
}
