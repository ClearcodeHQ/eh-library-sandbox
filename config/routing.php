<?php

$app->put('/books/{bookId}', "app.controller:putBooksAction")->bind('put_books');
$app->get('/books', "app.controller:getBooksAction")->bind('get_books');
$app->post('/reservations', "app.controller:postReservationsAction")->bind('post_reservations');
$app->patch('/reservations/{reservationId}', "app.controller:patchReservationsAction")->bind('patch_reservations');
$app->delete('/reservations/{reservationId}', "app.controller:deleteReservationsAction")->bind('delete_reservations');
$app->get('/reservations', "app.controller:getReservationsAction")->bind('get_reservations');
