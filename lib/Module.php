<?php

namespace TobiasKrais\D2ULinkbox;

/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class Module
{
    /**
     * Get modules offered by this addon.
     * @return array<\TobiasKrais\D2UHelper\Module> Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-1',
            'D2U Linkbox - Linkboxen mit Überschrift in Bild (BS4, deprecated)',
            10);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-2',
            'D2U Linkbox - Linkboxen mit Überschrift unter Bild (BS4, deprecated)',
            12);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-3',
            'D2U Linkbox - Farbboxen mit seitlichem Bild (BS4, deprecated)',
            8);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-4',
            'D2U Linkbox - Slider (BS4, deprecated)',
            7);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-5',
            'D2U Linkbox - Linkboxen mit Text neben dem Bild (BS4, deprecated)',
            4);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-6',
            'D2U Linkbox - Linkboxen mit Text und Hoverbild (BS4, deprecated)',
            2);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-7',
            'D2U Linkbox - Linkboxen mit Überschrift in Bild (BS5)',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-8',
            'D2U Linkbox - Linkboxen mit Überschrift unter Bild (BS5)',
            2);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-9',
            'D2U Linkbox - Farbboxen mit seitlichem Bild (BS5)',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-10',
            'D2U Linkbox - Slider (BS5)',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-11',
            'D2U Linkbox - Linkboxen mit Text neben dem Bild (BS5)',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-12',
            'D2U Linkbox - Linkboxen mit Text und Hoverbild (BS5)',
            2);
        return $modules;
    }
}
