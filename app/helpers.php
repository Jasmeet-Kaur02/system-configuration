<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


if (!function_exists('customValidate')) {
    function customValidate($validationData, $rules)
    {
        $validator = Validator::make($validationData, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}

if (!function_exists('validateUserId')) {
    function validateUserId($userId)
    {
        customValidate(['userId' => $userId], [
            'userId' => 'required|integer|exists:users,id'
        ]);
    }
}

if (!function_exists("validateUploadedFiles")) {
    function validateUploadedFiles(
        Request $request,
        string $key,
        int  $maximumCount,
        array $extensions,
        int $maximumSize
    ) {
        $data =  new stdClass;
        $files = $request->file($key);
        $errorMessage = "Invalid $key";
        if (!$request->file($key)) {
            $data->$key = "Please attach/upload $key";
            throw new CustomException($errorMessage, $data, 400);
        }
        if (count($files) > $maximumCount) {
            $data->$key = "Maximum $maximumCount can be uploaded at once";
            throw new CustomException($errorMessage, $data, 406);
        }
        foreach ($files as $file) {
            $extension = $file->extension();
            $check = in_array($extension, $extensions);
            if (!$check) {
                $data->$key = "Unsupported Media Format. $extension";
                throw new CustomException($errorMessage, $key, 415);
            }
            if ($file->getSize() > $maximumSize * 1024 * 1024) {
                $data->$key = "File size exceeds the max limit of $maximumSize MB.";
                throw new CustomException($errorMessage, $key, 415);
            }
        }
    }
}
