<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @return string
     */
    public static function getTableName():string
    {
        return (new static())->getTable();
    }
}
