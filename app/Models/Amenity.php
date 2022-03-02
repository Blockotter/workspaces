<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Amenity extends Model
{
    public string $name;
    public string $emoji;

    public string $id;

    protected $fillable = [
        'name',
        'emoji',
        'id',
    ];

    public function __construct(string $id, array $fields = [])
    {

        // Set the id of the workspace
        $this->setAttribute('id', $id);

        // Change all keys of $fields to snake_case
        $snake_cased_fields = [];
        foreach ($fields as $key => $value) {
            $snake_cased_fields[Str::snake($key)] = $value;
        }

        // Set the attributes of the workspace
        parent::__construct($snake_cased_fields);
    }
}
