<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Str;

class Package extends Model
{
    public string $name;
    public string $website;

    public bool $is_recurring;
    public bool $is_always_open;

    public float $cost;
    public float $minimum_hours;

    // Store the opening time as time of day
    public DateTime $opening_monday;
    public DateTime $closing_monday;

    public DateTime $opening_tuesday;
    public DateTime $closing_tuesday;

    public DateTime $opening_wednesday;
    public DateTime $closing_wednesday;

    public DateTime $opening_thursday;
    public DateTime $closing_thursday;

    public DateTime $opening_friday;
    public DateTime $closing_friday;

    public DateTime $opening_saturday;
    public DateTime $closing_saturday;

    public DateTime $opening_sunday;
    public DateTime $closing_sunday;

    public string $id;

    protected $fillable = [
        'name',
        'website',
        'is_recurring',
        'is_always_open',
        'cost',
        'minimum_hours',
        'opening_monday',
        'closing_monday',
        'opening_tuesday',
        'closing_tuesday',
        'opening_wednesday',
        'closing_wednesday',
        'opening_thursday',
        'closing_thursday',
        'opening_friday',
        'closing_friday',
        'opening_saturday',
        'closing_saturday',
        'opening_sunday',
        'closing_sunday',
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
