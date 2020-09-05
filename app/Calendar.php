<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
  protected $fillable = [
      'user_id'
  ];

  public function entries()
  {
    return $this->hasMany('App\Entry');
  }
}
