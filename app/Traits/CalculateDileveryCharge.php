<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CalculateDileveryCharge
{
    /**
    * @var Object
    */
    private $charge;
    private $distanceMatrix;

    public function getDistance($first_lat, $second_lat) {
        
        return $this->distanceMatrix[$first_lat.'-'.$second_lat];
    }

    /**
    * Calculate price for the given  distance.
    * 
    * @param Array of lat, long
    * @return Float
    */
    public function getDeliveryCharge(Array $request)
    {
        $this->calculateCharge( $this->calculatePriceUsingGoogleDistanceMatrixApi($request) );

        if (! $this->charge) $this->charge = $this->calculateCharge( $this->distanceCalculation($request) );

        return $this->charge;
    }

    /**
    * Calculate price for the given  distance.
    * 
    * @param Float $distance
    * @return Float
    */
    public function calculateCharge($distance)
    {
        if ( ! $distance ) return 0;

        switch ($distance) {

            case ($distance < 2):

                $this->charge = 1.5;
                break;

            case ($distance >= 2 && $distance < 3):

                $this->charge = 2;
                break;

            case ($distance >= 3 && $distance < 4):

                $this->charge = 2.5;
                break;

            case ($distance >= 4 && $distance < 5):

                $this->charge = 3;
                break;
        }

        // return $this->charge;
    }

    /**
    * Calculate price for the given  distance.
    * 
    * @param Float $distance
    * @return Float
    */
    public function calculatePriceUsingGoogleDistanceMatrixApi(Array $locations)
    {
        $first_location = $locations['first_location'];
        $second_location = $locations['second_location'];

        $origins        = $first_location['latitude'] . ',' . $first_location['longitude'];
        $destinations   = $second_location['latitude'] . ',' . $first_location['longitude'];

        $url = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' .$origins. '&destinations=' .$destinations. '&mode=driving&language=en-EN&sensor=false';

        $result = json_decode( file_get_contents( $url ) );

        if ($result) {

            if (! isset($result->rows[0])) return 0;

            $rows = $result->rows[0];

            if (! isset($rows->elements[0])) return 0;

            $ele = $rows->elements[0];

            if ($ele->status == 'ZERO_RESULTS' || $ele->status == 'NOT_FOUND') return 0;

            $price = explode(' ', $ele->distance->text);
            $this->distanceMatrix[$first_location['latitude'].'-'.$second_location['latitude']] = $price[0];

            return $price[0];
        }
    }

    /**
    * Calculate the distance in degrees.
    * Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
    * 
    * @param Location Array (latitude, longitude)
    * @return Float
    */
    function distanceCalculation(Array $locations, $unit = 'km', $decimals = 2) 
    {
        $first_location = $locations['first_location'];
        $second_location = $locations['second_location'];

        $degrees = rad2deg(acos((sin(deg2rad($first_location['latitude'])) * sin(deg2rad($second_location['latitude']))) + 
                    (cos(deg2rad($first_location['latitude'])) * cos(deg2rad($second_location['latitude'])) * 
                    cos(deg2rad($first_location['latitude']-$second_location['latitude'])))));

        switch ($unit) 
        {
            case 'km':
                // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
                $distance = $degrees * 111.13384; 
                break;

            case 'mi':
                // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
                $distance = $degrees * 69.05482; 
                break;

            case 'nmi':
                // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
                $distance =  $degrees * 59.97662; 
        }

            return $this->distanceMatrix[$first_location['latitude'].'-'.$second_location['latitude']] = round($distance, $decimals);
    }

}