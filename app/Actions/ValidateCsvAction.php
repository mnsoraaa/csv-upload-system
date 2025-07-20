<?php

namespace App\Actions;

class ValidateCsvAction
{
    /**
     * Validate CSV headers match expected format
     */
    public function validateHeaders(array $headers): void
    {
        $requiredHeaders = [
            'UNIQUE_KEY',
            'PRODUCT_TITLE',
            'PRODUCT_DESCRIPTION',
            'STYLE#',
            'SANMAR_MAINFRAME_COLOR',
            'SIZE',
            'COLOR_NAME',
            'PIECE_PRICE'
        ];

        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (!empty($missingHeaders)) {

            throw new \Exception("Missing required CSV headers: " . implode(', ', $missingHeaders));

        }

        if (!in_array('UNIQUE_KEY', $headers)) {

            throw new \Exception("UNIQUE_KEY header is required and cannot be empty");
            
        }
    }

    /**
     * Clean and validate individual CSV record, return database-ready data
     */
    public function cleanAndValidateRecord(array $record): ?array
    {
        $cleaned = [];

        foreach ($record as $key => $value) {

            $cleaned[$key] = trim(mb_convert_encoding($value, 'UTF-8', 'UTF-8'));

        }

        if (empty($cleaned['UNIQUE_KEY'])) {

            throw new \InvalidArgumentException('UNIQUE_KEY is required');

        }

        return [
            'unique_key' => trim($cleaned['UNIQUE_KEY']),
            'product_title' => !empty($cleaned['PRODUCT_TITLE']) ? trim($cleaned['PRODUCT_TITLE']) : null,
            'product_description' => !empty($cleaned['PRODUCT_DESCRIPTION']) ? trim($cleaned['PRODUCT_DESCRIPTION']) : null,
            'style_number' => !empty($cleaned['STYLE#']) ? trim($cleaned['STYLE#']) : null,
            'sanmar_mainframe_color' => !empty($cleaned['SANMAR_MAINFRAME_COLOR']) ? trim($cleaned['SANMAR_MAINFRAME_COLOR']) : null,
            'size' => !empty($cleaned['SIZE']) ? trim($cleaned['SIZE']) : null,
            'color_name' => !empty($cleaned['COLOR_NAME']) ? trim($cleaned['COLOR_NAME']) : null,
            'piece_price' => !empty($cleaned['PIECE_PRICE']) ? (float)$cleaned['PIECE_PRICE'] : null,
        ];
    }
}
