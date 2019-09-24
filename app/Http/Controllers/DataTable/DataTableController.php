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
                'table' => $this->getTableName(),
                'displayable' => array_values($this->getDisplayableColumns()),
                'updatable' => $this->getUpdatableColumns(),
                'records' => $this->getRecords()
            ]
        ]);
    }


    public function getDisplayableColumns()
    {
        return array_diff($this->getDatabaseColumns(), $this->builder->getModel()->getHidden());
    }

    public function getUpdatableColumns()
    {
        return $this->getDisplayableColumns();
    }
    protected function getTableName()
    {
        return ucfirst($this->builder->getModel()->getTable());
    }
    protected function getDatabaseColumns()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    public function update($id, Request $request)
    {
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }


    protected function getRecords()
    {
        return $this->builder->limit(request()->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
    }
}
