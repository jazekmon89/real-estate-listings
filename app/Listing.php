<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\StoredProcTrait as StoredProc;

use DB;
use Flash;

class Listing extends Model
{
    use StoredProc;


    protected $table = 'MainData';
    protected $primaryKey = 'MND_ID';

    protected $fillable = [
        
    ];
}
