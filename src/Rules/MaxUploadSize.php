<?php

namespace Brunocfalcao\LaravelHelpers\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxUploadSize implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Construct the full file path using the public_path function
        $filePath = public_path('storage/'.$value);

        // Check if the file exists and its size is within the limits
        return filesize($filePath) <= $this->getMaxUploadSize();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $maxSize = $this->getMaxUploadSize() / 1024; // Convert from bytes to kilobytes

        return "The :attribute size must be within {$maxSize} kilobytes, as specified by upload_max_filesize in php.ini.";
    }

    /**
     * Get the maximum allowed file size from php.ini and convert it to kilobytes.
     *
     * @return int
     */
    private function getMaxUploadSize()
    {
        $uploadMaxSize = ini_get('upload_max_filesize');

        return $this->convertToKilobytes($uploadMaxSize);
    }

    /**
     * Helper function to convert PHP ini size format to kilobytes
     */
    private function convertToKilobytes($size)
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        switch ($unit) {
            case 'P':
                $value *= 1024;
            case 'T':
                $value *= 1024;
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024 * 1024; // Convert megabytes to bytes
            case 'K':
                $value *= 1024; // Convert kilobytes to bytes

                return $value;
            default:
                return $value;
        }
    }
}
