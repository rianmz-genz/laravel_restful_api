<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use Helper;
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $existingUserCount = DB::select("SELECT COUNT(*) as count FROM users WHERE username = ?", [$data['username']])[0]->count;

        if ($existingUserCount == 1) {
            throw new HttpResponseException(response([
                "errors" => "username already registered"
            ], 400));
        }

        $data['password'] = Hash::make($data['password']);

        $userId = DB::table('users')->insertGetId($data);

        $user = DB::select("SELECT * FROM users WHERE id = ?", [$userId])[0];

        return $this->basic_response(new UserResource($user), 'Success to register user', 201);
    }


    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::select("SELECT * FROM users WHERE username = ? LIMIT 1", [$data['username']]);

        if (empty($user) || !Hash::check($data['password'], $user[0]->password)) {
            throw new HttpResponseException(response([
                "errors" => "username or password wrong"
            ], 401));
        }

        $user = $user[0];
        $token = Str::uuid()->toString();

        DB::update("UPDATE users SET token = ? WHERE id = ?", [$token, $user->id]);
        $userHasLogin = DB::selectOne("SELECT * FROM users WHERE username = ?", [$data['username']]);
        return $this->basic_response(new UserResource($userHasLogin), 'Succes login');
    }

    public function get(Request $request): JsonResponse
    {
        $user = Auth::user();
        return $this->basic_response(new UserResource($user), 'Success to get user');
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = Auth::user();

        // Buat array untuk menyimpan kolom dan nilai yang akan diupdate
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Lakukan update menggunakan raw query
        DB::table('users')
            ->where('id', $user->id)
            ->update($updateData);

        // Ambil data pengguna yang baru saja diupdate
        $updatedUser = DB::table('users')->find($user->id);

        return $this->basic_response(new UserResource($updatedUser), 'Success to update user');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();

        DB::update("UPDATE users SET token = null WHERE id = ?", [$user->id]);

        return $this->basic_response(true, 200);
    }
}
