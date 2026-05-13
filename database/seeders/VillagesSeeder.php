<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SplFileObject;

class VillagesSeeder extends Seeder
{
    private string $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $countryPath = database_path('data/countries.json');
        $provincePath = database_path('data/provincies.json');
        $postalCodePath = database_path('data/postal_codes.csv');

        if (File::exists($countryPath) && File::exists($provincePath)) {
            $postalCodes = [];
            if (File::exists($postalCodePath)) {
                $this->command->info('Building postal code index from CSV...');
                $file = new SplFileObject($postalCodePath, 'r');
                $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

                $header = null;
                foreach ($file as $row) {
                    if (! is_array($row) || $row === [null] || $row === false) {
                        continue;
                    }

                    if ($header === null) {
                        $header = $row;

                        continue;
                    }

                    if (count($row) === count($header)) {
                        $record = array_combine($header, $row);
                        $postalCode = $this->normalizePostalCode($record['kode_pos'] ?? null);

                        if ($postalCode) {
                            // Generate keys for all name variants found in CSV
                            foreach ($this->generateCandidateKeys($record) as $key) {
                                $postalCodes[$key] = $postalCode;
                            }
                        }
                    }
                }
            }

            $this->command->info('Seeding countries from local files...');
            try {
                $countriesData = json_decode(File::get($countryPath), true);
                $provinces = json_decode(File::get($provincePath), true);

                foreach ($countriesData as $country) {
                    $iso = Str::lower((string) data_get($country, 'cca2', ''));
                    $officialName = str((string) data_get($country, 'name.official', ''))->squish()->value();
                    $commonName = str((string) data_get($country, 'name.common', ''))->squish()->value();

                    if ($iso === '' || $officialName === '' || $commonName === '') {
                        $this->command->warn("Skipping country with missing data: ISO: {$iso}, Official Name: {$officialName}, Common Name: {$commonName}");

                        continue;
                    }

                    $root = (string) data_get($country, 'idd.root', '');
                    $suffixes = data_get($country, 'idd.suffixes', []);
                    $suffix = is_array($suffixes) ? (string) ($suffixes[0] ?? '') : '';
                    $normalizedPhoneCode = str_replace(['+', ''], '', $root.$suffix);

                    $numcode = data_get($country, 'ccn3');
                    $numcode = ($numcode === '' || $numcode === null) ? null : (int) $numcode;

                    $iso3 = data_get($country, 'cca3');
                    $iso3 = ($iso3 === '' || $iso3 === null) ? null : $iso3;

                    DB::table('countries')->updateOrInsert(
                        ['iso' => $iso],
                        [
                            'name' => $officialName,
                            'nice_name' => $commonName,
                            'iso3' => $iso3,
                            'numcode' => $numcode,
                            'phonecode' => (int) $normalizedPhoneCode,
                            'status' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                foreach ($provinces as $province) {
                    // Ambil ISO negara, default ke 'id' jika key tidak ada di JSON
                    $rawIso = data_get($province, 'country_iso');
                    $countryIso = $rawIso ? Str::lower((string) $rawIso) : 'id';

                    // Hanya proses provinsi jika negara adalah Indonesia (iso: id)
                    if ($countryIso !== 'id') {
                        continue;
                    }

                    $country = DB::table('countries')->where('iso', $countryIso)->first();

                    if (! $country) {
                        $this->command->warn("Skipping province with missing country: {$countryIso}");

                        continue;
                    }

                    DB::table('provincies')->updateOrInsert(
                        ['id' => data_get($province, 'id')],
                        [
                            'country_id' => $country->id,
                            'name' => str((string) data_get($province, 'name', ''))->squish()->value(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                $this->command->info('Countries and Provinces seeded. Fetching sub-data for Indonesia...');

                // Ambil data City, District, Village hanya untuk Indonesia
                $indonesia = DB::table('countries')->where('iso', 'id')->first();

                if (! $indonesia) {
                    $this->command->error('Country "Indonesia" with ISO "id" not found in database.');

                    return;
                }

                $indoProvinces = DB::table('provincies')->where('country_id', $indonesia->id)->get();
                $this->command->info('Found '.$indoProvinces->count().' provinces to process.');

                $matched = 0;
                $notMatched = 0;

                foreach ($indoProvinces as $prov) {
                    $pName = $this->normalizeName($prov->name);
                    $cities = $this->fetchJson("{$this->baseUrl}/regencies/{$prov->id}.json");

                    if ($cities) {
                        foreach ($cities as $cityData) {
                            $cityName = $this->normalizeName($cityData['name']);
                            $type = Str::contains(strtoupper($cityName), 'KOTA') ? 'kota' : 'kabupaten';

                            DB::table('cities')->updateOrInsert(['id' => $cityData['id']], [
                                'province_id' => $prov->id,
                                'name' => $cityName,
                                'type' => $type,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Fetch Districts
                            $districts = $this->fetchJson("{$this->baseUrl}/districts/{$cityData['id']}.json");
                            if ($districts) {
                                foreach ($districts as $distData) {
                                    $dName = $this->normalizeName($distData['name']);
                                    DB::table('districts')->updateOrInsert(['id' => $distData['id']], [
                                        'city_id' => $cityData['id'],
                                        'name' => str((string) $distData['name'])->squish()->upper()->value(),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

                                    // Fetch Villages
                                    $villages = $this->fetchJson("{$this->baseUrl}/villages/{$distData['id']}.json");
                                    if ($villages) {
                                        $this->command->line("    Seeding villages for district: {$distData['name']}");
                                        foreach ($villages as $villData) {
                                            $vName = $this->normalizeName($villData['name']);
                                            $fullKey = $this->makeHierarchyKey($pName, $cityName, $dName, $vName);

                                            $postal = $fullKey ? ($postalCodes[$fullKey] ?? null) : null;
                                            $postal ? $matched++ : $notMatched++;

                                            DB::table('villages')->updateOrInsert(['id' => $villData['id']], [
                                                'district_id' => $distData['id'],
                                                'name' => str((string) $villData['name'])->squish()->upper()->value(),
                                                'postal_code' => $postal,
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                $this->command->warn("Failed to fetch districts for city ID: {$cityData['id']}");
                            }
                        }
                    } else {
                        $this->command->warn("No cities found or API error for province: {$prov->name} (ID: {$prov->id})");
                    }
                }

                $this->command->info("Postal matched: {$matched}, not matched: {$notMatched}");
                $this->command->info('Full hierarchy for Indonesia seeded successfully.');
            } catch (\Exception $e) {
                $this->command->error('Error seeding data: '.$e->getMessage());
            }
        } else {
            $this->command->error('Data files not found. Please ensure countries.json exist in the database/data directory.');
        }
    }

    /**
     * Fetch JSON data from URL without using cURL.
     */
    private function fetchJson(string $url): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withOptions(['verify' => false])
                ->retry(3, 500)
                ->timeout(60)
                ->get($url);

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            $this->command?->warn("Gagal mengambil {$url}: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Generate all possible hierarchy keys for a CSV record to maximize matching chances.
     */
    private function generateCandidateKeys(array $record): array
    {
        $provinceCandidates = $this->uniqueCandidateNames([
            $record['nama_kemendagri_provinsi'] ?? null,
            $record['nama_bps_provinsi'] ?? null,
        ]);

        $cityCandidates = $this->uniqueCandidateNames([
            $record['nama_kabupaten_kota'] ?? null,
        ], true);

        $districtCandidates = $this->uniqueCandidateNames([
            $record['kemendagri_nama_kecamatan'] ?? null,
            $record['bps_nama_kecamatan'] ?? null,
        ], true);

        $villageCandidates = $this->uniqueCandidateNames([
            $record['kemendagri_nama_desa_kelurahan'] ?? null,
            $record['bps_nama_desa_kelurahan'] ?? null,
        ], true);

        $keys = [];
        foreach ($provinceCandidates as $p) {
            foreach ($cityCandidates as $c) {
                foreach ($districtCandidates as $d) {
                    foreach ($villageCandidates as $v) {
                        $key = $this->makeHierarchyKey($p, $c, $d, $v);
                        if ($key) {
                            $keys[] = $key;
                        }
                    }
                }
            }
        }

        return array_unique($keys);
    }

    private function uniqueCandidateNames(array $values, bool $withLooseVariants = false): array
    {
        $candidates = [];
        foreach ($values as $value) {
            $normalized = $this->normalizeName($value);
            if ($normalized === '') {
                continue;
            }

            $candidates[$normalized] = $normalized;
            if ($withLooseVariants) {
                foreach ($this->looseVariants($normalized) as $variant) {
                    $candidates[$variant] = $variant;
                }
            }
        }

        return array_values($candidates);
    }

    private function looseVariants(string $value): array
    {
        $variants = [$value];
        $replacements = [
            '/\bKABUPATEN\b/' => '',
            '/\bKOTA\b/' => '',
            '/\bADM\b/' => '',
            '/\bADMINISTRASI\b/' => '',
            '/\bKECAMATAN\b/' => '',
            '/\bKELURAHAN\b/' => '',
            '/\bDESA\b/' => '',
            '/\bGAMPONG\b/' => '',
            '/\bKAMPUNG\b/' => '',
            '/\bNAGARI\b/' => '',
            '/\bDUSUN\b/' => '',
            '/\bTHE\b/' => '',
        ];

        $current = $value;
        foreach ($replacements as $pattern => $replacement) {
            $current = preg_replace($pattern, $replacement, $current) ?? $current;
        }
        $current = preg_replace('/\s+/', ' ', trim($current)) ?? trim($current);

        if ($current !== '') {
            $variants[] = $current;
            $variants[] = str_replace(' ', '', $current);
        }

        return array_values(array_unique($variants));
    }

    private function normalizeName(mixed $value): string
    {
        $value = mb_strtoupper(trim((string) $value));
        if ($value === '') {
            return '';
        }
        $value = str_replace(["'", '`', '.', ',', '(', ')', '/', '\\', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    private function normalizePostalCode(mixed $value): ?string
    {
        $value = preg_replace('/\D+/', '', trim((string) $value)) ?? '';

        return $value === '' ? null : $value;
    }

    private function makeHierarchyKey(?string $p, ?string $c, ?string $d, ?string $v): ?string
    {
        if (! $p || ! $c || ! $d || ! $v) {
            return null;
        }

        return implode('|', [
            str_replace(' ', '', $p), // $p is already normalized by normalizeName (single spaces)
            str_replace(' ', '', $c), // $c is already normalized by normalizeName (single spaces)
            str_replace(' ', '', $d), // $d is already normalized by normalizeName (single spaces)
            str_replace(' ', '', $v), // $v is already normalized by normalizeName (single spaces)
        ]);
    }
}
