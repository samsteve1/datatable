<?php

namespace App\Http\Controllers\DataTable;

use App\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\DataTableDataTableController;

class PlanController extends DataTableController
{
    public function builder()
    {
        return  Plan::query();
    }

    public function getDisplayableColumns()
    {
        return [
            'id', 'paystack_id', 'price', 'active', 'created_at', 'updated_at'
        ];
    }

    public function getCreatableColumns()
    {
        return [
            'paystack_id', 'price', 'active'
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'paystack_id', 'price', 'active'
        ];
    }

    // public function update($id, Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         'telephone' => 'required'
    //     ]);
    //     $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    // }

    public function store(Request $request)
    {
        $this->validate($request, [
            'paystack_id' => 'required',
            'price' => 'required|numeric',
            ]);

        if (!$this->allowCreation) {
            return;
        }
        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

}
