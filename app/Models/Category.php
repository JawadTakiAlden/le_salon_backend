<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded  = ['id'];


    public function setImageAttribute ($image)
    {
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('images/category') , $newImageName);
        return $this->attributes['image'] ='/'.'images/category'.'/' . $newImageName;
    }
}
