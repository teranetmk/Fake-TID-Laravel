<?php

return [

    'title'      => 'Bestellungen',
    'delete'     => 'Löschen',
    'view'       => 'Ansehen',
    'cancel'     => 'Stonieren',
    'complete'   => 'Als Versendet markieren',
    'edit'       => 'Bearbeiten',
    'notes'      => 'Anmerkungen',
    'note_input' => 'Anmerkung',
    'add_note'   => 'Hinzufügen',
    'download'   => 'Herunterladen',

    'fiters' => [
        'fiter'        => 'Filtern',
        'clear_filter' => 'Zurücksetzen',
        'today_oders'  => 'heutige Bestellungen',
        'address_not_changed'  => 'Adresse nicht geändert'
    ],

    'status' => [
        'completed' => 'Eingeliefert', // 'Versandt / Fertiggestellt',
        'cancelled' => 'Stoniert',
        'pending'   => 'In Wartestellung',
        'package_was_accepted'   => 'Paket wurde angenommen',
        'package_was_refused'    => 'Paket wurde verweigert',
        'replace_has_been_paid'  => 'Replace wurde ausgezahlt',
    ],

    'show' => [
        'title'             => 'Bestellung #:id',
        'block_title'       => 'Bestelldaten',
        'track'             => 'TID verfolgen', // Track TID / TID verfolgen
        'download'          => 'PDF Herunterladen', // Download / PDF Herunterladen
        'product_name'      => 'Produktname:', // Produktname: Product name 5
        'save'              => 'Speichern', // Submit / Speichern
        'description'       => 'Beschreibung:', // Description / Beschreibung
        'short_description' => 'Kurzbeschreibung:', // Short description / Kurzbeschreibung
        'created'           => 'Erstellt am:', // Created / Erstellt am
        'deliver'           => 'Lieferung am:', // Deliver / Lieferung am

        'form' => [
            'sender'     => 'Absender:',
            'receiver'   => 'Empfänger:',
            'name'       => 'Name:',
            'first_name' => 'Vorname:',
            'last_name'  => 'Nachname:',
            'street'     => 'Straße:',
            'zip_code'   => 'PLZ:',
            'place'      => 'Ort:',
            'country'    => 'Land:',
            'update'     => 'aktualisieren',
            'address'    => 'Google Adresse:'
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
        'user'            => 'Benutzer',
        'status'          => 'Status',
        'delivery_method' => 'Versandart',
        'date'            => 'Datum',
        'date_of_order'   => 'Datum der Bestellung',
        'date_of_delivery'=> 'Datum der Lieferung',
        'actions'         => 'Aktionen',
        'product'         => 'Produkt',
        'notes'           => 'Drop',
        'receipt'         => 'Einlieferungsbeleg',
        'download'        => 'Download',
        'invalid_zip'     => 'Aktuell deaktiviert.',
        'tracking_number' => 'Sendungsnummer (RET CODE)',
        'tid' => 'TID',
    ],


];
