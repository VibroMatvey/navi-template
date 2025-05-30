<?php

namespace App\Shared\Presentation\EasyAdmin\Field\Helpers;

use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Validator\Constraints\File;

readonly class ImageHelperField
{
    public static function new(
        string  $propertyName,
        string  $label,
        ?string $uploadDir = "uploads/unknown",
        ?array  $mimeTypes = ["image/*", "video/*", "application/*"]
    ): ImageField
    {
        return ImageField::new($propertyName, $label)
            ->setColumns(6)
            ->setFormTypeOptions([
                'attr' => ['accept' => implode(", ", $mimeTypes)],
            ])
            ->setUploadedFileNamePattern('[ulid].[extension]')
            ->setUploadDir(Path::join('public', $uploadDir))
            ->setFileConstraints(new File(mimeTypes: $mimeTypes))
            ->setBasePath($uploadDir);
    }
}