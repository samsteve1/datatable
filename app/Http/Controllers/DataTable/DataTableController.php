<?php

namespace App\Http\Controllers\DataTable;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

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

    public function index(Request $request)
    {
        return response()->json([
            'data' => [
                'table' => $this->getTableName(),
                'displayable' => array_values($this->getDisplayableColumns()),
                'updatable' => $this->getUpdatableColumns(),
                'records' => $this->getRecords($request)
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


    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if($this->hasSearchQuery($request)) {
            //dd('ok');
            $builder = $this->buildSearch($builder, $request);
        }

        try {
            return $this->builder->limit(request()->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());
        }
        catch (QueryException $e) {
            return [];
        }


    }

    protected function hasSearchQuery(Request $request)
    {
        return count(array_filter($request->only(['column', 'operator', 'value']))) === 3;
    }
    protected function buildSearch(Builder $builder, Request $request)
    {
        $queryParts = $this->resolveQueryParts($request->operator, $request->value);

        return $builder->where($request->column, $queryParts['operator'], $queryParts['value']);
    }

    protected function resolveQueryParts($operator, $value)
    {
        return array_get([
            'equals' => [
                'operator' => '=',
                'value' => $value
            ],
            'contains' => [
                'operator' => 'LIKE',
                'value' => "%{$value}%"
            ],
            'starts_with' => [
                'operator' => 'LIKE',
                'value' => "{$value}%"
            ],
            'ends_with' => [
                'operator' => 'LIKE',
                'value' => "%{$value}%"
            ],
            'greater_than' => [
                'operator' => '>',
                'value' => $value
            ],
            'less_than' => [
                'operator' => '<',
                'value' => $value
            ],
            'less_than_equal' => [
                'operator' => '<=',
                'value' => $value
            ],
            'greater_than_equal' => [
                'operator' => '>=',
                'value' => $value
            ]
            ], $operator);
    }
}
