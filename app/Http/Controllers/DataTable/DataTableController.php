<?php

namespace App\Http\Controllers\DataTable;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

abstract class DataTableController extends Controller
{
    protected $builder;

    abstract public function builder();

    public function __construct()
    {
        $builder = $this->builder();

        if(!$builder instanceof Builder) {
            throw new Exception('Entity builder not instance of builder');
        }

        $this->builder = $builder;

    }

    /**
     * Get records
     * @return Illuminate\Http\JsonResponse
     */

    public function index()
    {
        return response()->json([
            'data' => [
                'displayable' => array_values($this->getDisplayableColumns()),
                'records' => $this->getRecords()
            ]
        ]);
    }


    public function getDisplayableColumns()
    {
        return array_diff($this->getDatabaseColumns(), $this->builder->getModel()->getHidden());
    }

    protected function getDatabaseColumns()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }


    protected function getRecords()
    {
        return $this->builder->get($this->getDisplayableColumns());
    }
}
