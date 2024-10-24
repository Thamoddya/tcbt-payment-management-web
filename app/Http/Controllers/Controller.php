<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function addCashier(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nic' => 'required|unique:users,nic',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nic' => $request->nic,
        ]);

        // Assign 'Cashier' role to the user
        $user->assignRole('Receptionist');

        return response()->json([
            'message' => 'Cashier added successfully',
            'data' => $user,
            'status' => 200,
        ]);
    }

    public function getCashiers($id)
    {
        $cashier = User::findOrFail($id);

        return response()->json([
            'message' => 'Cashier fetched successfully',
            'cashier' => $cashier,
            'status' => 200,
        ]);
    }

    public function updateCashier(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'nic' => 'required|unique:users,nic,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $cashier = User::findOrFail($id);
        $cashier->name = $request->name;
        $cashier->email = $request->email;
        $cashier->nic = $request->nic;

        if ($request->filled('password')) {
            $cashier->password = Hash::make($request->password);
        }

        $cashier->save();

        return response()->json([
            'message' => 'Cashier updated successfully',
            'status' => 200,
        ]);
    }
}
