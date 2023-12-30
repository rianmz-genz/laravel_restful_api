<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectCreateRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller
{
    use Helper;

    public function create(ProjectCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        Log::info(json_encode($user));
        // Periksa apakah proyek dengan nama yang sama sudah ada
        $existingProjectCount = DB::table('projects')->where('name', $data['name'])->count();

        if ($existingProjectCount > 0) {
            throw new HttpResponseException(response([
                "errors" => "Project with the same name already exists"
            ], 400));
        }

        // Periksa apakah file gambar diunggah dan valid
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Simpan file gambar
            $path = $request->file('image')->store('images/projects', 'public');
            $uploadedImage = url('storage/' . $path);

            // Jalankan transaksi untuk operasi penyimpanan
            $query = 'INSERT INTO projects (name,description,image,user_id) VALUES (?,?,?,?)';
            // Lakukan operasi penyimpanan
            DB::insert($query, [
                $data['name'],
                $data['description'],
                $uploadedImage,
                $user->id
            ]);

            $querySelect = 'SELECT * FROM projects where name = ?';
            $newProject = DB::select($querySelect, [$data['name']])[0];

            return $this->basic_response(new ProjectResource($newProject), "Success to create project", 201);
        }

        throw new HttpResponseException(response([
            "errors" => "Invalid or missing image file"
        ], 400));
    }

    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();
        $projects = DB::select('SELECT * FROM projects WHERE user_id = ?', [$user->id]);
        return $this->basic_response(new ProjectCollection($projects), 'Success get all');
    }
}
