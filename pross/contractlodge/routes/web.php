<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
# Logged out home/index
Route::get('/', 'WelcomeController@show');
# Logged in home/index
Route::get('/home', 'RaceController@index');
# Update Notification settings for a user
Route::put('/settings/notification', 'UserController@update');
# Uploads (polymporphic)
Route::get('/files/{upload}/download', 'UploadController@show');
# Contacts (polymporphic)
// Route::get('/contacts/create', 'ContactController@create');
// Route::get('/contacts/{contact_id}', 'ContactController@show');
# Notes (polymporphic)
// Route::get('/notes/create', 'NoteController@create');
// Route::get('/notes/{note_id}', 'NoteController@show');
# Invoices
Route::get('/invoices/{invoice_type}/create', 'InvoiceController@create')->name('invoices.create');
Route::get('/invoices/', 'InvoiceController@index')->name('invoices.index');
# Reports
Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::get('/', 'ReportController@index')->name('index');
    Route::get('/confirmations/outstanding', 'ReportController@showOutStandingConfirmation')->name('confirmations.outstanding');
});
# Hotels
Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function () {
    # Hotel Notes
    // Route::get('/{hotel_id}/notes/create', 'HotelNoteController@create');
    // Route::get('/{hotel_id}/notes/{note_id}/edit', 'HotelNoteController@edit');
    // Route::get('/{hotel_id}/notes/{note_id}', 'HotelNoteController@show');
    // Route::get('/{hotel_id}/notes', 'HotelNoteController@index');
    # Hotel Contacts // FIXME: Temporarily commented to get the routes:list command to work
    // Route::get('/{hotel_id}/contacts/create', 'HotelContactController@create');
    // Route::get('/{hotel_id}/contacts/{contact_id}/edit', 'HotelContactController@edit');
    // Route::get('/{hotel_id}/contacts/{contact_id}', 'HotelContactController@show');
    // Route::get('/{hotel_id}/contacts', 'HotelContactController@index');
    # Hotels
    Route::get('/create', 'HotelController@create')->name('create');
    Route::get('/archived', 'HotelController@archived')->name('archived');
    Route::get('/bills', 'BillController@index')->name('bills.index');
    Route::get('/{hotel}/edit', 'HotelController@edit')->name('edit');
    Route::get('/{hotel}', 'HotelController@show')->name('show');
    Route::get('/', 'HotelController@index')->name('index');
    Route::put('/{hotel_id}/unarchive', 'HotelController@unarchive')->name('unarchive');
    Route::put('/{hotel}', 'HotelController@update')->name('update');
    Route::post('/', 'HotelController@store')->name('store');
    Route::delete('/{hotel}', 'HotelController@destroy')->name('destroy');
});
# Clients
Route::group(['prefix' => 'clients', 'as' => 'clients.'], function () {
    # Client Notes
    // Route::get('/{client_id}/notes/create', 'ClientNoteController@create');
    // Route::get('/{client_id}/notes/{note_id}/edit', 'ClientNoteController@edit');
    // Route::get('/{client_id}/notes/{note_id}', 'ClientNoteController@show');
    // Route::get('/{client_id}/notes', 'ClientNoteController@index');
    # Client Invoices (non-race)
    Route::get('/{client}/invoices/{invoice_type}/create', 'ClientInvoiceController@create')->name('invoices.create');
    Route::get('/{client}/invoices/{invoice}/edit', 'ClientInvoiceController@edit')->name('invoices.edit');
    // Route::get('/{client}/invoices/{invoice}', 'ClientInvoiceController@show')->name('invoices.show');

    Route::get('{client}/extras/{custom_invoice}', 'ClientInvoiceController@show')->name('invoices.show');
    Route::get('{client}/confirmations/{confirmation}', 'ClientInvoiceController@showConfirmation')->name('confirmations.show');

    Route::get('/{client}/invoices', 'ClientInvoiceController@index')->name('invoices.index');
    # Client Contacts // FIXME: Temporarily commented to get the routes:list command to work
    // Route::get('/{client_id}/contacts/create', 'ClientContactController@create');
    // Route::get('/{client_id}/contacts/{contact_id}/edit', 'ClientContactController@edit');
    // Route::get('/{client_id}/contacts/{contact_id}', 'ClientContactController@show');
    // Route::get('/{client_id}/contacts', 'ClientContactController@index');
    # Clients
    Route::get('/create', 'ClientController@create')->name('create');
    Route::get('/archived', 'ClientController@archived')->name('archived');
    Route::get('/{client}/edit', 'ClientController@edit')->name('edit');
    Route::get('/{client}', 'ClientController@show')->name('show');
    Route::get('/', 'ClientController@index')->name('index');
    Route::put('/{client_id}/unarchive', 'ClientController@unarchive')->name('unarchive');
    Route::put('/{client}', 'ClientController@update')->name('update');
    Route::post('/', 'ClientController@store')->name('store');
    Route::delete('/{client}', 'ClientController@destroy')->name('destroy');
});
# Races
Route::group(['prefix' => 'races', 'as' => 'races.'], function () {
    # Race Invoices
    Route::get('/{race}/invoices/{invoice_type}/create', 'RaceInvoiceController@create')->name('invoices.create');
    Route::get('/{race}/invoices/{invoice}/edit', 'RaceInvoiceController@edit')->name('invoices.edit');
    Route::get('/{race}/invoices/{invoice}', 'RaceInvoiceController@show')->name('invoices.show');
    Route::get('/{race}/invoices', 'RaceInvoiceController@index')->name('invoices.index');
    # Race Client Invoices
    Route::get('/{race}/clients/{client}/invoices/{invoice_type}/create', 'RaceClientInvoiceController@create')->name('clients.invoices.create');
    Route::get('/{race}/clients/{client}/invoices/{invoice}/edit', 'RaceClientInvoiceController@edit')->name('clients.invoices.edit');
    Route::get('/{race}/clients/{client}/invoices/{invoice}', 'RaceClientInvoiceController@show')->name('clients.invoices.show');
    // Route::get('/{race}/clients/{client}/invoices', 'RaceClientInvoiceController@index')->name('clients.invoices.index');
    # Race Hotel Invoice
    Route::get('/{race}/hotels/{hotel}/invoices/{invoice_type}/create', 'RaceHotelInvoiceController@create')->name('hotels.invoices.create');
    # Race Hotel Client Invoices
    Route::get('/{race}/hotels/{hotel}/clients/{client}/invoices/{invoice_type}/create', 'RaceHotelClientInvoiceController@create')->name('hotels.clients.invoices.create');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/invoices', 'RaceHotelClientInvoiceController@index')->name('hotels.clients.invoices.index');
    # Race Hotel Client Custom/Extras Invoices
    Route::get('/{race}/hotels/{hotel}/clients/{client}/extras/{custom_invoice}/edit', 'RaceHotelClientInvoiceController@edit')->name('hotels.clients.invoices.edit');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/extras/{custom_invoice}/send', 'RaceHotelClientInvoiceController@send')->name('hotels.clients.invoices.send');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/extras/{custom_invoice}/pdf', 'RaceHotelClientInvoiceController@pdf')->name('hotels.clients.invoices.pdf');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/extras/{custom_invoice}', 'RaceHotelClientInvoiceController@show')->name('hotels.clients.invoices.show');
    # Race Hotel Client Confirmation
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/edit', 'RaceHotelClientInvoiceController@editConfirmation')->name('hotels.clients.confirmations.edit');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/request', 'RaceHotelClientInvoiceController@requestSignature')->name('hotels.clients.confirmations.request');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/send', 'RaceHotelClientInvoiceController@sendConfirmation')->name('hotels.clients.confirmations.send');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/pdf', 'RaceHotelClientInvoiceController@pdfConfirmation')->name('hotels.clients.confirmations.pdf');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}', 'RaceHotelClientInvoiceController@showConfirmation')->name('hotels.clients.confirmations.show');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/mark-as-signed', 'RaceHotelClientInvoiceController@markAsSigned')->name('hotels.clients.confirmations.mark-as-signed');
    Route::get('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}/mark-as-unsigned', 'RaceHotelClientInvoiceController@markAsUnsigned')->name('hotels.clients.confirmations.mark-as-unsigned');
    Route::delete('/{race}/hotels/{hotel}/clients/{client}/confirmations/{confirmation}', 'RaceHotelClientInvoiceController@destroy')->name('hotels.clients.confirmations.destroy');
    # Race Hotel Clients
    Route::get('/{race}/hotels/{hotel}/clients/{client}', 'RaceHotelClientController@show')->name('hotels.clients.show'); // Includes invoices and payments
    # Race Hotel Bills
    Route::get('/{race}/hotels/{hotel}/bills', 'RaceHotelBillController@edit')->name('hotels.bills.edit'); // Entire list is editable at once, including create
    # Race Hotel Reservations (shortcut to aggregated hotel rooming list, irrespective of client)
    Route::get('/{race}/hotels/{hotel}/reservations', 'RaceHotelReservationController@index')->name('hotels.reservations'); // Entire list is editable at once, including create
    Route::get('/{race}/hotels/{hotel}/reservations/export', 'RaceHotelController@export')->name('hotels.reservations.export'); // for export/download excel
    Route::post('/{race}/hotels/{hotel}/reservations/import', 'RaceHotelController@import')->name('hotels.reservations.import'); // for import/upload excel
    # Race Notes
    // Route::get('/{race_id}/notes/create', 'RaceNoteController@create');
    // Route::get('/{race_id}/notes/{note_id}/edit', 'RaceNoteController@edit');
    // Route::get('/{race_id}/notes/{note_id}', 'RaceNoteController@show');
    // Route::get('/{race_id}/notes', 'RaceNoteController@index');
    # Race Hotels
    Route::get('/{race}/hotels/create', 'RaceHotelController@create')->name('hotels.create');
    Route::get('/{race}/hotels/search', 'RaceHotelController@search')->name('hotels.search');
    Route::get('/{race}/hotels/{hotel}/reconcile', 'RaceHotelController@reconcile')->name('hotels.reconcile');
    Route::get('/{race}/hotels/{hotel}/edit', 'RaceHotelController@edit')->name('hotels.edit'); // Edit room types, rates, and "booked" (contracted) hotel inventory
    Route::delete('/{race}/hotels/{hotel}/destroy', 'RaceHotelController@destroy')->name('hotels.destroy');

    Route::get('/{race}/hotels/{hotel}/attach', 'HotelController@attach')->name('hotels.attach');
    Route::get('/{race}/hotels/{hotel}', 'RaceHotelController@show')->name('hotels.show'); // Includes room types, inventory, and rates
    Route::put('/{race}/hotels/{hotel}', 'RaceHotelController@update')->name('hotels.update');
    Route::post('/{race}/hotels', 'HotelController@store')->name('hotels.store');
    # Races
    Route::get('/create/{year?}', 'RaceController@create')->name('create');
    Route::get('/archived', 'RaceController@archived')->name('archived');
    Route::get('/{race}/edit', 'RaceController@edit')->name('edit');
    Route::get('/{race}', 'RaceController@show')->name('show');
    Route::get('/', 'RaceController@index')->name('index');
    Route::put('/{race_id}/unarchive', 'RaceController@unarchive')->name('unarchive');
    Route::put('/{race}', 'RaceController@update')->name('update');
    Route::post('/', 'RaceController@store')->name('store');
    Route::delete('/{race}', 'RaceController@destroy')->name('destroy');
});
