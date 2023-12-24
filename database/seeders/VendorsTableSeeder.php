<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Option;
use App\Models\OptionGroup;
use App\Models\Product;
use App\Models\AppUser;
use App\Models\Vendor;
use Exception;
use FakerRestaurant\Provider\ar_SA\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('ar_SA');
        $faker->addProvider(new Restaurant($faker));
        for ($i = 0; $i < 1000; $i++) {
            print('Creating vendor number ' . $i . '
            ');

            $name = $faker->name;
            $email = $faker->email;
            $phone = '0' . rand(500000000, 599999999);
            $vendor_name_ar = $faker->company;
            $vendor_name_en = $faker->company;
            $vendor_email = $faker->email;

            // dd($name, $email, $phone, $vendor_name_ar, $vendor_name_en, $vendor_email);
            // return;

            //
            $user = AppUser::where('phone', $phone)->first();
            if (!empty($user)) throw new Exception(__("Account with phone already exists"), 1);

            try {
                DB::beginTransaction();

                //
                $user = new AppUser();
                $user->name = $name ?? '';
                $user->email = $email ?? '';
                $user->phone = $phone;
                $user->country_code = "";
                $user->password = Hash::make('');
                $user->is_active = true;
                $user->save();
                $user->syncRoles('manager');
                $user->addMediaFromUrl("https://source.unsplash.com/1080x1080/?user")->toMediaCollection('profile');

                logger('$user->id = ' . $user->id);

                // create vendor
                $vendor = new Vendor();
                $vendor->name_ar = $vendor_name_ar;
                $vendor->name_en = $vendor_name_en;
                $vendor->email = $vendor_email;
                $vendor->phone = $phone;
                $vendor->is_active = true;
                $vendor->latitude = $faker->latitude;
                $vendor->longitude = $faker->longitude;
                $vendor->address = '';
                $vendor->description = $faker->realText;
                $vendor->delivery_fee = rand(10, 50);
                $vendor->min_order = rand(10, 50);
                $vendor->delivery_time  = '' . rand(10, 30) . ' - ' . rand(30, 60);
                $vendor->save();
                logger('$vendor->id = ' . $vendor->id);

                $categories = Category::inRandomOrder()->limit(3)->get()->pluck('id');
                $vendor->categories()->sync($categories);


                $vendor->addMediaFromUrl("https://source.unsplash.com/1080x1080/?logo")->toMediaCollection('logo');
                $vendor->addMediaFromUrl("https://source.unsplash.com/1920x1080/?food")->toMediaCollection('feature_image');

                // assign manager to vendor
                $user->vendor_id = $vendor->id;
                $user->save();


                for ($menuIndex = 0; $menuIndex < rand(3, 10); $menuIndex++) {
                    $menu = new Menu();
                    $menu->name_ar = $faker->company;
                    $menu->name_en = $faker->company;
                    $menu->is_active = 1;
                    $menu->vendor_id = $vendor->id;
                    $menu->save();
                    logger('$menuIndex = ' . $menuIndex . ' || $menu->id = ' . $menu->id);

                    for ($productIndex = 0; $productIndex < rand(3, 10); $productIndex++) {
                        $product = new Product();
                        $product->name_ar = $faker->foodName;
                        $product->name_en = $faker->foodName;
                        $product->description_ar = $faker->realText;
                        $product->description_en = $faker->realText;
                        $product->preparation_time = $faker->randomNumber(5, false);
                        $product->price = $faker->randomNumber(2, false);
                        $product->discount_price = $faker->randomNumber(1, false);
                        $product->capacity = "" . $faker->randomNumber(5, false) . "";
                        $product->unit = "ml";
                        $product->package_count = $faker->randomNumber(1, false);
                        $product->featured = rand(0, 1);
                        $product->deliverable = rand(0, 1);
                        $product->is_active = 1;
                        $product->vendor_id = $vendor->id;
                        $product->save();
                        logger('$productIndex = ' . $productIndex . ' || $product->id = ' . $product->id);

                        $product->menus()->sync($menu->id);

                        $categories = Category::inRandomOrder()->limit(3)->get()->pluck('id');
                        $product->categories()->sync($categories);

                        //
                        $imageUrl = str_ireplace(" ", "", $product->name);
                        $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?food," . $imageUrl . "")->toMediaCollection();
                        // $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                        // $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                        // $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();
                        // $product->addMediaFromUrl("https://source.unsplash.com/1280x720/?product," . $imageUrl . "")->toMediaCollection();


                        for ($optionGroupIndex = 0; $optionGroupIndex < rand(3, 10); $optionGroupIndex++) {
                            $optionGroup = new OptionGroup();
                            $optionGroup->name_ar = $faker->company;
                            $optionGroup->name_en = $faker->company;
                            $optionGroup->product_id = $product->id;
                            $optionGroup->is_active = 1;
                            $optionGroup->save();
                            // logger('$optionGroupIndex = ' . $optionGroupIndex . ' || $optionGroup->id = ' . $optionGroup->id );


                            for ($optionIndex = 0; $optionIndex < rand(3, 10); $optionIndex++) {
                                $option = new Option();
                                $option->name_ar = $faker->company;
                                $option->name_en = $faker->company;
                                $option->description = $faker->company;
                                $option->price = rand(0, 10);
                                $option->option_group_id = $optionGroup->id;
                                $option->is_active = 1;
                                $option->save();
                                // logger('$optionIndex = ' . $optionIndex . ' || $option->id = ' . $option->id );
                            }
                        }
                    }
                }
                logger('-->Finished<--');

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                logger()->error('ErrorMsg', ['Throwable' => $th]);
                // throw $th;
            }
            print('Finished Vendor number' . $i . '
            ');
        }

        return;
        DB::table('vendors')->delete();

        $faker = \Faker\Factory::create();
        $totalVendors = rand(3, 9);
        for ($i = 0; $i < $totalVendors; $i++) {
            $model = new Vendor();
            $model->name_ar = $faker->company;
            $model->name_en = $faker->company;
            $model->description = $faker->catchPhrase;
            $model->delivery_fee = $faker->randomNumber(2, false);
            $model->delivery_range = $faker->randomNumber(3, false);
            $model->tax = $faker->randomNumber(2, false);
            $model->phone = $faker->phoneNumber;
            $model->email = $faker->email;
            $model->address = $faker->address;
            $model->latitude = $faker->latitude();
            $model->longitude = $faker->longitude();
            $model->tax = rand(0, 1);
            $model->pickup = rand(0, 1);
            $model->delivery = rand(0, 1);
            $model->is_active = 1;
            $model->save();

            //
            $model->addMediaFromUrl("https://source.unsplash.com/800x480/?logo")->toMediaCollection("logo");
            $model->addMediaFromUrl("https://source.unsplash.com/1280x720/?vendor")->toMediaCollection("feature_image");
        }
    }
}
