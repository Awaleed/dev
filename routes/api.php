<?php

use App\Filament\Resources\VendorResource;
use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankAccountController;
use App\Http\Controllers\API\CartInfoController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\OptionController;
use App\Http\Controllers\API\OptionGroupController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OTPController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\PaymentsController;
use App\Http\Controllers\API\PayoutController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\TechnicalSupportController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\VendorTransactionController;
use App\Http\Middleware\LogAfterRequest;
use App\Http\Resources\AppNotificationResource;
use App\Models\AppNotification;
use App\Models\AppUser;
use App\Models\Payout;
use App\Models\User;
use App\Models\Vendor;
use App\Services\LocationDetailsService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;

Route::middleware([LogAfterRequest::class])->group(function () {
    Route::post('otp/send', [OTPController::class, 'sendOTP']);
    Route::post('otp/verify', [OTPController::class, 'verifyOTP']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::delete('my/profile', fn () => ['message' => request()->header('Accept-Language') == 'ar' ? 'تم ارسال الطلب بنجاح سوف يقوم فريقنا بالتواصل معك لتاكيد حذف الحساب' : 'The request has been sent successfully, our team will contact you to confirm deletion of the account']);

    Route::prefix('user')->group(function () {
        Route::get('location_details', fn (Request $request) => LocationDetailsService::get($request->latitude, $request->longitude));
    });


    Route::post('products/{id}/menus', [ProductController::class, 'menus']);
    Route::apiResource('products', ProductController::class);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::get('products/{id}/photos', [ProductController::class, 'photos']);
    Route::post('products/{id}/photos', [ProductController::class, 'addPhoto']);
    Route::delete('/products/photos/{imageId}', [ProductController::class, 'deletePhoto']);
    Route::post('products/photos/order', [ProductController::class, 'orderPhotos']);
    // Route::('products/{id}/photos', [ProductController::class, 'photos']);

    Route::apiResource('offers', OfferController::class);

    Route::get('vendors/{id}/menus', [VendorController::class, 'menus']);
    Route::get('vendors/{id}/products', [VendorController::class, 'products']);
    Route::get('vendors/{id}/summery', [VendorController::class, 'summery']);
    Route::get('vendors/{id}/sales', [VendorController::class, 'sales']);
    Route::get('vendors/{id}/customers', [VendorController::class, 'customers']);
    Route::get('vendors/{id}/customers/{userId}', [VendorController::class, 'customer']);
    Route::get('vendors/{id}/reviews', [ReviewController::class, 'getVendorReviews']);
    Route::post('vendors/{id}/update', [VendorController::class, 'update']);
    Route::post('add_review', [ReviewController::class, 'addReview'])->middleware('auth:api');
    Route::apiResource('vendors', VendorController::class);


    Route::get('privacy_policy', fn () => setting('privacyPolicy', ''));
    Route::get('terms', fn () => setting('terms', ''));
    Route::get('about', fn () => setting('terms', ''));

    Route::get('payment_methods', [PaymentMethodController::class, 'index']);
    Route::get('user_search', [SearchController::class, 'vendorOrProduct']);
    Route::apiResource('categories', CategoryController::class);



    Route::prefix('search')->group(function () {
        Route::get('vendor_or_product', [SearchController::class, 'vendorOrProduct']);
        Route::get('vendor_or_product/autocomplete', [SearchController::class, 'vendorOrProductAutocomplete']);

        Route::get('order_or_product', [SearchController::class, 'orderOrProduct']);
        Route::get('order_or_product/autocomplete', [SearchController::class, 'orderOrProductAutocomplete']);
    });

    Route::webhooks('moyaserPaymentCallback');
    Route::any('order/{code}/payment/callback/moyaser', [PaymentsController::class, 'moyaserCallback']);


    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('my/transactions', [AppUserController::class, 'myTransactions']);
        // Auth::user()->vendor->earnings()->latest()->paginate()
        Route::get('/earnings', [VendorTransactionController::class, 'index']);
        // Auth::user()->teacher->earnings()->sum('amount')
        Route::get('/payout',  [PayoutController::class, 'index']);
        Route::post('/payout',  [PayoutController::class, 'store']);
        Route::post('/send_notification',  [NotificationController::class, 'store']);

        Route::get('/notifications', fn () => AppNotificationResource::collection(AppNotification::where('user_id', Auth::id())->latest()->paginate()));
        Route::get('/notifications/count', fn () => AppNotification::where('user_id', Auth::id())->whereNull('read_at')->count())->withoutMiddleware([LogAfterRequest::class]);
        Route::post('/notifications/all', fn () => AppNotification::where('user_id', Auth::id())->update(['read_at' => now()]));
        Route::post('/notifications/{id}', fn ($id) => AppNotification::findOrFail($id)->update(['read_at' => now()]));

        Route::apiResource('bank_accounts', BankAccountController::class);

        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites', [FavoriteController::class, 'toggleFavorite']);
        Route::get('my/profile', [AppUserController::class, 'myProfile']);
        Route::post('my/profile', [AppUserController::class, 'updateMyProfile']);
        Route::post('technical_support', [TechnicalSupportController::class, 'store']);
        Route::put('profile/password/update', [AuthController::class, 'changePassword']);
        Route::apiResource('coupons', CouponController::class);
        Route::apiResource('orders', OrderController::class)->only('index', 'store', 'show', 'update');

        Route::post('cart_info', [CartInfoController::class, 'getCartInfo']);

        $routes = [
            'menus' => MenuController::class,
            'options' => OptionController::class,
            'option_groups' => OptionGroupController::class,
            'employees' => EmployeeController::class,
        ];

        foreach ($routes as $key => $controller) {
            Route::get("$key/deleted", [$controller, 'trash']);
            Route::put("$key/{id}/restore", [$controller, 'restore']);

            Route::apiResource($key, $controller);
        }
    });
});
Route::get('test', function (Request $request) {

    $phones = [
        '557440440',
        '506168400',
        '544245000',
        '544245010',
        '544245210',
        // *******
        '0557440440',
        '0506168400',
        '0544245000',
        '0544245010',
        '0544245210',

    ];

    // find each AppUser with phone in $phones and $user->syncRoles('manager');
    $users = AppUser::whereIn('phone', $phones)->get();
    $users->each(function ($user) {
        $user->syncRoles('manager');
    });
    return $users;


    return;
    $latitude = request()->latitude;
    $longitude = request()->longitude;
    $key = env('GOOGLE_MAPS_API_KEY', '');

    $httpClint = new Client();
    $response = $httpClint->get(
        'https://maps.googleapis.com/maps/api/distancematrix/json',
        [
            'query' => [
                "key" => $key,
                "origins" => '21.562120430032,39.161102290027',
                "destinations" => '21.56331095160974,39.189901761710644',
            ]
        ]
    );

    $data = json_decode($response->getBody()->getContents(), true);
    return $data['rows'][0]['elements'][0]['duration']['text'];
    // $vendor = Vendor::latest()->first();
    // $appNotification = new AppNotification();

    // $appNotification->title_ar = 'مرحبا بكم في اسركم';
    // $appNotification->title_en = 'Welcome to OSRKM';
    // $appNotification->text_ar = 'حساب الاسرة المنتجة قيد المراجعة. سوف يقوم ممثل اسركم بمراجعة الحساب وتفعيله في غضون ذلك ، يمكنك تعديل منتجاتك وتفاصيل حسابك';
    // $appNotification->text_en = 'Your account is under review. OSRKM representative will review and activate the account in the meantime, you can edit your products and account details';

    // $vendor->sendNotificationToMangers($appNotification);

    // foreach (Payout::all() as $payout) {
    //     $payout->setStatus('pending');
    // }
});
