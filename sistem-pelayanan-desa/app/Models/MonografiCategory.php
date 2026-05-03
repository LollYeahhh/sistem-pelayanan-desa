<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonografiCategory extends Model
{
    protected $table = "monografi_categories";

    public $timestamps = false;

    protected $fillable = ["kode", "nama_kategori", "icon", "urutan"];

    public function fields()
    {
        return $this->hasMany(MonografiField::class, "category_id")->orderBy(
            "urutan",
        );
    }
}
