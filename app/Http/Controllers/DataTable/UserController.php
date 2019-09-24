<?php

namespace App\Http\Controllers\DataTable;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\DataTableDataTableController;

class UserController extends DataTableController
{
    public function builder()
    {
        return  User::query();
    }

    public function getDisplayableColumns()
    {
        return [
            'id', 'name', 'email', 'created_at', 'telephone'
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'name', 'email', 'telephone', 'created_at'
        ];
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'telephone' => 'required'
        ]);
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

}
