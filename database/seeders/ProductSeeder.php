<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $now = Carbon::now();

        // 1. Seed Products
        $this->command?->info('Seeding products...');
        $sourceProducts = DB::connection('latihan')
            ->table('jpb_product')
            ->orderBy('id')
            ->get();

        foreach ($sourceProducts as $source) {
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('products')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'sku' => $source->sku,
                    'name' => $source->name,
                    'slug' => $source->slug,
                    'type' => strtolower($source->type),
                    'varian' => $source->varian,
                    'hu' => (int) $source->hu,
                    'bv' => (int) $source->bv,
                    'price_hpp' => (float) $source->price_hpp,
                    'price' => (float) $source->price,
                    'price_member' => (float) $source->price_member,
                    'price_customer' => (float) $source->price_customer,
                    'sponsor_point' => (float) $source->sponsor_point,
                    'pairing_point' => (float) $source->pairing_point,
                    'reward_point' => (float) $source->reward_point,
                    'stockist_point' => (float) $source->stockist_point,
                    'reward_budget' => (float) $source->reward_budget,
                    'weight' => $source->weight ? (float) $source->weight : null,
                    'stock' => (int) $source->stock,
                    'description' => $source->description,
                    'image' => $source->image,
                    'show_order' => (bool) $source->show_order,
                    'status' => (bool) $source->status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 2. Seed Product Variants
        $this->command?->info('Seeding product variants...');
        $sourceVariants = DB::connection('latihan')
            ->table('jpb_product_varian')
            ->orderBy('id')
            ->get();

        foreach ($sourceVariants as $source) {
            // Verify if parent product exists
            if (! DB::table('products')->where('id', $source->id_product)->exists()) {
                continue;
            }

            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('product_variants')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'product_id' => $source->id_product,
                    'name' => $source->name,
                    'varian' => $source->varian,
                    'bv' => $source->bv ? (float) $source->bv : null,
                    'price_hpp' => (float) $source->price_hpp,
                    'price' => (float) $source->price,
                    'weight' => (float) $source->weight,
                    'image' => $source->image,
                    'status' => (bool) $source->status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 3. Seed Product Packages
        $this->command?->info('Seeding product packages...');
        $sourcePackages = DB::connection('latihan')
            ->table('jpb_product_package')
            ->orderBy('id')
            ->get();

        foreach ($sourcePackages as $source) {
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('product_packages')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'sku' => $source->sku,
                    'name' => $source->name,
                    'slug' => $source->slug ?: str($source->name)->slug()->value(),
                    'type' => $source->type,
                    'hu' => (int) $source->hu,
                    'type_price' => $source->type_price,
                    'total_item' => (int) $source->total_item,
                    'total_qty' => (int) $source->total_qty,
                    'total_amount' => (float) $source->total_amount,
                    'bv' => (float) $source->bv,
                    'price' => (float) $source->price,
                    'weight' => $source->weight ? (float) $source->weight : null,
                    'sponsor_point' => (float) $source->sponsor_point,
                    'pairing_point' => (float) $source->pairing_point,
                    'stockist_point' => (float) $source->stockist_point,
                    'reward_point' => (float) $source->reward_point,
                    'image' => $source->image,
                    'description' => $source->description,
                    'show_order' => (bool) $source->show_order,
                    'status' => (bool) $source->status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 4. Seed Product Package Items
        $this->command?->info('Seeding product package items...');
        $sourceItems = DB::connection('latihan')
            ->table('jpb_product_package_item')
            ->orderBy('id')
            ->get();

        foreach ($sourceItems as $source) {
            // Verify package & product exist
            if (! DB::table('product_packages')->where('id', $source->id_package)->exists() ||
                ! DB::table('products')->where('id', $source->id_product)->exists()) {
                continue;
            }

            // Verify variant if applicable
            $variantId = null;
            if ($source->id_varian && DB::table('product_variants')->where('id', $source->id_varian)->exists()) {
                $variantId = $source->id_varian;
            }

            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('product_package_items')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'package_id' => $source->id_package,
                    'product_id' => $source->id_product,
                    'variant_id' => $variantId,
                    'qty' => (int) $source->qty,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }
}
