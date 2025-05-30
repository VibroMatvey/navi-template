<?php

namespace App\Shared\Domain\Service;

use Symfony\Component\Uid\Ulid;
use URLify;

final readonly class UtilsService
{
    public static function generatePassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function generateSlug(string $value, ?array $chars = [], ?string $lang = 'ru'): string
    {
        URLify::add_chars($chars, $lang);
        return URLify::slug($value);
    }

    public static function generateUlid(): string
    {
        return Ulid::generate();
    }
}