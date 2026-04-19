<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Manga Store API",
 *     description="API sederhana untuk ecommerce toko buku manga. Mock data disimpan dalam file JSON (non-database).",
 *     @OA\Contact(
 *         name="Muhammad Alvin Satria",
 *         email="[email protected]"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local development server"
 * )
 *
 * @OA\Schema(
 *     schema="Manga",
 *     type="object",
 *     required={"id", "title", "author", "price", "genre", "stock"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="One Piece Vol. 1"),
 *     @OA\Property(property="author", type="string", example="Eiichiro Oda"),
 *     @OA\Property(property="price", type="integer", example=35000),
 *     @OA\Property(property="genre", type="string", example="Shounen"),
 *     @OA\Property(property="stock", type="integer", example=15)
 * )
 *
 * @OA\Schema(
 *     schema="MangaInput",
 *     type="object",
 *     required={"title", "author", "price", "genre", "stock"},
 *     @OA\Property(property="title", type="string", example="Bleach Vol. 1"),
 *     @OA\Property(property="author", type="string", example="Tite Kubo"),
 *     @OA\Property(property="price", type="integer", example=37000),
 *     @OA\Property(property="genre", type="string", example="Shounen"),
 *     @OA\Property(property="stock", type="integer", example=10)
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Item dengan ID 99 tidak Ditemukan")
 * )
 */
class MangaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/manga",
     *     summary="Menampilkan semua manga",
     *     tags={"Manga"},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar manga berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Daftar manga berhasil diambil"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Manga"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $data = Manga::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar manga berhasil diambil',
            'data'    => $data,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/manga/{id}",
     *     summary="Menampilkan manga berdasarkan ID",
     *     tags={"Manga"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID manga",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail manga ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Detail manga ditemukan"),
     *             @OA\Property(property="data", ref="#/components/schemas/Manga")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Manga tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $item = Manga::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => "Item dengan ID {$id} tidak Ditemukan",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail manga ditemukan',
            'data'    => $item,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/manga",
     *     summary="Menambahkan manga baru",
     *     tags={"Manga"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MangaInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Manga berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Manga berhasil ditambahkan"),
     *             @OA\Property(property="data", ref="#/components/schemas/Manga")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'  => 'required|string|max:150',
            'author' => 'required|string|max:100',
            'price'  => 'required|integer|min:0',
            'genre'  => 'required|string|max:50',
            'stock'  => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $newItem = Manga::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Manga berhasil ditambahkan',
            'data'    => $newItem,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/manga/{id}",
     *     summary="Mengedit seluruh data manga (replace)",
     *     tags={"Manga"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID manga",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MangaInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Manga berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Manga berhasil diperbarui"),
     *             @OA\Property(property="data", ref="#/components/schemas/Manga")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Manga tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (!Manga::find($id)) {
            return response()->json([
                'success' => false,
                'message' => "Item dengan ID {$id} tidak Ditemukan",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'  => 'required|string|max:150',
            'author' => 'required|string|max:100',
            'price'  => 'required|integer|min:0',
            'genre'  => 'required|string|max:50',
            'stock'  => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $updated = Manga::update($id, $validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Manga berhasil diperbarui',
            'data'    => $updated,
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/manga/{id}",
     *     summary="Mengedit sebagian data manga (partial update)",
     *     tags={"Manga"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID manga",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="One Piece Vol. 1 (Revisi)"),
     *             @OA\Property(property="price", type="integer", example=40000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Manga berhasil diperbarui sebagian",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Manga berhasil diperbarui sebagian"),
     *             @OA\Property(property="data", ref="#/components/schemas/Manga")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Manga tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function partialUpdate(Request $request, int $id): JsonResponse
    {
        if (!Manga::find($id)) {
            return response()->json([
                'success' => false,
                'message' => "Item dengan ID {$id} tidak Ditemukan",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'  => 'sometimes|required|string|max:150',
            'author' => 'sometimes|required|string|max:100',
            'price'  => 'sometimes|required|integer|min:0',
            'genre'  => 'sometimes|required|string|max:50',
            'stock'  => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim untuk diperbarui',
            ], 422);
        }

        $updated = Manga::update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Manga berhasil diperbarui sebagian',
            'data'    => $updated,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/manga/{id}",
     *     summary="Menghapus manga berdasarkan ID",
     *     tags={"Manga"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID manga",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Manga berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Manga berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Manga tidak ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = Manga::delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => "Item dengan ID {$id} tidak Ditemukan",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Manga berhasil dihapus',
        ], 200);
    }
}
