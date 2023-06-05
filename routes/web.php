<?php

//	Route::get('/info',function(){
//
//		phpinfo();
//
//	});
//    Route::get('/migrate',function(){
//        \Artisan::call('migrate',[
//            '--force' => true
//         ]);
//        return \Artisan::output();
//    });
//    Route::get('/schedule',function(){
//        \Artisan::call('schedule:run');
//        return \Artisan::output();
//    });
//	Route::get( '/zacra-migrate', 'App\CommandController@migrateZacra');
//    Route::get( '/clear-cache', 'App\CommandController@clearCache');

    /**
     * Error
     */

    Route::get('connection-error', 'Error\ErrorController@BTCConnectionError')->name('btc-connection');
    //Route::get('419', 'Error\ErrorController@sessionExpired');
    Route::get('403', 'Error\ErrorController@forbidden');
    Route::get('404', 'Error\ErrorController@notFound')->name('not-found');
    Route::get('500', 'Error\ErrorController@fatal');
    Route::get('503', 'Error\ErrorController@serviceUnavailable');
    Route::get('no-permissions', 'Error\ErrorController@noPermissions')->name('no-permissions');
    Route::get('maintenance', 'Error\ErrorController@maintenance')->name('maintenance');
       Route::get('search-address', 'Shop\ShopController@searchAddresses')->name('buy-product-form-search-address');
    /**
     * API
     */

    Route::get('/api/product/database/import', 'API\ProductDatabaseImportController@databaseImport')->name('api-product-database-import');
    Route::post('/api/product/database/import', 'API\ProductDatabaseImportController@databaseImport');

    Route::get('/api/product/database/lbl/import', 'API\ProductDatabaseImportController@databaseImportLineByLine')->name('api-product-database-line-by-line-import');
    Route::post('/api/product/database/lbl/import', 'API\ProductDatabaseImportController@databaseImportLineByLine');

    Route::get('/api/product/database/seperator/import', 'API\ProductDatabaseImportController@databaseImportSeperator')->name('api-product-database-seperator-import');
    Route::post('/api/product/database/seperator/import', 'API\ProductDatabaseImportController@databaseImportSeperator');

    Route::get('/api/bitcoin/info', 'API\BitcoinWalletController@bitcoinWalletInfo')->name('api-bitcoin-wallet-info');
    Route::post('/api/bitcoin/info', 'API\BitcoinWalletController@bitcoinWalletInfo');

    /**
     * Frontend
     */

    Route::get('/custom/css', 'Custom\CSSController@generateCustomCSS')->name('custom-css');
    Route::get('/custom/colors', 'Custom\CSSController@generateOverridingColorsCSS')->name('custom-colors');

    # HomePage
    Route::get( '/', 'App\HomePageController@showIndex' )->name( 'home_page' );

    # Default
    // Route::get('/', 'App\DefaultController@showIndex')->name('index');
    Route::get('/page/{page?}', 'App\DefaultController@showIndex')->name('index');

    # Auth
