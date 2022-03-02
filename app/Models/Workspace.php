<?php

namespace App\Models;

use Tapp\Airtable\Facades\AirtableFacade as Airtable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Str;

class Workspace extends Model
{
    public string $name;

    public string $place;
    public string $street_name;
    public string $building_number;
    public string $zipcode;
    public string $country;

    public string $website;
    public string $phone_number;
    public string $email;

    public string $image_url;

    public float $internet_speed;

    public DateTime $created_at;
    public DateTime $updated_at;

    public Collection $workspace_packages;

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
        'workspace_packages',
        'image_url',
    ];

    public function __construct(array $fields = [])
    {
        $snake_cased_fields = [];
        foreach ($fields as $key => $value) {
            $snake_cased_fields[Str::snake($key)] = $value;
        }

        if (isset($snake_cased_fields['packages'])) {
            $workspace_packages = collect();
            foreach ($snake_cased_fields['packages'] as $package) {
                $fields = Airtable::table('packages')->find($package)['fields'];

                if (isset($fields['Workspace']) && !empty($fields['Workspace'])) {
                    $fields['workspace_id'] = $fields['Workspace'][0];
                }

                $workspace_packages->push(new Package($fields));
            }
            $this->setAttribute('workspace_packages', $workspace_packages);
        }

        if (isset($snake_cased_fields['image']) && isset($snake_cased_fields['image'][0]['thumbnails'])) {
            $this->setAttribute('image_url', $snake_cased_fields['image'][0]['thumbnails']['large']['url']);
        }

        parent::__construct($snake_cased_fields);
    }

    public function getName(): string
    {
        $name = $this->getAttribute('name');

        // If the name ends at 'Coworking Space', remove it
        if (str_ends_with($name, 'Coworking Space')) {
            $name = substr($name, 0, -15);
        }

        return $name;
    }
}
