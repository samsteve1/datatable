<?php

namespace App\Http\Controllers\DataTable;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\DataTableDataTableController;

class UserController extends DataTableController
{
    protected $allowCreation = true;

    protected $allowDeletion = true;
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
    public function getCreatableColumns()
    {
        return [
            'name', 'email', 'telephone'
        ];
    }

    public function getUpdatableColumns()
    {
        return [
            'name', 'email', 'telephone', 'created_at'
        ];
    }

    public function getCustomColumnNames()
    {
        return [
            'email' => 'Email address',
            'name' => 'Full name',
            'telephone' => 'Phone Number',
            'created_at' => 'Date Registered'
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
