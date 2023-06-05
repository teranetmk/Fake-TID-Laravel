<?php

return [

    'validator' => [
        'first_name'        => 'Vorname Pflichtfeld',
        'last_name'         => 'Nachname Pflichtfeld',
        'street'            => 'Straße Pflichtfeld',
        'zip'               => 'PLZ Pflichtfeld',
        'city'              => 'Ort Pflichtfeld',
        'country'           => 'Land Pflichtfeld',
        'sender_first_name' => 'Vorname Pflichtfeld',
        'sender_last_name'  => 'Nachname Pflichtfeld',
        'sender_street'     => 'Straße Pflichtfeld',
        'sender_zip'        => 'PLZ Pflichtfeld',
        'sender_city'       => 'Ort Pflichtfeld',
        'sender_country'    => 'Land Pflichtfeld',
        'shipping_time'     => 'Versandzeit Pflichtfeld',
        'send_at'           => 'Versanddatum Pflichtfeld',
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
    	'show_shipping_addresses_button'    => 'Hier klicken um die Adressen automatisch zu importieren',
        'import_shipping_addresses_button'  => 'Adressen importieren',
        'receipt'           => 'Einlieferungsbeleg',
        'receipt_label'     => 'Einlieferungsbeleg generieren (+0,00 EUR)',
        'tracking_number'   => 'SENDUNGSNUMMER (RET CODE):',
    ],

    'orders' => [
        'status' => [
            'completed' => 'Eingeliefert',// 'Versandt / Fertiggestellt',
            'cancelled' => 'Stoniert',
            'pending'   => 'In Wartestellung',
            'package_was_accepted'   => 'Paket wurde angenommen',
            'package_was_refused'    => 'Paket wurde verweigert',
        ],
    ],

    'sale' => ':percent%',

    'tags' => [
        'sale' => 'im Angebot'
    ],

    'delivery_method' => [
        'title' => 'Versandart',
        'row'   => ':name (+:price)'
    ],

    'delivery_price'              => 'Versandkosten:',
    'delivery_method_needed'      => 'Bitte Versandart auswählen.',
    'orders_notes'                => 'Anmerkungen:',
    'order_note'                  => 'Drop',
    'orders_status'               => 'Status:',
    'orders_order_note'           => 'Drop:',
    'order_note_placeholder'      => 'Lieferanschrift (HD / PS / BKD...)',
    'order_note_long'             => 'Drop zu lang (max. :charallowed Zeichen)',
    'order_note_needed'           => 'Bitte Drop angeben.',
    'product_weight'              => 'Gewicht:',
    'weight_placeholder'          => 'Gewicht',
    'amount'                      => ':amount Stück',
    'product_amount'              => 'Anzahl:',
    'no_products_exists'          => 'Keine Produkte vorhanden.',
    'creditcards'                 => 'Kreditkarten',
    'buy_button'                  => 'Kaufen',
    'details_button'              => 'Details',
    'product_not_found'           => 'Produkt nicht gefunden.',
    'category'                    => 'Kategorie:',
    'uncategorized'               => 'Unkategorisiert',
    'amount_with_char'            => 'Verfügbar: :amount_with_char',
    'bought_weight'               => 'Gewicht:',
    'unlimited'                   => 'Unbegrenzt verfügbar',
    'sold_out'                    => 'Ausverkauft',
    'all_categories'              => 'Alle Kategorien',
    'no_products_category_exists' => 'Keine Produkte in dieser Kategorie vorhanden.',
    'not_enought_money'           => 'Du hast nicht genügend Guthaben! Bitte zahle erst Geld auf dein Konto ein.',
    'product_not_available'       => 'Das Produkt ist nicht mehr verfügbar.',
    'you_bought'                  => 'Du hast <b>:name</b> für <b>:price</b> gekauft!',
    'you_bought_with_amount'      => 'Du hast :amount x <b>:name</b> für <b>:price</b> (Gesamt: :totalprice) gekauft!',
    'you_bought_with_amount2'     => 'Du hast :amount_with_char <b>:name</b> für <b>:price</b> (Gesamt: :totalprice) gekauft!',
    'buy_error'                   => 'Beim Kauf ist ein unbekannter Fehler aufgetreten.',
    'stock_status'                => 'Lagerbestand:',
    'price'                       => 'Preis:',
    'totalprice'                  => 'Gesamtpreis:',
    'must_logged_in'              => 'Du musst dich einloggen oder registrieren, um das Produkt kaufen zu können.',
    'to_shop'                     => 'Zum Shop',
    'confirm'                     => 'Bestätigen',
    'total_price'                 => 'Gesamtpreis:',
    'product_confirm_buy'         => 'Kauf bestätigen',
    'start_video_alert'           => 'Starte jetzt ein Video, bevor du den Kauf-Button betätigst!',
    'replace_rules_alert'         => 'Beachte die Replace-Regeln!',
    'cancel_order'                => 'Kauf abbrechen',
    'product_details'             => 'Produkt-Details',
    'delivery_tomorrow'           => 'Deine Bestellung wurde nach 17:30 Uhr aufgegeben. Die Einlieferung erfolgt erst am nächsten Werktag.'
];
