<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $old_slug
 * @property string $new_slug
 * @property string $created_at
 * @property string $updated_at
 */
class Redirect extends Model
{
    use HasFactory;
}
