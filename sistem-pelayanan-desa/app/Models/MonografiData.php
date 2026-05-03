<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonografiData extends Model
{
    protected $table = "monografi_data";

    protected $fillable = ["field_id", "rt_user_id", "value", "tahun"];

    protected function casts(): array
    {
        return [
            "tahun" => "integer",
        ];
    }

    public function field()
    {
        return $this->belongsTo(MonografiField::class, "field_id");
    }

    public function rtUser()
    {
        return $this->belongsTo(User::class, "rt_user_id");
    }
}
