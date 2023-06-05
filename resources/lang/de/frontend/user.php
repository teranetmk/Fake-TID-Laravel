<?php

return [

    'admin_panel'           => 'Admin Panel',
    'to_much_tickets_error' => 'Du kannst maximal :amount offene Tickets haben.',

    'login' => [
        'title'            => 'Anmelden',
        'submit'           => 'Einloggen',
        'forgot_password'  => 'Passwort vergessen?',
        'remember'         => 'Eingeloggt bleiben',
        'password'         => 'Passwort',
        'confirm_password' => 'Passwort',
        'create_account'   => 'Account erstellen'
    ],

    'news'  => [
        'title'             => 'Aktuelle News'
    ],

    'register' => [
        'title'              => 'Account erstellen',
        'submit'             => 'Registrieren',
        'password'           => 'Passwort',
        'confirm_password'   => 'Passwort wiederholen',
        'cancel'             => 'Abbrechen',
        'newsletter_enabled' => 'Newsletter Abonnieren'
    ],

    'reset' => [
        'password' => [
            'title'            => 'Passwort zurücksetzen',
            'submit'           => 'Zurücksetzen',
            'password'         => 'Passwort',
            'confirm_password' => 'Passwort wiederholen'
        ]
    ],

    'verify' => [
        'title'    => 'E-Mail Adresse bestätigen',
        'alerts'   => [
            'sent' => 'Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse gesendet.'
        ],
        'messages' => [
            'before_proceeding'  => 'Bevor Sie fortfahren, überprüfen Sie bitte Ihre E-Mail auf einen Bestätigungslink.',
            'not_received_email' => 'Wenn Sie keine E-Mail erhalten haben, <a href=":url">klicken Sie hier</a>.'
        ]
    ],

    'panel' => [
        'welcome_message' => 'Willkommen, <b>:name</b>.',
        'member_since'    => 'Du bist Mitglied seit dem <b>:date</b>!'
    ],

    'orders_page' => [
        'no_orders_exists' => 'Du hast bisher nichts bestellt.'
    ],

    'coupon_redeem' => [
        'title'   => 'Gutschein einlösen',
        'code'    => 'Gutscheincode:',
       'label'   => 'Gutscheincode hier eingeben',
        'submit'  => 'Einlösen',
        'error1'  => 'Ungültiger Gutscheincode.',
        'error2'  => 'Gutschein kann nicht mehr eingelöst werden.',
        'error3'  => 'Du hast den Gutschein bereits eingelöst.',
        'success' => 'Du hast den Gutscheincode <b>:code</b> im Wert von <b>:amount</b> eingelöst.'
    ],

    'transactions_page' => [
        'no_transactions_exists' => 'Keine Transaktionen.',
        'confirmed'              => 'Bestätigt',
        'confirmations'          => ':confirms/:confirms_needed Bestätigungen',
        'waiting'                => 'Zahlung ausstehend',
        'actions'                => 'Aktionen',
        'txid'                   => 'TXID',
        'txid_label'             => 'Transaktions-ID',
        'status'                 => 'Status',
        'wallet'                 => 'Wallet',
        'amount'                 => 'Betrag',
        'date'                   => 'Datum',
        'id'                     => 'ID'
    ],

    'tickets' => [
        'list_tickets'        => 'Tickets',
        'no_tickets_exists'   => 'Du hast noch keine Tickets erstellt.',
        'status'              => 'Status',
        'category'            => 'Kategorie',
        'date'                => 'Datum',
        'actions'             => 'Aktionen',
        'subject'             => 'Betreff',
        'no_category'         => 'Keine Kategorie',
        'view'                => 'Ansehen',
        'delete'              => 'Entfernen',
        'id'                  => 'ID',
        'ticket_details'      => 'Ticket Übersicht',
        'message'             => 'Nachricht',
        'reply_button'        => 'Antwort abschicken', // Absenden
        'all_fields_required' => 'Alle Felder sind erforderlich.',
        'message_to_long'     => 'Deine Nachricht ist zu lang (max. :chars Zeichen).',
        'ticket_create'       => 'Ticket erstellen',
        'create_button'       => 'Erstellen',
        'reply_title'         => 'Antwort schreiben',
        'status_data'         => [
            'open'    => 'Offen',
            'closed'  => 'Geschlossen',
            'replied' => 'Beantwortet'
        ]
    ],

    'profile'                         => 'Mein Konto',
    'deposit'                         => 'Guthaben aufladen',
    'transactions'                    => 'Einzahlungen',
    'orders'                          => 'Sendungsnummern',
    'payment_methods'                 => 'Zahlungsmöglichkeiten',
    'transactions_history'            => 'Einzahlungen',
    'date'                            => 'Datum:',
    'settings'                        => 'Passwort ändern',
    'success_password_changed'        => 'Passwort erfolgreich geändert.',
    'settings_current_password_wrong' => 'Das aktuelle Passwort ist falsch.',
    'settings_change_password'        => 'Passwort ändern',
    'settings_current_password'       => 'Aktuelles Passwort',
    'settings_new_password'           => 'Neues Passwort',
    'settings_new_password_confirm'   => 'Neues Passwort bestätigen',
    'settings_newsletter_enabled'     => 'Newsletter abonnieren',
    'settings_change_jabber_id'       => 'Jabber-ID ändern',
    'settings_jabber_id'              => 'Jabber-ID',
    'settings_change_mail_address'    => 'E-Mail Adresse ändern',
    'settings_mail_address'           => 'E-Mail Adresse',
    'settings_save_submit'            => 'Ändern',
    'logout'                          => 'Abmelden',
    'name'                            => 'Anzeigename',
    'username'                        => 'Benutzername',
    'email'                           => 'E-Mail Adresse',
    'jabber_id'                       => 'Jabber-ID',
    'email_or_username'               => 'E-Mail / Benutzername',
    'cashin_btc_button'               => 'Mit Bitcoin einzahlen',
    'btc_cashin_title'                => 'Bitcoin Einzahlung',
    'btc_cashin_info'                 => 'Deine Wallet:',
    'minimum_deposit'                 => 'Mindest einzahlung',
    'wallet_copied'                   => 'Wallet kopiert!',
    'copy'                            => 'Kopieren',
    'open_in_wallet'                  => 'In Wallet öffnen',
    'i_paid_button'                   => 'Als bezahlt markieren',
    'check_again'                     => 'Nochmal prüfen',
    'deposit_btc_redeem'              => 'Gutschein einlösen',
];
