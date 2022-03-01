<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Str;

class Workspace extends Model
{
    public string $name;
    public string $place;
    public string $street_name;
    public string $building_number;
    public string $zipcode;
    public string $country;
    public DateTime $created_at;
    public DateTime $updated_at;
    public string $website;
    public string $phone_number;
    public string $email;
    public float $internet_speed;

    protected $fillable = [
        'name',
        'place',
        'street_name',
        'building_number',
        'zipcode',
        'country',
        'created_at',
        'updated_at',
        'website',
        'phone_number',
        'email',
        'internet_speed',
    ];

    public function __construct(array $fields = [])
    {
        $snake_cased_fields = [];
        foreach ($fields as $key => $value) {
            $snake_cased_fields[Str::snake($key)] = $value;
        }

        parent::__construct($snake_cased_fields);
    }
}
