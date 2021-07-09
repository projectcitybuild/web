<?php


namespace App\Library\Auditing\Contracts;


interface Recordable extends \Altek\Accountant\Contracts\Recordable
{
    /**
     * Get the URL to show the user a record
     *
     * @return string
     */
    public function getPanelShowUrl(): string;
}
