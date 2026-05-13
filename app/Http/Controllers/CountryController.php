<?php

namespace App\Http\Controllers\Api;

use App\Domain\Country\Actions\CreateCountryAction;
use App\Domain\Country\Actions\DeleteCountryAction;
use App\Domain\Country\Actions\UpdateCountryAction;
use App\Domain\Country\Data\CountryData;
use App\Domain\Country\Rules\CreateCountryRules;
use App\Domain\Country\Rules\UpdateCountryRules;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    /**
     * Menampilkan daftar semua negara.
     */
    public function index(): JsonResponse
    {
        $countries = Country::all();

        return response()->json(CountryData::collection($countries));
    }

    /**
     * Menyimpan negara baru.
     */
    public function store(Request $request, CreateCountryAction $createCountryAction): JsonResponse
    {
        // Validasi request menggunakan aturan yang telah didefinisikan
        $validatedData = $request->validate((new CreateCountryRules)->execute());

        // Buat DTO dari data yang divalidasi
        $countryData = CountryData::fromRequest($validatedData);

        // Jalankan Action untuk membuat negara
        $country = $createCountryAction->execute($countryData);

        return response()->json(CountryData::fromModel($country), Response::HTTP_CREATED);
    }

    /**
     * Menampilkan detail negara tertentu.
     */
    public function show(Country $country): JsonResponse
    {
        // Laravel secara otomatis melakukan Route Model Binding
        return response()->json(CountryData::fromModel($country));
    }

    /**
     * Memperbarui negara tertentu.
     */
    public function update(Request $request, Country $country, UpdateCountryAction $updateCountryAction): JsonResponse
    {
        // Validasi request menggunakan aturan pembaruan, mengabaikan ID negara saat ini
        $validatedData = $request->validate((new UpdateCountryRules)->execute($country->id));

        // Buat DTO dari data yang divalidasi
        $countryData = CountryData::fromRequest($validatedData);

        // Jalankan Action untuk memperbarui negara
        $updatedCountry = $updateCountryAction->execute($country, $countryData);

        return response()->json(CountryData::fromModel($updatedCountry));
    }

    /**
     * Menghapus negara tertentu.
     */
    public function destroy(Country $country, DeleteCountryAction $deleteCountryAction): Response
    {
        $deleteCountryAction->execute($country);

        return response()->noContent();
    }
}
