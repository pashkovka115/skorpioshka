<?php


namespace Core\Console\Traits;


trait Helper
{
    public function migrate_exists($file)
    {
        $sp = explode('_', $file);
        array_shift($sp);
        array_shift($sp);
        array_shift($sp);
        array_shift($sp);

//        echo implode('_', $sp);

        $files = glob(MIGRATIONS_PATH . '/*' . implode('_', $sp));
//        print_r($files);
        foreach ($files as $item) {
            $sp2 = explode('_', $item);
            $g_file = $sp2[count($sp2) - 1];
            $sp3 = explode('_', $g_file);
            if (implode('_', $sp) == $sp3[count($sp3) - 1]){
                return true;
            }
            require $item;
        }
        $classes = get_declared_classes();
        foreach ($classes as $class){
            $rc = new \ReflectionClass($class);
            if ($rc->isUserDefined() and $rc->implementsInterface('\Core\Migrations\Migration')){
                return true;
            }
        }
    }
}