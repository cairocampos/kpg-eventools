<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function __construct(User $repository) {
        $this->repository = $repository;
    }

    public function index() 
    {
        $users = $this->repository->where("id", '!=', auth()->id())->paginate();

        return $users;
    }
}
