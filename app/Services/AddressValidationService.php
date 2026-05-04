<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Exception;

class AddressValidationService
{
    /**
     * Validate the shipping address.
     *
     * @param array $addressData
     * @return array
     * @throws Exception
     */
    public function validate(array $addressData): array
    {
        $validator = Validator::make($addressData, [
            'street'   => 'required|string|max:255',
            'city'     => 'required|string|max:100',
            'state'    => 'required|string|max:100',
            'pincode'  => 'required|string|size:6|regex:/^[1-9][0-9]{5}$/',
            'phone'    => 'required|string|min:10|max:15',
        ], [
            'pincode.regex' => 'Please enter a valid 6-digit pincode.',
            'pincode.size'  => 'Pincode must be exactly 6 digits.',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        return $validator->validated();
    }
}
