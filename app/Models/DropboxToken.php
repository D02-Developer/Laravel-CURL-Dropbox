<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropboxToken extends Model
{
    protected $fillable = ['access_token', 'token_type', 'expires_in', 'refresh_token', 'scope', 'uid', 'account_id'];

}
