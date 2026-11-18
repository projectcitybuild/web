<?php

namespace App\Providers;

use Spatie\TypeScriptTransformer\Data\Location;
use Spatie\TypeScriptTransformer\Data\WritingContext;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNamespace;
use Spatie\TypeScriptTransformer\Writers\GlobalNamespaceWriter;

class VueCompatibleGlobalNamespaceWriter extends GlobalNamespaceWriter
{
    protected function buildNamespace(Location $location): TypeScriptNamespace
    {
        return new TypeScriptNamespace(
            $location->name,
            $location->transformed,
            array_map(fn (Location $child): TypeScriptNamespace => $this->buildExportedNamespace($child), $location->children),
        );
    }

    private function buildExportedNamespace(Location $location): TypeScriptNamespace
    {
        $children = array_map(
            fn (Location $child): TypeScriptNamespace => $this->buildExportedNamespace($child),
            $location->children,
        );

        return new class($location->name, $location->transformed, $children) extends TypeScriptNamespace
        {
            public function write(WritingContext $context): string
            {
                return 'export '.parent::write($context);
            }
        };
    }
}
