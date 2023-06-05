<?php

return [

    'validator' => [
        'first_name'        => 'Vorname must be required',
        'last_name'         => 'Nachname must be required',
        'street'            => 'Straße must be required',
        'zip'               => 'PLZ must be required',
        'city'              => 'Ort must be required',
        'country'           => 'Land must be required',
        'sender_first_name' => 'Vorname must be required',
        'sender_last_name'  => 'Nachname must be required',
        'sender_street'     => 'Straße must be required',
        'sender_zip'        => 'PLZ must be required',
        'sender_city'       => 'Ort must be required',
        'sender_country'    => 'Land must be required',
        'shipping_time'     => 'Versandzeit must be required',
        'send_at'           => 'Versanddatum must be required',
    ],

    'form' => [
        'sender'     => 'Absender',
        'receiver'   => 'Empfänger',
        'first_name' => 'Vorname:',
        'last_name'  => 'Nachname:',
        'street'     => 'Straße:',
        'zip_code'   => 'PLZ:',
        'place'      => 'Ort:',
        'country'    => 'Land:',
    	'show_shipping_addresses_button'    => 'Click here to import the addresses automatically',
        'import_shipping_addresses_button'  => 'Import addresses',
        'receipt'           => 'Receipt',
        'receipt_label'     => 'Generate receipt (+0.00 EUR)',
        'tracking_number'   => 'Tracking number (RET CODE):',
    ],

    'orders' => [
        'status' => [
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'pending'   => 'Pending',
            'package_was_accepted'   => 'Paket wurde angenommen',
            'package_was_refused'    => 'Paket wurde verweigert',
        ],
    ],

    'sale' => ':percent%',

    'tags' => [
        'sale' => 'Sale'
    ],

    'delivery_method' => [
        'title' => 'Delivery method',
        'row'   => ':name (+:price)'
    ],

    'delivery_price' => 'Delivery price:',

    'delivery_method_needed' => 'Please choose a delivery method.',

    'orders_notes'                => 'Notes:',
    'order_note'                  => 'Drop',
    'orders_status'               => 'Status:',
    'orders_order_note'           => 'Drop:',
    'order_note_placeholder'      => 'Shipping address (HD / PS / BKD...)',
    'order_note_long'             => 'Order note to long (max. :charallowed chars)',
    'order_note_needed'           => 'Enter a drop.',
    'product_weight'              => 'Weight:',
    'weight_placeholder'          => 'Weight',
    'amount'                      => ':amount Qty.',
    'product_amount'              => 'Amount:',
    'no_products_exists'          => 'No products exists.',
    'creditcards'                 => 'Creditcards',
    'buy_button'                  => 'Buy',
    'details_button'              => 'Details',
    'product_not_found'           => 'Product not found.',
    'category'                    => 'Category:',
    'uncategorized'               => 'Uncategorized',
    'amount_with_char'            => 'Available: :amount_with_char',
    'bought_weight'               => 'Weight:',
    'unlimited'                   => 'Unlimited stock',
    'sold_out'                    => 'Sold out',
    'all_categories'              => 'All categories',
    'no_products_category_exists' => 'No products available in this category.',
    'not_enought_money'           => 'You do not have enough credits! Please deposit to your account first.',
    'product_not_available'       => 'The product is no longer available.',
    'you_bought'                  => 'Du hast <b>:name</b> für <b>:price</b> gekauft!',
    'you_bought_with_amount'      => 'Du hast :amount x <b>:name</b> für <b>:price</b> (Gesamt: :totalprice) gekauft!',
    'you_bought_with_amount2'     => 'Du hast :amount_with_char <b>:name</b> für <b>:price</b> (Gesamt: :totalprice) gekauft!',
    'buy_error'                   => 'An unknown error occurred during the purchase.',
    'stock_status'                => 'Stock status:',
    'price'                       => 'Price:',
    'totalprice'                  => 'Total price:',
    'must_logged_in'              => 'You need to log in or register to buy the product.',
    'to_shop'                     => 'Back to shop',
    'confirm'                     => 'Confirm',
    'total_price'                 => 'Total price:',
    'product_confirm_buy'         => 'Confirm',
    'start_video_alert'           => 'Start a video now, before you press the buy button!',
    'replace_rules_alert'         => 'Note the replace rules!',
    'cancel_order'                => 'Cancel order',
    'product_details'             => 'Product-Details',
    'delivery_tomorrow'           => 'Your order was placed after 5:30 p.m. The delivery takes place on the next working day.'

];
