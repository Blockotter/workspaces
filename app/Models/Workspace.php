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

                if (isset($fields['Workspace']) && ! empty($fields['Workspace'])) {
                    $fields['workspace_id'] = $fields['Workspace'][0];
                }

                $workspace_packages->push(new Package($fields));
            }
            $this->setAttribute('workspace_packages', $workspace_packages);
        }

        parent::__construct($snake_cased_fields);
    }
}
