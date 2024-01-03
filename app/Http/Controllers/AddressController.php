<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use stdClass;
use App\Models\User;

class AddressController extends Controller
{
    private function validateAddressId($addressId)
    {
        customValidate(['addressId' => $addressId], [
            'addressId' => 'required|integer|exists:addresses,id'
        ]);
    }

    public function create(Request $request, $userId)
    {
        $validatedData = customValidate($request->all(), [
            'houseNumber' => "required|string",
            "landmark" => "nullable|string",
            "area" => "required|string",
            "city" => "required|string",
            "country" => "required|string",
            "state" => "required|string",
            "postalCode" => "required|integer"
        ]);
        validateUserId($userId);
        $user = User::find($userId);
        $address = $user->addresses()->create($validatedData);

        $data = new stdClass;
        $data->address = $address;

        return $this->success($data, "Address has been added successfully", 200);
    }

    public function update(Request $request, $userId, $addressId)
    {
        $validatedData = customValidate($request->all(), [
            'houseNumber' => "nullable|string",
            "landmark" => "nullable|string",
            "area" => "nullable|string",
            "city" => "nullable|string",
            "country" => "nullable|string",
            "state" => "nullable|string",
            "postalCode" => "nullable|integer"
        ]);
        validateUserId($userId);
        $this->validateAddressId($addressId);
        $user = User::find($userId);
        $address = $user->addresses()->find($addressId);

        if (!$address) {
            return $this->error('The address you are trying to update does not belong to you.', 400);
        }

        foreach ($validatedData as $key => $value) {
            if ($value !== null) {
                $address->$key = $value;
            }
        }
        $address->save();

        $data = new stdClass;
        $data->address = $address;

        return $this->success($data, "Address has been updated successfully", 200);
    }

    public function delete($userId, $addressId)
    {
        validateUserId($userId);
        $this->validateAddressId($addressId);
        $user = User::find($userId);
        $address = $user->addresses()->find($addressId);

        if (!$address) {
            return $this->error('The address you are trying to delete does not belong to you.', 400);
        }
        $address->delete();
        return $this->success(true, "Address has been delete successfully", 200);
    }
}
