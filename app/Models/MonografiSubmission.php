<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonografiSubmission extends Model
{
    protected $table = "monografi_submissions";

    protected $fillable = [
        "rt_user_id",
        "tahun",
        "status",
        "wilayah_label",
        "last_saved_at",
        "submitted_at",
    ];

    protected function casts(): array
    {
        return [
            "tahun" => "integer",
            "last_saved_at" => "datetime",
            "submitted_at" => "datetime",
        ];
    }

    public function rtUser()
    {
        return $this->belongsTo(User::class, "rt_user_id");
    }
}
