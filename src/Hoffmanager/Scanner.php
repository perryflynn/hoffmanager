<?php

namespace Hoffmanager;

use PerrysLambda\ArrayList;
use PerrysLambda\ObjectArray;
use PerrysLambda\Converter\TypeStringListConverter;

class Scanner
{

    protected $sources;
    protected $ext;

    public function __construct(array $sourcedirectories, array $validextensions)
    {
        $this->sources = $sourcedirectories;
        $this->ext = $validextensions;
    }

    public function scanall()
    {
        $list = new ObjectArray();
        foreach($this->sources as $source)
        {
            if(is_dir($source))
            {
                $this->appendSource($list, $source);
            }
            else
            {
                throw new \InvalidArgumentException("Not a valid directory");
            }
        }
        return $list;
    }

    protected function appendSource(ObjectArray $list, $source)
    {
        $inputparser = new TypeStringListConverter('\Hoffmanager\MusicFile');
        $inputparser->setIteratorSource(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS)));
        $temp = new ObjectArray($inputparser);
        $temp
            ->where(function($v) { return in_array($v->getExtension(), $this->ext); })
            ->each(function($v) use($list) { $list->add($v); });
    }

}
