<?php
/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class D2ULinkboxModules
{
    /**
     * Get modules offered by this addon.
     * @return D2UModule[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new D2UModule('24-1',
            'D2U Linkbox - Linkboxen mit Überschrift in Bild',
            8);
        $modules[] = new D2UModule('24-2',
            'D2U Linkbox - Linkboxen mit Überschrift unter Bild',
            9);
        $modules[] = new D2UModule('24-3',
            'D2U Linkbox - Farbboxen mit seitlichem Bild',
            6);
        $modules[] = new D2UModule('24-4',
            'D2U Linkbox - Slider',
            5);
        $modules[] = new D2UModule('24-5',
            'D2U Linkbox - Linkboxen mit Text neben dem Bild',
            2);
        return $modules;
    }
}
