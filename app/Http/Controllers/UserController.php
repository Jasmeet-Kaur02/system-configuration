<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use stdClass;

class UserController extends Controller
{
    public function update(Request $request, $userId)
    {
        $validatedData = customValidate($request->all(), [
            'firstName' => "nullable|string",
            "lastName" => "nullable|string",
            "email" => "nullable|email|unique:users,email",
            "phoneNumber" => "nullable|string|min:10",
            "gender" => "nullable|string|in:male,female",
        ]);
        validateUserId($userId);

        $user = User::find($userId);

        foreach ($validatedData as $key => $value) {
            if ($value !== 'null' && $key !== "userId") {
                $user->$key = $value;
            }
        }

        $user->save();
        $data = new stdClass;
        $data->user = $user;

        return $this->success($data, "User details have been updated successfully", 200);
    }

    public function delete($userId)
    {
        validateUserId($userId);
        $user = User::find($userId);
        $user->tokens()->delete();
        $user->delete();

        return $this->success(true, "User account has been deleted successfully", 200);
    }

    public function updateProfilePicture(Request $request, $userId)
    {
        validateUserId($userId);
        validateUploadedFiles($request, 'profile', 1, ['jpg', 'png', 'jpeg', 'webp'], 2);

        $user = User::find($userId);
        $image = $request->file('profile')[0];
        $newProfilePicture = [
            "url" => $image->store('uploads', ['disk' => "public"]),
            "mime" => $image->getMimeType(),
            'size' => $image->getSize()
        ];

        if ($user->profilePicture) {
            $profilePicture = $user->profilePicture;
            Storage::disk('public')->delete($profilePicture->url);
            $profilePicture->update($newProfilePicture);
            $message = "Profile picture has been updated successfully";
        } else {
            $profilePicture = $user->profilePicture()->create($newProfilePicture);
            $message = "Profile picture has been added successfully";
        }
        $data = new stdClass;
        $data->profilePicture = $profilePicture;
        return $this->success($data, $message, 200);
    }

    public function deleteProfilePicture($userId)
    {
        validateUserId($userId);
        $user = User::find($userId);
        if ($user->profilePicture) {
            $profilePicture = $user->profilePicture;
            Storage::disk('public')->delete($profilePicture->url);
            $user->profilePicture()->delete();
        }
        return $this->success(true, "Profile picture has been removed successfully", 200);
    }
}
