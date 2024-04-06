<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_date',
        'activity_date_withoutday',
        'activity_rev',
        'activity_dc',
        'activity_check_in_l',
        'activity_check_in_z',
        'activity_check_out_l',
        'activity_check_out_z',
        'activity',
        'activity_remark',
        'activity_from',
        'activity_from_airport_code',
        'activity_from_std_l',
        'activity_from_std_z',
        'activity_to',
        'activity_to_std_l',
        'activity_to_std_z',
        'activity_hotel',
        'activity_blh',
        'activity_flight_time',
        'activity_night_time',
        'activity_dur',
        'activity_ext',
        'activity_pax_booked',
        'activity_acreg',
        'activity_crew_meal',
        'activity_resources',
        'activity_cc',
        'activity_name',
        'activity_pos',
        'activity_work_phone',
        'activity_hd_crew',
        'activity_hd_name',
        'activity_hd_seat',
        'activity_remarks',
        'activity_fdp_time',
        'activity_max_fdp',
        'activity_rest_compl'
    ];

    protected $showable = [
        'activity_date',
        'activity_rev',
        'activity_dc',
        'activity_check_in_l',
        'activity_check_in_z',
        'activity_check_out_l',
        'activity_check_out_z',
        'activity',
        'activity_remark',
        'activity_from',
        'activity_from_airport_code',
        'activity_from_std_l',
        'activity_from_std_z',
        'activity_to',
        'activity_to_std_l',
        'activity_to_std_z',
        'activity_hotel',
        'activity_blh',
        'activity_flight_time',
        'activity_night_time',
        'activity_dur',
        'activity_ext',
        'activity_pax_booked',
        'activity_acreg',
        // Add other fields that should be considered "showable"...
    ];

    protected $casts = [
        // 'activity_date' => 'json',
        'activity_rev' => 'json',
        'activity_dc' => 'json',
        'activity_check_in_l' => 'json',
        'activity_check_in_z' => 'json',
        'activity_check_out_l' => 'json',
        'activity_check_out_z' => 'json',
        'activity' => 'json',
        'activity_remark' => 'json',
        'activity_from' => 'json',
        'activity_from_std_l' => 'json',
        'activity_from_std_z' => 'json',
        'activity_to' => 'json',
        'activity_to_std_l' => 'json',
        'activity_to_std_z' => 'json',
        'activity_hotel' => 'json',
        'activity_blh' => 'json',
        'activity_flight_time' => 'json',
        'activity_night_time' => 'json',
        'activity_dur' => 'json',
        'activity_ext' => 'json',
        'activity_pax_booked' => 'json',
        'activity_acreg' => 'json', 
        // Cast 'data' attribute to JSON
    ];

    public function getShowableAttributes()
    {
        $showableAttributes = [];
        foreach ($this->showable as $attribute) {
            if ($this->hasGetMutator($attribute)) {
                $showableAttributes[$attribute] = $this->mutateAttribute($attribute, $this->$attribute);
            } elseif ($this->isJsonAttribute($attribute) && in_array(gettype($this->getAttribute($attribute)), ['string'])) {
                $showableAttributes[$attribute] = json_decode($this->getAttribute($attribute), true);
            } else {
                $showableAttributes[$attribute] = $this->getAttribute($attribute);
            }
        }
        return $showableAttributes;
    }

    protected function isJsonAttribute($attribute)
    {
        return $this->isJsonCastable($attribute) ||
               $this->hasCast($attribute, ['json', 'array']);
    }
}
