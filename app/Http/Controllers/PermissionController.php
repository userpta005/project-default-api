<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    public function permissionsToTree(): JsonResponse
    {
        $data = Permission::query()
            ->select(['id', 'name', 'description', '_lft', '_rgt', 'parent_id'])
            ->get()
            ->toTree();

        return response()->json([
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function userPermissions(): JsonResponse
    {
        $user = User::query()->findOrFail(auth()->id());
        $data = $user->allPermissions();

        return response()->json([
            'data' => $data,
        ], Response::HTTP_OK);
    }
}
