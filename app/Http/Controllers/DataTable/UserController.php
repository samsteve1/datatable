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
            'id', 'name', 'email', 'created_at', 'updated_at'
        ];
    }

}
