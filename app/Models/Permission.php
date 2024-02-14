<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Permission extends Model
{
    use NodeTrait;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    public const permissions = [
        [
            'id' => 1,
            'name' => 'frontoffice_domain',
            'description' => 'FrontOffice',
            'children' => [
                ['id' => 2, 'name' => 'frontoffice.painel_page', 'description' => 'Painel'],
            ],
        ],
        [
            'id' => 3,
            'name' => 'backoffice_domain',
            'description' => 'Painel Administrativo',
            'children' => [
                ['id' => 4, 'name' => 'backoffice.painel_page', 'description' => 'Painel'],
            ],
        ],
    ];

    public static function getIds(): array
    {
        $ids = [];
        $loop = function ($data) use (&$loop, &$ids) {
            foreach ($data as $value) {
                if (array_key_exists('id', $value)) {
                    array_push($ids, $value['id']);
                    if (array_key_exists('children', $value)) {
                        $loop($value['children']);
                    }
                }
            }
        };

        $loop(self::permissions);

        return $ids;
    }
}
