<?php
namespace App\Modules\Bans;

interface BannableModelInterface {
    
    /**
     * Returns the data of a field used to identify a player
     * in banning.
     * 
     * For example, for Minecraft return the UUID field
     *
     * @return string
     */
    public function getBanIdentifier() : string;
}