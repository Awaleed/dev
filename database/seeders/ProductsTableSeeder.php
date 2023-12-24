<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Option;
use App\Models\OptionGroup;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// use FakerRestaurant\Provider\en_US\Restaurant;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->delete();

        $faker = \Faker\Factory::create();
        // $faker->addProvider(new Restaurant($faker));
        $totalProducts = 10; // rand(1, 10);
        $vendors = Vendor::all()->pluck('id');
        echo $vendors;
        //
        foreach ($vendors as $vendor) {
            echo 'dowing ' . $vendor;

            for ($i = 0; $i < $totalProducts; $i++) {

                $menus = Menu::inRandomOrder()->where('vendor_id', $vendor)->limit(2)->get();
                $product = new Product();
                // $product->name = $this->productName($faker, $model->category_id);
                // $product->description = $faker->paragraph();
                $product->name_ar = $this->productName($faker, $product->category_id);
                $product->name_en = $this->productName($faker, $product->category_id);
                $product->description_ar = $faker->paragraph();
                $product->description_en = $faker->paragraph();
                $product->preparation_time = $faker->randomNumber(5, false);
                $product->price = $faker->randomNumber(2, false);
                $product->discount_price = $faker->randomNumber(1, false);
                $product->capacity = "" . $faker->randomNumber(5, false) . "";
                $product->unit = "ml";
                $product->package_count = $faker->randomNumber(1, false);
                $product->featured = rand(0, 1);
                $product->deliverable = rand(0, 1);
                $product->is_active = 1;
                $product->vendor_id = $vendor;
                $product->save();

                $product->menus()->sync($menus->pluck('id'));

                //
                $imageUrl = str_ireplace(" ", "", $product->name);
                $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();


                for ($j = 0; $j < 3; $j++) {
                    $optionGroup = new OptionGroup();
                    $optionGroup->name_ar = $faker->company;
                    $optionGroup->name_en = $faker->company;
                    $optionGroup->product_id = $product->id;
                    $optionGroup->is_active = 1;
                    $optionGroup->save();
                    for ($k = 0; $k < 3; $k++) {
                        $model = new Option();
                        $model->name_ar = $faker->company;
                        $model->name_en = $faker->company;
                        $model->description = $faker->company;
                        $model->price = rand(1, 10);
                        $model->option_group_id = $optionGroup->id;
                        $model->is_active = 1;
                        $model->save();
                    }
                }
            }
        }
        // \DB::table('products')->insert(array (
        //     0 =>
        //     array (
        //         'id' => 1,
        //         'name' => 'Milk',
        //         'description' => 'Very sweet milk',
        //         'price' => 3.5,
        //         'discount_price' => 0.0,
        //         'capacity' => '300',
        //         'unit' => 'ml',
        //         'package_count' => '1',
        //         'featured' => 1,
        //         'deliverable' => 1,
        //         'is_active' => 1,
        //         'vendor_id' => 2,
        //         'category_id' => 1,
        //         'created_at' => '2021-01-09 00:44:51',
        //         'updated_at' => '2021-01-09 10:09:29',
        //     ),
        //     1 =>
        //     array (
        //         'id' => 2,
        //         'name' => 'Carrot',
        //     'description' => ' A flat bread (just like the Greeks used to bake long time back) layered with juicy tomato sauce and topped with fresh veggies as pizza toppings and shredded mozzarella cheese is now, better known as nothing but Pizza.',
        //         'price' => 3.0,
        //         'discount_price' => 0.5,
        //         'capacity' => '1',
        //         'unit' => 'g',
        //         'package_count' => '6',
        //         'featured' => 1,
        //         'deliverable' => 1,
        //         'is_active' => 1,
        //         'vendor_id' => 2,
        //         'category_id' => 2,
        //         'created_at' => '2021-01-09 09:56:55',
        //         'updated_at' => '2021-01-09 10:08:24',
        //     ),
        // ));
    }


    public function productName($faker, $categoryID)
    {
        $name = "";
        switch ($categoryID) {
            case 1:
                $name = $faker->beverageName();
                break;
            case 2:
                $name = $faker->dairyName();
                break;
            case 3:
                $name = $faker->dairyName();
                break;
            case 4:
                $name = $faker->dairyName();
                break;
            case 5:
                $name = $faker->foodName();
                break;
            case 6:
                $name = $faker->meatName();
                break;
            case 7:
                $name = $faker->vegetableName();
                break;
            default:
                $name = $faker->foodName();
                break;
        }
        return $name;
    }
}
