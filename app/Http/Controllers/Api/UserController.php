<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\StoreUsersRequest;
use App\Http\Requests\Api\Users\UpdateUsersRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum usuário encontrado.']);
        }
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);

        return response()->json($user, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsersRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $user->update($validatedData);

        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Usuário atualizado com sucesso.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
