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
            'D2U Linkbox - Linkboxen mit Überschrift in Bild',
            10);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-2',
            'D2U Linkbox - Linkboxen mit Überschrift unter Bild',
            11);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-3',
            'D2U Linkbox - Farbboxen mit seitlichem Bild',
            8);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-4',
            'D2U Linkbox - Slider',
            7);
        $modules[] = new \TobiasKrais\D2UHelper\Module('24-5',
            'D2U Linkbox - Linkboxen mit Text neben dem Bild',
            4);
            $modules[] = new \TobiasKrais\D2UHelper\Module('24-6',
            'D2U Linkbox - Linkboxen mit Text und Hoverbild',
            1);
        return $modules;
    }
}
