<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = ['name'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

     // Relationships

    public static function getPage($page, $pageLimit = 10)
    {
        $pagesCount = ceil(Domain::count() / $pageLimit);
        $offset = $pageLimit * ($page - 1);
        $start = $page > 3 ? max(min($page - 2, $pagesCount - 4), 1) : 1;
        $end = $start + 4 < $pagesCount ? $start + 4 : $pagesCount;
        return [
            'domains' => Domain::skip($offset)
                ->take($pageLimit)
                ->get()
                ->toArray(),
            'currentPage' => $page,
            'pagesCount' => $pagesCount,
            'start' => $start,
            'end' => $end
        ];
    }
}