//     Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

    #Route::post('login', 'Auth\LoginController@login')->name('login');
    #Route::get( 'login', 'Error\ErrorController@notFound');


    # Auth
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

    Route::post('login', 'Auth\LoginController@userLogin')->name('userlogin');
    //Route::get( 'login', 'Error\ErrorController@notFound');




    Route::get('ausloggen', 'Auth\LoginController@logout')->name('logout');
    Route::post('ausloggen', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    /*
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    */

    # UserPanel
    // Route::get('home', 'UserPanel\UserPanelController@showUserDashboard')->name('home');

    Route::post('coupon/redeem', 'UserPanel\UserPanelController@redeemCoupon')->name('redeem-coupon');

    Route::get('einstellungen', 'UserPanel\UserPanelController@showSettingsPage')->name('settings');

    Route::get('settings/password/change', 'UserPanel\UserPanelController@redirectToSettingsPage');

    Route::post('settings/password/change', 'UserPanel\UserPanelController@passwordChangeForm')->name('settings-password-change');

    Route::get('settings/jabber-id/change', 'UserPanel\UserPanelController@redirectToSettingsPage');

    Route::post('settings/jabber-id/change', 'UserPanel\UserPanelController@jabberIDChangeForm')->name('settings-jabber-id-change');

    Route::get('settings/mail-address/change', 'UserPanel\UserPanelController@redirectToSettingsPage');

    Route::post('settings/mail-address/change', 'UserPanel\UserPanelController@mailAddressChangeForm')->name('settings-mail-address-change');

     //User Dashboard Routes
     Route::get('user/dashboard', 'UserPanel\UserPanelController@showUserDashboard')->name('users-backend-dashboard');
     Route::get('user/myorders', 'UserPanel\UserPanelController@ShowMyOrders')->name('my-orders');
     Route::get('user/myorders/page/{page?}', 'UserPanel\UserPanelController@ShowMyOrders')->name('myorders-orders-with-pageNumber');
     Route::get('user/myorders/id/{id}', 'UserPanel\UserPanelController@showOrderDetail')->name('myorder-detail');
    Route::get('deposit', 'UserPanel\UserPanelController@showDepositPage')->name('deposit');
    Route::get('btc-einzahlung', 'UserPanel\UserPanelController@showDepositBtcPage')->name('deposit-btc');
    Route::get('gutschein-einloesen', 'UserPanel\UserPanelController@btcRedeemPage')->name('deposit-btc-redeem');
    Route::post('gutschein-einloesen', 'UserPanel\UserPanelController@btcRedeemCoupon')->name('deposit-btc-redeem-coupon');
    Route::post('btc-einzahlung/coupon/redeem', 'UserPanel\UserPanelController@btcRedeemCoupon')->name('deposit-btc-edeem-coupon');
    Route::post('deposit-btc/{id}', 'UserPanel\UserPanelController@depositBtcPaidCheck')->name('deposit-btc-post');

	//Route::get('deposit-btc', 'UserPanel\UserPanelController@depositBtcPaidCheck')->name('deposit-btc-post');
    Route::get('meine-tids', 'UserPanel\UserPanelController@showOrdersPage')->name('orders');
    Route::get('meine-tids/record', 'UserPanel\UserPanelController@showOrdersPage')->name('orders-with-pageNumber');

    Route::get('meine-tickets', 'UserPanel\TicketController@showTicketsPage')->name('tickets');
    Route::get('meine-tickets/{page?}', 'UserPanel\TicketController@showTicketsPage')->name('tickets-with-pageNumber');
    Route::get('ticket/delete/{id}', 'UserPanel\TicketController@deleteTicket')->name('ticket-delete');
    Route::get('ticket-erstellen', 'UserPanel\TicketController@showTicketCreatePage')->name('ticket-create');
    Route::post('ticket/create', 'UserPanel\TicketController@showTicketCreatePage')->name('ticket-create-form');
    Route::post('ticket/reply/{id}', 'UserPanel\TicketController@replyTicket')->name('ticket-reply');
    Route::get('ticket/{id}', 'UserPanel\TicketController@showTicketPage')->name('ticket-id');

    Route::get('meine-einzahlungen', 'UserPanel\UserPanelController@showTransactionsPage')->name('transactions');
    Route::get('meine-einzahlungen/{page?}', 'UserPanel\UserPanelController@showTransactionsPage')->name('transactions-with-pageNumber');

    # Cronjobs
    Route::get('cronjob/bitcoin', 'Cronjobs\BitcoinCronjobController@checkTransaction')->name('cronjob-bitcoin');

    # FAQ
    Route::get('faq', 'FAQ\FAQController@showFAQPage')->name('faq');

    # Language
    Route::get('deutsch', 'App\LanguageController@setLanguageDutch')->name('language.de');
    Route::get('english', 'App\LanguageController@setLanguageEnglish')->name('language.en');

    # Shop


     Route::get('tid-sortiment', 'Shop\ShopController@showShopPage')->name('shop');
    Route::get('showdescription/{id}', 'Shop\ShopController@showdescription');

    Route::get('produkt-kaufen/{id?}/{amount?}', 'Shop\ShopController@buyProductForm')->name('buy-product');
    Route::get('receipt/{tidId}', 'Shop\ReceiptController@generateReceipt')->name('receipt.jpg');
    Route::post('produkt-kaufen', 'Shop\ShopController@buyProductForm')->name('buy-product-form');
    Route::post('produkt-kaufen/confirm', 'Shop\ShopController@buyProductConfirmForm')->name('buy-product-form-confirm');

    Route::post('produkt-kaufentest/confirm', 'Shop\ShopController@recipthtml')->name('recipthtml');



    Route::get('product/{id}', 'Shop\ShopController@showProductPage')->name('product-page');

    Route::get('creditcards', 'Shop\CreditCardsController@showCreditCardsPage')->name('creditcards');
    Route::get('creditcards/page/{page?}', 'Shop\CreditCardsController@showCreditCardsPage')->name('creditcards-with-pageNumber');

    /**
     * Backend
     */

    # Login & Logout
    Route::get('admin/login', 'Backend\LoginController@showLoginPage')->name('backend-login');
    Route::post('admin/login', 'Backend\LoginController@login')->name('backend-login-form');

    Route::post('admin/logout', 'Backend\LogoutController@logout');
    Route::get('admin/logout', 'Backend\LogoutController@logout')->name('backend-logout');

    # Dashboard
    Route::get('admin', 'Backend\DashboardController@showDashboard')->name('backend-dashboard');
    Route::get('admin/dashboard', 'Backend\DashboardController@showDashboard')->name('backend-dashboard');

    # Upload
    Route::group( [
        'as'     => 'admin.',
        'prefix' => 'admin/'
    ], function () {

        Route::resource( 'uploads', 'Backend\UploadController' )->only( [ 'index', 'create', 'store' ] );
        Route::get( 'uploads/destroy/{id}', 'Backend\UploadController@destroy' )->name( 'uploads.destroy' );
        Route::get( 'uploads/download/{id}', 'Backend\UploadController@download' )->name( 'uploads.download' );

        // Route::get('admin/upload', 'Backend\UploadController@showUpload')->name('backend-upload');

    } );


    # System Settings
    Route::get('admin/system/settings', 'Backend\System\SettingsController@showSettings')->name('backend-system-settings');
    Route::post('admin/system/settings', 'Backend\System\SettingsController@showSettings')->name('backend-system-settings-form');

    # Design
    Route::get('admin/design', 'Backend\DesignController@page')->name('backend-design');
    Route::post('admin/design', 'Backend\DesignController@page')->name('backend-design-form');

    # Media
    Route::post('admin/media/upload', 'Backend\MediaController@upload')->name('backend-media-upload');
    Route::get('admin/media', 'Backend\MediaController@page')->name('backend-media');
    Route::get('admin/media/page/{page?}', 'Backend\MediaController@page')->name('backend-media-with-pageNumber');
    Route::get('admin/media/delete/{id}', 'Backend\MediaController@delete')->name('backend-media-delete');

    # System Payments
    Route::get('admin/system/payments', 'Backend\System\PaymentsController@showPayments')->name('backend-system-payments');
    Route::post('admin/system/payments', 'Backend\System\PaymentsController@showPayments')->name('backend-system-payments-form');

    # Bitcoin Wallet
    Route::get('admin/bitcoin', 'Backend\Bitcoin\DashboardController@showDashboardPage')->name('backend-bitcoin-dashboard');
    Route::get('admin/bitcoin/page/{page?}', 'Backend\Bitcoin\DashboardController@showDashboardPage')->name('backend-bitcoin-dashboard-with-pageNumber');
    Route::post('admin/bitcoin/sendbtc', 'Backend\Bitcoin\DashboardController@sendBtcForm')->name('backend-bitcoin-sendbtc-form');
    Route::post('admin/bitcoin/primarywallet', 'Backend\Bitcoin\DashboardController@setPrimaryWalletForm')->name('backend-bitcoin-primarywallet-form');

    # Jabber
    Route::get('admin/jabber', 'Backend\JabberController@showJabberPage')->name('backend-jabber');
    Route::post('admin/jabber/newsletter', 'Backend\JabberController@sendNewsletter')->name('backend-jabber-newsletter-form');
    Route::post('admin/jabber/login', 'Backend\JabberController@loginSave')->name('backend-jabber-login-form');

    # Orders
    Route::post('admin/orders/add-note/{id}', 'Backend\OrdersController@addNote')->name('backend-orders-add-note-form');
    Route::post('admin/orders/add-label-code/{id}', 'Backend\OrdersController@addLabelCode')->name('backend-orders-add-label-code');
    Route::post('admin/orders/upload-pdf/{id}', 'Backend\OrdersController@uploadPdf')->name('backend-orders-upload-pdf');
    Route::get('admin/orders/cancel/{id}', 'Backend\OrdersController@cancelOrder')->name('backend-order-cancel');
    Route::get('admin/orders/complete/{id}', 'Backend\OrdersController@completeOrder')->name('backend-order-complete');
    Route::get('admin/orders/edit/random/{orderId}', 'Backend\OrdersController@randomRecipientEdit')->name('backend-order-edit-random');
    Route::post('admin/orders/edit/random/{orderId}', 'Backend\OrdersController@randomRecipientUpdate')->name('backend-order-update-random');
    Route::post('admin/orders/random/address/{orderId}', 'Backend\OrdersController@randomRecipientAddress')->name('backend-order-random-address');
    Route::get('admin/orders/edit/{orderId}', 'Backend\OrdersController@recipientEdit')->name('backend-order-edit');
    Route::post('admin/orders/update/{orderId}', 'Backend\OrdersController@recipientUpdate')->name('backend-order-update');
    Route::get('admin/orders/delete/{id}', 'Backend\OrdersController@deleteOrder')->name('backend-order-delete');
    Route::get('admin/orders/id/{id}', 'Backend\OrdersController@showOrder')->name('backend-order-id');
    Route::get('admin/orders', 'Backend\OrdersController@showOrdersPage')->name('backend-orders');
    Route::get('admin/orders/page/{page?}', 'Backend\OrdersController@showOrdersPage')->name('backend-orders-with-pageNumber');
    Route::get('admin/orders/notchanged/{page?}', 'Backend\OrdersController@showOrdersPage')->name('address-not-changed');
    Route::get('admin/orders/generate/{orderId}', 'Backend\OrdersController@regenerateOrderPdf');
    Route::post('admin/orders/upload/{orderId}', 'Backend\OrdersController@uploadTidFileManual')->name('order-manual-add-tid');



    Route::post( 'admin/orders/page/clearfilter', 'Backend\OrdersController@clearFilter' )->name( 'backend-orders-clearFilter' );
    Route::get('admin/order/download_tid/{filename}/random', 'Backend\OrdersController@downloadTidFileRandom')->name('backend-orders-downloadTidFile-random');
    Route::get( 'admin/order/download_tid/{filename}', 'Backend\OrdersController@downloadTidFile' )->name( 'backend-orders-downloadTidFile' );
    Route::get( 'admin/order/download_tid/{filename}/manual', 'Backend\OrdersController@downloadTidFileManual' )->name( 'backend-orders-downloadTidFile-manual' );
    Route::post( 'admin/order/status/{id}', 'Backend\OrdersController@setStatus' )->name( 'backend-order-setStatus' );
    Route::post( 'admin/order/setBulkStatus', 'Backend\OrdersController@setBulkStatus' )->name( 'backend-order-setBulkStatus' );
    Route::post('admin/order/boxingstatus/{id}', 'Backend\OrdersController@setBoxingStatus')->name('backend-order-setstatus-for-boxing');
    Route::post('admin/order/replacestatus/{id}', 'Backend\OrdersController@setReplaceStatus')->name('backend-order-set-replace-status-for-boxing');


    # Product Categories
    Route::get('admin/management/products/category/delete/{id}', 'Backend\Management\ProductsCategoriesController@deleteProductCategory')->name('backend-management-product-category-delete');
    Route::get('admin/management/products/categories', 'Backend\Management\ProductsCategoriesController@showProductsCategoriesPage')->name('backend-management-products-categories');
    Route::get('admin/management/products/categories/page/{page?}', 'Backend\Management\ProductsCategoriesController@showProductsCategoriesPage')->name('backend-management-products-categories-with-pageNumber');
    Route::get('admin/management/products/categories/add', 'Backend\Management\ProductsCategoriesController@showProductCategoryAddPage')->name('backend-management-product-category-add');
    Route::post('admin/management/products/categories/add', 'Backend\Management\ProductsCategoriesController@addProductCategoryForm')->name('backend-management-product-category-add-form');
    Route::get('admin/management/products/categories/edit/{id}', 'Backend\Management\ProductsCategoriesController@showProductCategoryEditPage')->name('backend-management-product-category-edit');
    Route::post('admin/management/products/categories/edit', 'Backend\Management\ProductsCategoriesController@editProductCategoryForm')->name('backend-management-product-category-edit-form');

    # Products
    Route::get('admin/management/product/database/{id}', 'Backend\Management\ProductsController@showProductDatabasePage')->name('backend-management-product-database');
    Route::post('admin/management/product/database/import/txt', 'Backend\Management\ProductsController@databaseImportTXT')->name('backend-management-product-database-import-txt');
    Route::post('admin/management/product/database/import/one', 'Backend\Management\ProductsController@databaseImportONE')->name('backend-management-product-database-import-one');
    Route::post('admin/management/product/database/import/items', 'Backend\Management\ProductsController@databaseImportItems')->name('backend-management-product-database-import-items');
    Route::get('admin/management/product/delete/{id}', 'Backend\Management\ProductsController@deleteProduct')->name('backend-management-product-delete');
    Route::get('admin/management/products/add', 'Backend\Management\ProductsController@showProductAddPage')->name('backend-management-product-add');
    Route::post('admin/management/products/add', 'Backend\Management\ProductsController@addProductForm')->name('backend-management-product-add-form');
    Route::get('admin/management/products/edit/{id}', 'Backend\Management\ProductsController@showProductEditPage')->name('backend-management-product-edit');
    Route::post('admin/management/products/edit', 'Backend\Management\ProductsController@editProductForm')->name('backend-management-product-edit-form');
    Route::get('admin/management/products', 'Backend\Management\ProductsController@showProductsPage')->name('backend-management-products');

    # Random addresses
    Route::get('admin/management/address/database', 'Backend\OrdersController@showAddressDatabasePage')->name('backend-management-address-database');
    Route::post('admin/management/address/database/import', 'Backend\OrdersController@addressDatabaseImport')->name('backend-management-address-database-import');

    /*
    Route::get('admin/management/creditcards', 'Backend\Management\CreditCardsController@showCreditCardsPage')->name('backend-management-creditcards');
    Route::get('admin/management/creditcards/page/{page?}', 'Backend\Management\CreditCardsController@showCreditCardsPage')->name('backend-management-creditcards-with-pageNumber');
    */

    # Ticket Categories
    Route::get('admin/management/tickets/category/delete/{id}', 'Backend\Management\TicketsCategoriesController@deleteTicketCategory')->name('backend-management-ticket-category-delete');
    Route::get('admin/management/tickets/categories', 'Backend\Management\TicketsCategoriesController@showTicketsCategoriesPage')->name('backend-management-tickets-categories');
    Route::get('admin/management/tickets/categories/page/{page?}', 'Backend\Management\TicketsCategoriesController@showTicketsCategoriesPage')->name('backend-management-tickets-categories-with-pageNumber');
    Route::get('admin/management/tickets/categories/add', 'Backend\Management\TicketsCategoriesController@showTicketCategoryAddPage')->name('backend-management-ticket-category-add');
    Route::post('admin/management/tickets/categories/add', 'Backend\Management\TicketsCategoriesController@addTicketCategoryForm')->name('backend-management-ticket-category-add-form');
    Route::get('admin/management/tickets/categories/edit/{id}', 'Backend\Management\TicketsCategoriesController@showTicketCategoryEditPage')->name('backend-management-ticket-category-edit');
    Route::post('admin/management/tickets/categories/edit', 'Backend\Management\TicketsCategoriesController@editTicketCategoryForm')->name('backend-management-ticket-category-edit-form');

    # Tickets
    Route::get('admin/management/ticket/delete/{id}', 'Backend\Management\TicketsController@deleteTicket')->name('backend-management-ticket-delete');
    Route::get('admin/management/ticket/edit/{id}', 'Backend\Management\TicketsController@showTicketEditPage')->name('backend-management-ticket-edit');
    Route::get('admin/management/ticket/close/{id}', 'Backend\Management\TicketsController@closeTicket')->name('backend-management-ticket-close');
    Route::get('admin/management/ticket/open/{id}', 'Backend\Management\TicketsController@openTicket')->name('backend-management-ticket-open');
    Route::post('admin/management/ticket/reply', 'Backend\Management\TicketsController@replyTicketForm')->name('backend-management-ticket-reply-form');
    Route::post('admin/management/ticket/move-category', 'Backend\Management\TicketsController@moveTicketForm')->name('backend-management-ticket-move-form');
    Route::get('admin/management/tickets', 'Backend\Management\TicketsController@showTicketsPage')->name('backend-management-tickets');
    Route::post('admin/management/ticket/change-ballance/{id}', 'Backend\Management\TicketsController@changeBallance')->name('backend-management-ticket-ballance');
    //Route::get('admin/management/tickets/page/{page?}', 'Backend\Management\TicketsController@showTicketsPage')->name('backend-management-tickets-with-pageNumber');

    # FAQ Categories
    Route::get('admin/management/faqs/category/delete/{id}', 'Backend\Management\FAQsCategoriesController@deleteFAQCategory')->name('backend-management-faq-category-delete');
    Route::get('admin/management/faqs/categories', 'Backend\Management\FAQsCategoriesController@showFAQsCategoriesPage')->name('backend-management-faqs-categories');
    Route::get('admin/management/faqs/categories/page/{page?}', 'Backend\Management\FAQsCategoriesController@showFAQsCategoriesPage')->name('backend-management-faqs-categories-with-pageNumber');
    Route::get('admin/management/faqs/categories/add', 'Backend\Management\FAQsCategoriesController@showFAQCategoryAddPage')->name('backend-management-faq-category-add');
    Route::post('admin/management/faqs/categories/add', 'Backend\Management\FAQsCategoriesController@addFAQCategoryForm')->name('backend-management-faq-category-add-form');
    Route::get('admin/management/faqs/categories/edit/{id}', 'Backend\Management\FAQsCategoriesController@showFAQCategoryEditPage')->name('backend-management-faq-category-edit');
    Route::post('admin/management/faqs/categories/edit', 'Backend\Management\FAQsCategoriesController@editFAQCategoryForm')->name('backend-management-faq-category-edit-form');

    # FAQ
    Route::get('admin/management/faq/delete/{id}', 'Backend\Management\FAQsController@deleteFAQ')->name('backend-management-faq-delete');
    Route::get('admin/management/faq/edit/{id}', 'Backend\Management\FAQsController@showFAQEditPage')->name('backend-management-faq-edit');
    Route::post('admin/management/faq/edit', 'Backend\Management\FAQsController@editFAQForm')->name('backend-management-faq-edit-form');
    Route::get('admin/management/faq/add', 'Backend\Management\FAQsController@showFAQAddPage')->name('backend-management-faq-add');
    Route::post('admin/management/faq/add', 'Backend\Management\FAQsController@addFAQForm')->name('backend-management-faq-add-form');
    Route::get('admin/management/faqs', 'Backend\Management\FAQsController@showFAQsPage')->name('backend-management-faqs');
    Route::get('admin/management/faqs/page/{page?}', 'Backend\Management\FAQsController@showFAQsPage')->name('backend-management-faqs-with-pageNumber');

    # Articles
    Route::get('admin/management/article/delete/{id}', 'Backend\Management\ArticlesController@deleteArticle')->name('backend-management-article-delete');
    Route::get('admin/management/article/edit/{id}', 'Backend\Management\ArticlesController@showArticleEditPage')->name('backend-management-article-edit');
    Route::post('admin/management/article/edit', 'Backend\Management\ArticlesController@editArticleForm')->name('backend-management-article-edit-form');
    Route::get('admin/management/article/add', 'Backend\Management\ArticlesController@showArticleAddPage')->name('backend-management-article-add');
    Route::post('admin/management/article/add', 'Backend\Management\ArticlesController@addArticleForm')->name('backend-management-article-add-form');
    Route::get('admin/management/articles', 'Backend\Management\ArticlesController@showArticlesPage')->name('backend-management-articles');
    Route::get('admin/management/articles/page/{page?}', 'Backend\Management\ArticlesController@showArticlesPage')->name('backend-management-articles-with-pageNumber');

    # Users
    Route::get('admin/management/user/login/{id}', 'Backend\Management\UsersController@loginAsUser')->name('backend-management-user-login');
    Route::get('admin/management/user/delete/{id}', 'Backend\Management\UsersController@deleteUser')->name('backend-management-user-delete');
    Route::get('admin/management/user/edit/{id}', 'Backend\Management\UsersController@showUserEditPage')->name('backend-management-user-edit');
    Route::post('admin/management/user/update/permissions', 'Backend\Management\UsersController@updateUserPermissionsForm')->name('backend-management-user-update-permissions-form');
    Route::post('admin/management/user/edit', 'Backend\Management\UsersController@editUserForm')->name('backend-management-user-edit-form');
    Route::get('admin/management/users', 'Backend\Management\UsersController@showUsersPage')->name('backend-management-users');
    Route::post('admin/management/user_password','Backend\Management\UsersController@user_password')->name('backend-change-user-password');
    // Route::get('admin/management/users/page/{page?}', 'Backend\Management\UsersController@showUsersPage')->name('backend-management-users-with-pageNumber');

    # Coupons
    Route::get('admin/management/coupon/delete/{id}', 'Backend\Management\CouponsController@deleteCoupon')->name('backend-management-coupon-delete');
    Route::get('admin/management/coupon/edit/{id}', 'Backend\Management\CouponsController@showCouponEditPage')->name('backend-management-coupon-edit');
    Route::post('admin/management/coupon/edit', 'Backend\Management\CouponsController@editCouponForm')->name('backend-management-coupon-edit-form');
    Route::get('admin/management/coupon/add', 'Backend\Management\CouponsController@showCouponAddPage')->name('backend-management-coupon-add');
    Route::post('admin/management/coupon/add', 'Backend\Management\CouponsController@addCouponForm')->name('backend-management-coupon-add-form');
    Route::get('admin/management/coupons', 'Backend\Management\CouponsController@showCouponsPage')->name('backend-management-coupons');
    Route::get('admin/management/coupons/page/{page?}', 'Backend\Management\CouponsController@showCouponsPage')->name('backend-management-coupons-with-pageNumber');

    # DeliveryMethods
    Route::get('admin/management/delivery-method/delete/{id}', 'Backend\Management\DeliveryMethodsController@deleteDeliveryMethod')->name('backend-management-delivery-method-delete');
    Route::get('admin/management/delivery-method/edit/{id}', 'Backend\Management\DeliveryMethodsController@showDeliveryMethodEditPage')->name('backend-management-delivery-method-edit');
    Route::post('admin/management/delivery-method/edit', 'Backend\Management\DeliveryMethodsController@editDeliveryMethodForm')->name('backend-management-delivery-method-edit-form');
    Route::get('admin/management/delivery-method/add', 'Backend\Management\DeliveryMethodsController@showDeliveryMethodAddPage')->name('backend-management-delivery-method-add');
    Route::post('admin/management/delivery-method/add', 'Backend\Management\DeliveryMethodsController@addDeliveryMethodForm')->name('backend-management-delivery-method-add-form');
    Route::get('admin/management/delivery-methods', 'Backend\Management\DeliveryMethodsController@showDeliveryMethodsPage')->name('backend-management-delivery-methods');
    Route::get('admin/management/delivery-methods/page/{page?}', 'Backend\Management\DeliveryMethodsController@showDeliveryMethodsPage')->name('backend-management-delivery-methods-with-pageNumber');

    # Notifications
    Route::get('admin/notifications/clear', 'Backend\NotificationsController@deleteAllNotifications')->name('backend-notifications-clear');
    Route::get('admin/notification/delete/{id}', 'Backend\NotificationsController@deleteNotification')->name('backend-notification-delete');
    Route::get('admin/notifications', 'Backend\NotificationsController@showNotificationsPage')->name('backend-notifications');
    Route::get('admin/notifications/page/{page?}', 'Backend\NotificationsController@showNotificationsPage')->name('backend-notifications-with-pageNumber');

    # JSON
    Route::post('admin/api/recent-orders', 'Backend\API\JSONController@getRecentOrders')->name('backend-api-recent-orders');
    Route::get('admin/api/recent-orders', 'Backend\API\JSONController@getRecentOrders')->name('backend-api-recent-orders');

    Route::post('admin/api/recent-tickets', 'Backend\API\JSONController@getRecentTickets')->name('backend-api-recent-tickets');
    Route::get('admin/api/recent-tickets', 'Backend\API\JSONController@getRecentTickets')->name('backend-api-recent-tickets');

    Route::get('admin/api/notifications', 'Backend\API\JSONController@getNotifications')->name('backend-api-notifications');



    Route::get('/{slug}', 'Shop\ShopController@showProductCategoryPage')->name('product-category');
    //employee profit controller
    Route::get('admin/commission', 'Backend\Profits\CommissionController@show_commision')->name('employee-commission')->middleware(['backend', 'permission:see_profits']);
    Route::get('admin/cashondelivery', 'Backend\Profits\CommissionController@cashonDeliveryChart')->name('cashondelivery')->middleware(['backend', 'permission:see_profits']);


    Route::get('partner/products', 'UserPanel\UserPanelController@showPartnerProducts')->name('partner-management-products');
    Route::get('partner/products/edit/{id}', 'UserPanel\UserPanelController@showProductEditPage')->name('partner-product-edit');
    Route::post('partner/products/edit', 'UserPanel\UserPanelController@editProductForm')->name('partner-product-edit-form');
    Route::get('partner/product/database/{id}', 'UserPanel\UserPanelController@showProductDatabasePage')->name('partner-product-database');
    Route::post('partner/product/database/import/txt', 'UserPanel\UserPanelController@databaseImportTXT')->name('partner-product-database-import-txt');
    Route::post('partner/product/database/import/one', 'UserPanel\UserPanelController@databaseImportONE')->name('partner-product-database-import-one');
    Route::post('partner/product/database/import/items', 'UserPanel\UserPanelController@databaseImportItems')->name('partner-product-database-import-items');
    Route::get('partner/orders', 'UserPanel\UserPanelController@ShowPartnerOrders')->name('partner-orders');
    Route::get('partner/profits', 'UserPanel\UserPanelController@partnerProfit')->name('partner-profits');
    Route::post('partner/profits', 'UserPanel\UserPanelController@partnerProfit')->name('profit-filter');
    Route::post( 'partner/orders/status/{id}', 'UserPanel\UserPanelController@PartnerOrdersetStatus' )->name( 'partner-order-setStatus' );
    Route::get( 'partner/tickets', 'UserPanel\UserPanelController@PartnerTickets' )->name( 'partner-tickets' );
    Route::get('partner/ticket/delete/{id}', 'UserPanel\UserPanelController@partnerdeleteTicket')->name('partner-ticket-delete');
    Route::get('partner/ticket/edit/{id}', 'UserPanel\UserPanelController@partnerTicketEditPage')->name('partner-ticket-edit');
    Route::get('partner/ticket/close/{id}', 'UserPanel\UserPanelController@PartnercloseTicket')->name('partner-ticket-close');
    Route::get('partner/ticket/open/{id}', 'UserPanel\UserPanelController@PartneropenTicket')->name('partner-ticket-open');
    Route::post('partner/ticket/reply', 'UserPanel\UserPanelController@partnerreplyTicketForm')->name('partner-ticket-reply-form');
    Route::post('create-btc-invoice', 'UserPanel\UserPanelController@createBTCInvoice')->name('create-btc-invoice');
    Route::post('deposit-btc-check', 'UserPanel\BtcPayServerController@btcpaymentcheck')->name('deposit-btc-check');


Route::get('/clear-cache', function() {
    //Artisan::call('route:clear');
    //Artisan::call('cache:clear');
    Artisan::call('view:clear');
    //Artisan::call('config:clear');
    return "Artisan cleared";
});


Route::get('/cache-clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    //Artisan::call('config:clear');
    return "Artisan Cache cleared";
});

Route::get('/route-clear', function() {

    Artisan::call('route:cache');

    //Artisan::call('config:clear');
    return "Artisan Route cleared";
});

Route::get('/view-clear', function() {

    Artisan::call('view:clear');

    //Artisan::call('config:clear');
    return "Artisan Route cleared";
});

Route::get('/route-list', function() {

    Artisan::call('route:list');

    //Artisan::call('config:clear');
    die;
    return "Artisan Route List";

});



Route::get('/clear-cache-uppp', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});

//php artisan route:cache

//php artisan view:clear


Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    return "Config Cache";
});

Route::get('/opt', function() {
    Artisan::call('optimize');
    return "optimized";
});

Route::get('/opt-clae', function() {
    Artisan::call('optimize:clear');
    return "optimized clear";
});


