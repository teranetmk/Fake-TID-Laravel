<?php

return [

    'title'      => 'Orders',
    'delete'     => 'Delete',
    'view'       => 'Show',
    'cancel'     => 'Cancel',
    'complete'   => 'Sent',
    'edit'       => 'Edit',
    'notes'      => 'Anmerkungen',
    'note_input' => 'Anmerkung',
    'add_note'   => 'Hinzufügen',
    'download'   => 'Download',

    'fiters' => [
        'fiter'        => 'Fiter',
        'clear_filter' => 'Clear filter',
        'today_oders'  => 'Today oders',
        'address_not_changed'  => 'Address not changed'
    ],

    'status' => [
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'pending'   => 'Pending',
        'package_was_accepted'   => 'Paket wurde angenommen',
        'package_was_refused'    => 'Paket wurde verweigert',
        'replace_has_been_paid'  => 'Replace wurde ausgezahlt',
    ],

    'show' => [
        'title'             => 'Order #:id',
        'block_title'       => 'Order data',
        'track'             => 'Track TID', // Track TID / TID verfolgen
        'download'          => 'PDF Download', // Download / PDF Herunterladen
        'product_name'      => 'Produkt Name:', // Produktname: Product name 5
        'save'              => 'Save', // Submit / Speichern
        'description'       => 'Description:', // Description / Beschreibung
        'short_description' => 'Short description:', // Short description / Kurzbeschreibung
        'created'           => 'Created at:', // Created / Erstellt am
        'deliver'           => 'Deliver at:', // Deliver / Lieferung am

        'form' => [
            'sender'     => 'Absender',
            'receiver'   => 'Empfänger',
            'first_name' => 'Vorname:',
            'last_name'  => 'Nachname:',
            'street'     => 'Straße:',
            'zip_code'   => 'PLZ:',
            'place'      => 'Ort:',
            'country'    => 'Land:',
            'update'     => 'Update',
            'address'    => 'Google Adresse:',
        ],

        'additional_fields' => [
            'title' => 'Additional',
            'form' => [
                'label_code'    => 'Label Code',
                'upload'    => 'Upload',
            ]
        ]
    ],

    'table' => [
        'id'              => 'ID',
        'user'            => 'User',
        'status'          => 'Status',
        'delivery_method' => 'Versandart',
        'date'            => 'Date',
        'date_of_order'   => 'Date of order',
        'date_of_delivery'=> 'Date of delivery',
        'actions'         => 'Actions',
        'product'         => 'Product',
        'notes'           => 'Drop',
        'receipt'         => 'Receipt',
        'download'        => 'Download',
        'invalid_zip'     => 'Zip is Invalid',
        'tracking_number' => 'Tracking number (RET CODE)',
        'tid' => 'TID',
    ]

];
