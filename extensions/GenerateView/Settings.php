<?php


class Settings
{
    use \Core\Helpers\ToArray;

    private $teachers = [
        '__name'=>'Учителя',
        '__list' => ['id'=>'ID', 'name'=>'Имя', 'content'=>'Текст'],
        'name'=>[
            'name'=>'Имя',
            'description' => 'Имя учителя',
            'type'=>'input/text',
        ],
        'content'=>[
            'name'=>'Контент',
            'description' => 'Текст записи',
            'type'=>'textarea',
        ],
    ];
}

return (new Settings())->toArray();