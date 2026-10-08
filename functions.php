<?php
declare(strict_types=1);

function validate_text(string $value, string $label, int $maxLength = 5000): ?string
{
    $value = trim($value);
    if ($value === '') {
        return "{$label} is required.";
    }
    if (mb_strlen($value) > $maxLength) {
        return "{$label} must be {$maxLength} characters or fewer.";
    }
    return null;
}

function redirect(string $location): never
{
    header("Location: {$location}");
    exit;
}
